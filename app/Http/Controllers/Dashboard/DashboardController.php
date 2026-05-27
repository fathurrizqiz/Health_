<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Karyawans;
use App\Models\RekapJamDiklat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $karyawan = Karyawans::count();
        $year = now()->year;

        // Aktual per bagian
        $aktualPerBagian = Karyawans::when($search, function ($query, $search) {
            $query->where('bagian', 'ILIKE', "%{$search}%");
        })
            ->join('rekap_jam_diklat', 'karyawans.nrp', '=', 'rekap_jam_diklat.nrp')
            ->where('rekap_jam_diklat.tahun', $year)
            ->select('karyawans.bagian')
            ->selectRaw('SUM(rekap_jam_diklat.total_jam) as total_aktual')
            ->groupBy('karyawans.bagian')
            ->get()
            ->pluck('total_aktual', 'bagian');

        // Total per bagian
        $totalPerBagian = Karyawans::when($search, function ($query, $search) {
            $query->where('bagian', 'ILIKE', "%{$search}%");
        })
            ->select('bagian')
            ->selectRaw('COUNT(*) as total_karyawan')
            ->groupBy('bagian')
            ->get();

        $dataFinal = $totalPerBagian->map(function ($row) use ($aktualPerBagian) {
            $targetPerOrang = Karyawans::where('bagian', $row->bagian)
                ->join(
                    'target_jam_datamaster',
                    'karyawans.klinis_non_klinis',
                    '=',
                    'target_jam_datamaster.kategori'
                )
                ->avg('target_jam_datamaster.target_jam') ?? 0;

            $targetTotal = $row->total_karyawan * $targetPerOrang * 12;
            $aktual = $aktualPerBagian[$row->bagian] ?? 0;

            return [
                'kategori'          => $row->bagian,
                'totalKaryawan'     => $row->total_karyawan,
                'targetPerOrang'    => round($targetPerOrang, 2),
                'totalTargetJam'    => $targetTotal,
                'aktualJam'         => (float) $aktual,
                'persentase'        => $targetTotal > 0
                    ? round(($aktual / $targetTotal) * 100, 2)
                    : 0,
            ];
        })->sortByDesc('persentase')->values();

        $targetAll   = $dataFinal->sum('totalTargetJam');
        $rekapJam    = RekapJamDiklat::where('tahun', $year)->sum('total_jam');

        // === Metrik Tambahan ===
        $persentaseOverall  = $targetAll > 0 ? round(($rekapJam / $targetAll) * 100, 2) : 0;
        $bulanSekarang      = now()->month;
        $namaBulan          = now()->locale('id')->monthName;
        $expectedProgress   = round(($bulanSekarang / 12) * 100, 1);
        $paceStatus         = $persentaseOverall >= $expectedProgress ? 'on_track' : 'behind';

        $bagianTercapai     = $dataFinal->filter(fn ($item) => $item['persentase'] >= 100)->count();
        $bagianBelumTercapai = $dataFinal->filter(fn ($item) => $item['persentase'] < 100)->count();
        $rataRataPencapaian = $dataFinal->count() > 0 ? round($dataFinal->avg('persentase'), 2) : 0;

        $topPerformer    = $dataFinal->isNotEmpty() ? $dataFinal->first() : null;
        $lowestPerformer = $dataFinal->isNotEmpty() ? $dataFinal->last() : null;

        // === DATA TREN BULANAN (BARU) ===
        $trenBulananRaw = RekapJamDiklat::where('tahun', $year)
            ->select('bulan')
            ->selectRaw('SUM(total_jam) as total_aktual')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total_aktual', 'bulan');

        // Hitung target kumulatif per bulan (asumsi target merata per bulan)
        $targetPerBulan = $targetAll / 12;
        $trenBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            // Jika bulan belum lewat, target tetap ditampilkan tapi aktual 0
            $aktualBulan = $trenBulananRaw[$i] ?? 0;
            $targetKumulatif = round($targetPerBulan * $i);
            $aktualKumulatif = 0;
            
            // Hitung aktual kumulatif sampai bulan ke-i
            for ($j = 1; $j <= $i; $j++) {
                $aktualKumulatif += $trenBulananRaw[$j] ?? 0;
            }

            $trenBulanan[] = [
                'bulan' => $i,
                'namaBulan' => \Carbon\Carbon::create()->month($i)->locale('id')->monthName,
                'aktualBulan' => (float) $aktualBulan,
                'targetKumulatif' => $targetKumulatif,
                'aktualKumulatif' => (float) $aktualKumulatif,
            ];
        }

        // === TOP 5 BAGIAN UNTUK GRAFIK PERBANDINGAN (BARU) ===
        $top5Bagian = $dataFinal->take(5)->pluck('kategori')->toArray();
        
        $trenPerTopBagian = [];
        if (count($top5Bagian) > 0) {
            $trenPerBagianRaw = Karyawans::whereIn('karyawans.bagian', $top5Bagian)
                ->join('rekap_jam_diklat', 'karyawans.nrp', '=', 'rekap_jam_diklat.nrp')
                ->where('rekap_jam_diklat.tahun', $year)
                ->select('karyawans.bagian', 'rekap_jam_diklat.bulan')
                ->selectRaw('SUM(rekap_jam_diklat.total_jam) as total_aktual')
                ->groupBy('karyawans.bagian', 'rekap_jam_diklat.bulan')
                ->get();

            foreach ($top5Bagian as $bagian) {
                $dataBulan = [];
                for ($i = 1; $i <= 12; $i++) {
                    $val = $trenPerBagianRaw->first(fn ($q) => $q->bagian === $bagian && $q->bulan === $i);
                    $dataBulan[] = $val ? (float) $val->total_aktual : 0;
                }
                $trenPerTopBagian[] = [
                    'namaBagian' => $bagian,
                    'data' => $dataBulan
                ];
            }
        }

        return Inertia::render('Dashboard', [
            'totalKaryawans'      => $karyawan,
            'totalPerKategori'    => $dataFinal,
            'totalJamDiklat'      => [
                'tahun'         => $year,
                'totalJamDiklat' => $rekapJam,
            ],
            'targetAll'            => $targetAll,
            'persentaseOverall'    => $persentaseOverall,
            'bulanSekarang'        => $bulanSekarang,
            'namaBulan'            => $namaBulan,
            'expectedProgress'     => $expectedProgress,
            'paceStatus'           => $paceStatus,
            'bagianTercapai'       => $bagianTercapai,
            'bagianBelumTercapai'  => $bagianBelumTercapai,
            'rataRataPencapaian'   => $rataRataPencapaian,
            'topPerformer'         => $topPerformer,
            'lowestPerformer'      => $lowestPerformer,
            'trenBulanan'          => $trenBulanan, // Dikirim ke chart
            'trenPerTopBagian'     => $trenPerTopBagian, // Dikirim ke chart
            'filters'              => [
                'search' => $search,
            ],
        ]);
    }
}