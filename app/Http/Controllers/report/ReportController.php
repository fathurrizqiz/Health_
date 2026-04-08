<?php

namespace App\Http\Controllers\report;

use App\Http\Controllers\Controller;
use App\Models\Karyawans;
use App\Models\RekapJamDiklat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Tangkap input 'months' (array). Default ke bulan sekarang jika kosong.
        $selectedMonths = $request->input('months', [now()->month]);
        $searchbagian = $request->input('bagian', null);
        $year = now()->year;

        // Pastikan selalu dalam bentuk array
        if (!is_array($selectedMonths)) {
            $selectedMonths = [$selectedMonths];
        }

        // Hitung berapa bulan yang dipilih untuk mengalikan target
        $countSelectedMonths = count($selectedMonths);

        // 1. Ambil Aktual pake whereIn()
        $queryAktual = Karyawans::join('rekap_jam_diklat', 'karyawans.nrp', '=', 'rekap_jam_diklat.nrp')
            ->whereIn('rekap_jam_diklat.bulan', $selectedMonths)
            ->where('rekap_jam_diklat.tahun', $year)
            ->select('karyawans.bagian')
            ->selectRaw('SUM(rekap_jam_diklat.total_jam) as total_aktual');

        if ($searchbagian) {
            $queryAktual->where('karyawans.bagian', 'ILIKE', '%' . $searchbagian . '%');

        }
        $aktualPerBagian = $queryAktual->groupBy('karyawans.bagian')
            ->get()
            ->pluck('total_aktual', 'bagian');
        // 2. Ambil total karyawan per bagian
        $queryTotalBagian = Karyawans::select('bagian')
            ->selectRaw('COUNT(*) as total_karyawan');

        if ($searchbagian) {
            $queryTotalBagian->where('bagian', 'ILIKE', '%' . $searchbagian . '%');
        }

        $totalPerBagian = $queryTotalBagian->groupBy('bagian')->get();

        // 3. Gabungkan Data
        $dataFinal = $totalPerBagian->map(function ($row) use ($aktualPerBagian, $countSelectedMonths) {
            $targetPerOrangTahunan = Karyawans::where('bagian', $row->bagian)
                ->join('target_jam_datamaster', 'karyawans.klinis_non_klinis', '=', 'target_jam_datamaster.kategori')
                ->avg('target_jam_datamaster.target_jam');

            // Target per bulan dikali jumlah bulan yang difilter
            $targetPerOrang = ($targetPerOrangTahunan / 12) * $countSelectedMonths;
            $targetTotal = $row->total_karyawan * $targetPerOrang;
            $aktual = $aktualPerBagian[$row->bagian] ?? 0;

            return [
                'kategori' => $row->bagian,
                'totalKaryawan' => $row->total_karyawan,
                'targetPerOrang' => round($targetPerOrang, 2),
                'totalTargetJam' => round($targetTotal, 2),
                'aktualJam' => (float) $aktual,
                'persentase' => $targetTotal > 0 ? round(($aktual / $targetTotal) * 100, 2) : 0,
            ];
        });

        // 4. Hitung Grand Total
        $targetAll = $dataFinal->sum('totalTargetJam');
        $rekapJamTerpilih = RekapJamDiklat::whereIn('bulan', $selectedMonths)
            ->where('tahun', $year)
            ->sum('total_jam');

        return Inertia::render('report/Report', [
            'filters' => [
                'months' => $selectedMonths, // Kirim array bulan kembali ke Vue
                'year' => $year,
                'bagian' => $searchbagian,
            ],
            'totalPerKategori' => $dataFinal,
            'totalJamDiklat' => (float) $rekapJamTerpilih,
            'targetAll' => round($targetAll, 2),
        ]);
    }
}
