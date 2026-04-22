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

        $countSelectedMonths = count($selectedMonths);

        // 1. Ambil Aktual Jam Diklat per Bagian
        $queryAktual = Karyawans::join('rekap_jam_diklat', 'karyawans.nrp', '=', 'rekap_jam_diklat.nrp')
            ->whereIn('rekap_jam_diklat.bulan', $selectedMonths)
            ->where('rekap_jam_diklat.tahun', $year)
            ->select('karyawans.bagian')
            ->selectRaw('SUM(rekap_jam_diklat.total_jam) as total_aktual');

        if ($searchbagian) {
            $queryAktual->where('karyawans.bagian', 'ILIKE', '%' . $searchbagian . '%');
        }

        // Menggunakan pluck agar langsung menjadi key-value: ['Nama Bagian' => Total Jam]
        $aktualPerBagian = $queryAktual->groupBy('karyawans.bagian')
            ->pluck('total_aktual', 'bagian');


        // 2. Ambil Total Karyawan & Total Target per Bagian (Tanpa Looping Database)
        // Melakukan JOIN langsung ke target jam dan melakukan SUM. 
        $queryTarget = Karyawans::leftJoin('target_jam_datamaster', 'karyawans.klinis_non_klinis', '=', 'target_jam_datamaster.kategori')
            ->select('karyawans.bagian')
            ->selectRaw('COUNT(karyawans.nrp) as total_karyawan')
            ->selectRaw('SUM(target_jam_datamaster.target_jam) as total_target_jam_dasar');

        if ($searchbagian) {
            $queryTarget->where('karyawans.bagian', 'ILIKE', '%' . $searchbagian . '%');
        }

        $targetKaryawanPerBagian = $queryTarget->groupBy('karyawans.bagian')->get();


        // 3. Gabungkan Data
        $dataFinal = $targetKaryawanPerBagian->map(function ($row) use ($aktualPerBagian, $countSelectedMonths) {

            // Jika nilai di database adalah target PER BULAN, cukup kalikan jumlah bulan yang difilter:
            // Mengikuti contoh hitungan Anda (mengalikan hasil dengan suatu pengali, misal 30):
            // Silakan sesuaikan angka '30' di bawah ini jika itu memang standar pengali instansi Anda.

            // Saya asumsikan target di DB adalah target dasar per bulan berdasarkan deskripsi Anda.
            $targetTotal = $row->total_target_jam_dasar * $countSelectedMonths;

            // Catatan: Jika pada rumus Anda "x 30" itu adalah jumlah hari, Anda bisa ganti menjadi:
            // $targetTotal = $row->total_target_jam_dasar * 30 * $countSelectedMonths;

            $aktual = $aktualPerBagian[$row->bagian] ?? 0;

            return [
                'kategori' => $row->bagian,
                'totalKaryawan' => $row->total_karyawan,
                // Rata-rata target per orang di bagian tersebut
                'targetPerOrang' => $row->total_karyawan > 0 ? round($targetTotal / $row->total_karyawan, 2) : 0,
                'totalTargetJam' => round($targetTotal, 2),
                'aktualJam' => (float) $aktual,
                'persentase' => $targetTotal > 0 ? round(($aktual / $targetTotal) * 100, 2) : 0,
            ];
        });

        // 4. Hitung Total
        $targetAll = $dataFinal->sum('totalTargetJam');
        $rekapJamTerpilih = RekapJamDiklat::whereIn('bulan', $selectedMonths)
            ->where('tahun', $year)
            ->sum('total_jam');

        return Inertia::render('report/Report', [
            'filters' => [
                'months' => $selectedMonths,
                'year' => $year,
                'bagian' => $searchbagian,
            ],
            'totalPerKategori' => $dataFinal,
            'totalJamDiklat' => (float) $rekapJamTerpilih,
            'targetAll' => round($targetAll, 2),
        ]);
    }
}
