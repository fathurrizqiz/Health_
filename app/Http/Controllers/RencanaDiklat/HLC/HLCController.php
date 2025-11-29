<?php

namespace App\Http\Controllers\RencanaDiklat\HLC;

use App\Http\Controllers\Controller;
use App\Models\DiklatKaryawan;
use App\Models\HLCManajement;
use App\Models\Karyawans;
use App\Models\ProgramHlc;
use App\Models\RekapJamDiklat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HLCController extends Controller
{
    public function index()
    {
        $karyawan = Karyawans::all();
        $program = ProgramHlc::with('hlc.karyawan')->orderBy('tahun', 'desc')->get();


        return Inertia::render('RencanaDiklat/HLC/index', [
            'program' => $program,
            'karyawans' => $karyawan,

        ]);
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
                HLCManajement::where('nrp', $nrp)->where('status', 'approved')
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

    public function storeProgram(Request $request)
    {
        $validated = $request->validate([
            'nama_program' => 'required|string|max:255',
            'tahun' => 'required|digits:4'
        ]);

        ProgramHlc::create($validated);
        return redirect()->route('diklat.hlc.admin');
    }
    public function storeDetail(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:program_diklat_hlc,id',
            'nama_diklat' => 'required|string',
            'pengajar' => 'nullable|string',
            'penyelenggara' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'nrp' => 'nullable|string|max:255',
            'jam_diklat' => 'nullable|integer'
        ]);

        // Default approved karena admin yang input
        $validated['status'] = 'approved';

        $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
        $tanggalSelesai = Carbon::parse($validated['tanggal_selesai']);

        $selisihHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;
        $validated['jam_diklat'] = $validated['jam_diklat'] * $selisihHari;

        // create HLC
        $hlc = HLCManajement::create($validated);

        // Panggil rekap
        $this->updateRekapBulanan(
            $hlc->nrp,
            date('Y', strtotime($hlc->tanggal_mulai)),
            date('n', strtotime($hlc->tanggal_mulai))
        );

        return redirect()->route('diklat.hlc.admin');
    }
    public function edit($id)
    {
        $edit = HLCManajement::findOrFail($id);
        return Inertia::render('RencanaDiklat/HLC/edit', [
            'edit' => $edit
        ]);
    }
    public function updateDetail(Request $request, $id)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:program_diklat_hlc,id',
            'nama_diklat' => 'required|string',
            'pengajar' => 'nullable|string',
            'penyelenggara' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'nrp' => 'nullable|string|max:255',
            'jam_diklat' => 'nullable|integer'
        ]);

        $hlc = HLCManajement::findOrFail($id);

        // status tetap approved karena admin yang update
        $validated['status'] = 'approved';

        $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
        $tanggalSelesai = Carbon::parse($validated['tanggal_selesai']);
        $selisihHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        // Hitung ulang jam_diklat hanya jika dikirim
        if (isset($validated['jam_diklat'])) {
            $validated['jam_diklat'] = $validated['jam_diklat'] * $selisihHari;
        }

        $hlc->update($validated);

        // Update rekap bulanan
        $this->updateRekapBulanan(
            $hlc->nrp,
            date('Y', strtotime($hlc->tanggal_mulai)),
            date('n', strtotime($hlc->tanggal_mulai))
        );

        return redirect()->route('diklat.hlc.admin');
    }
    public function destroy($id)
    {
        $delete = HLCManajement::findOrFail($id);

        $tahun = Carbon::parse($delete->tanggal_mulai)->year;
        $bulan = Carbon::parse($delete->tanggal_mulai)->month;
        $delete->delete();

        if ($delete->status === 'approved') {
            $this->updateRekapBulanan($delete->nrp, $tahun, $bulan);
        }
        return redirect()->route('diklat.hlc.admin');
    }

}
