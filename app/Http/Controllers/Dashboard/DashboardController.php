<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Karyawans;
use App\Models\RekapJamDiklat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
{
    $karyawan = Karyawans::count();
    $year = now()->year;

    // 1. Hitung Target per Bagian
    $totalPerKategori = Karyawans::join(
        'target_jam_datamaster',
        'karyawans.klinis_non_klinis',
        '=',
        'target_jam_datamaster.kategori'
    )
        ->select('karyawans.bagian', 'target_jam_datamaster.target_jam')
        ->selectRaw('COUNT(karyawans.id) as total_karyawan')
        ->groupBy('karyawans.bagian', 'target_jam_datamaster.target_jam')
        ->get();

    // 2. Ambil Aktual dari RekapJamDiklat
    $aktualPerBagian = Karyawans::join('rekap_jam_diklat', 'karyawans.nrp', '=', 'rekap_jam_diklat.nrp')
        ->where('rekap_jam_diklat.tahun', $year)
        ->select('karyawans.bagian')
        ->selectRaw('SUM(rekap_jam_diklat.total_jam) as total_aktual')
        ->groupBy('karyawans.bagian')
        ->get()
        ->pluck('total_aktual', 'bagian');

    // 3. Gabungkan Data (Mapping)
    $dataFinal = $totalPerKategori->map(function ($row) use ($aktualPerBagian) {
        $aktual = $aktualPerBagian[$row->bagian] ?? 0;
        $target = $row->total_karyawan * $row->target_jam;
        return [
            'kategori' => $row->bagian,
            'totalKaryawan' => $row->total_karyawan,
            'targetPerOrang' => $row->target_jam,
            'totalTargetJam' => $target,
            'aktualJam' => (float)$aktual, // pastikan angka
            'persentase' => $target > 0 ? round(($aktual / $target) * 100, 2) : 0
        ];
    });

    $targetAll = $dataFinal->sum('totalTargetJam');
    $rekapJam = RekapJamDiklat::where('tahun', $year)->sum('total_jam');

    return Inertia::render('Dashboard', [
        'totalKaryawans' => $karyawan,
        'totalPerKategori' => $dataFinal, // Kirim data yang sudah di-map
        'totalJamDiklat' => [
            'tahun' => $year,
            'totalJamDiklat' => $rekapJam // Pastikan key sesuai dengan interface frontend
        ],
        'targetAll' => $targetAll,
    ]);
}
}
