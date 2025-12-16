<?php

namespace App\Http\Controllers\KaryawanDiklat;

use App\Http\Controllers\Controller;
use App\Models\AksiDetailInternal;
use App\Models\PeriodeBagianDetailInternal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InternalController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $karyawan = \DB::table('karyawans')
            ->where('nrp', $user->nrp)
            ->first();

        if (!$karyawan) {
            return abort(403, 'Data karyawan tidak ditemukan.');
        }

        // Ambil data peserta (PeriodeBagianDetailInternal)
        $pesertaList = PeriodeBagianDetailInternal::with([
            'periode.detail', // untuk nama_diklat, dll
            'aksi',           // untuk jam_diklat
        ])
            ->where('nrp', $user->nrp)
            ->get()
            ->map(function ($peserta) {
                return [
                    'id' => $peserta->id, // ✅ ID yang valid untuk route binding
                    'jam_diklat' => $peserta->aksi?->jam_diklat ?? 0,
                    'nama_diklat' => $peserta->periode?->detail?->nama_diklat,
                    'nama_pengajar' => $peserta->periode?->nama_pengajar ?? '-',
                    'pree_done_at' => $peserta->pree_done_at,
                    'post_done_at' => $peserta->post_done_at,
                    'sertifikat_path' => $peserta->sertifikat_path,
                ];
            });

        return Inertia::render('Diklat/Internal/index', [
            'internal' => $pesertaList,
        ]);
    }


}
