<?php

namespace App\Http\Controllers\RencanaDiklat\RPT;

use App\Http\Controllers\Controller;
use App\Models\DiklatEksternal;
use App\Models\DiklatKaryawan;
use App\Models\Karyawans;
use App\Models\ProgramEksternal;
use App\Models\RekapJamDiklat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NonFormalController extends Controller
{
    public function index()
    {
        $karyawan = Karyawans::all();
        $program = ProgramEksternal::with('eksternal.karyawan')->orderBy('tahun', 'desc')->get();
        return Inertia::render('RencanaDiklat/RPT/PendidikanNonFormal/index',[
            'karyawan' => $karyawan,
            'program' => $program
        ]);
    }
    public function storeProgram(Request $request)
    {
        $validate = $request->validate([
            'nama_diklat' => 'required|string|max:255',
            'tahun' => 'required|string|max:255',
        ]);
        ProgramEksternal::create($validate);
        return redirect()->route('Diklat.eksternal');
    }
    public function storeDetail(Request $request)
    {
        $validate = $request->validate([
            'program_id' => 'required|exists:program_diklat_eksternal,id',
            'nama_karyawan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'jam_diklat' => 'required|integer',
            'penyelenggara' => 'required|string|max:255',
            'nrp' => 'nullable|string|max:255',
        ]);

        // Default approved karena admin yang input
        $validate['status'] = 'approved';

        $tanggalMulai = Carbon::parse($validate['tanggal_mulai']);
        $tanggalSelesai = Carbon::parse($validate['tanggal_selesai']);

        $selisihHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;
        $validate['jam_diklat'] = $validate['jam_diklat'] * $selisihHari;
        
        $eksternal = DiklatEksternal::create($validate);

        // Panggil rekap
        $this->updateRekapBulanan(
            $eksternal->nrp,
            date('Y', strtotime($eksternal->tanggal_mulai)),
            date('n', strtotime($eksternal->tanggal_mulai))
        );
        return redirect()->route('Diklat.eksternal');
    }

    public function updateRekapBulanan($nrp, $tahun, $bulan)
    {
        $totalJam = (
            DiklatKaryawan::where('nrp', $nrp)->where('status', 'approved')
                ->whereYear('tanggal_mulai', $tahun)
                ->whereMonth('tanggal_mulai', $bulan)
                ->sum('jam_diklat')
        ) +
            (
                DiklatEksternal::where('nrp', $nrp)->where('status', 'approved')
                    ->whereYear('tanggal_mulai', $tahun)
                    ->whereMonth('tanggal_mulai', $bulan)
                    ->sum('jam_diklat')
            );

        RekapJamDiklat::updateOrCreate(
            [
                'nrp' => $nrp,
                'tahun' => $tahun,
                'bulan' => $bulan
            ],
            [
                'total_jam' => $totalJam
            ]
        );
    }

}
