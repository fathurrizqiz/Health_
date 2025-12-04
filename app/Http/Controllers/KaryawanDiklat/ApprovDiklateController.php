<?php

namespace App\Http\Controllers\KaryawanDiklat;

use App\Http\Controllers\Controller;
use App\Models\DiklatEksternal;
use App\Models\DiklatKaryawan;
use App\Models\HLCManajement;
use App\Models\RekapJamDiklat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApprovDiklateController extends Controller
{
    public function index()
    {
        $diklat = DiklatKaryawan::all();

        return Inertia::render('Diklat/Approve/index', [
            'diklat' => $diklat
        ]);
    }

    public function updateRekapBulanan($nrp, $tahun, $bulan)
    {
        $totalJam =(
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


    public function approve($id)
    {
        $diklat = DiklatKaryawan::findOrFail($id);

        if ($diklat->status !== 'pending') {
            return back()->with('error', 'Data sudah diproses sebelumnya.');
        }

        $diklat->update([
            'status' => 'approved'
        ]);



        $this->updateRekapBulanan(
            $diklat->nrp,
            $diklat->tanggal_mulai->format('Y'),
            $diklat->tanggal_mulai->format('n')
        );


        return back()->with('success', 'Diklat berhasil disetujui!');
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|max:255'
        ]);

        $diklat = DiklatKaryawan::findOrFail($id);

        if ($diklat->status !== 'pending') {
            return back()->with('error', 'Data sudah diproses sebelumnya.');
        }

        $diklat->update([
            'status' => 'rejected',
            'alasan_penolakan' => $validated['alasan_penolakan']
        ]);

        $this->updateRekapBulanan(
            $diklat->nrp,
            $diklat->tanggal_mulai->format('Y'),
            $diklat->tanggal_mulai->format('n')
        );


        return back()->with('success', 'Diklat telah ditolak!');
    }


}
