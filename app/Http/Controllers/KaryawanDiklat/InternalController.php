<?php

namespace App\Http\Controllers\KaryawanDiklat;

use App\Http\Controllers\Controller;
use App\Models\AksiDetailInternal;
use App\Models\PeriodeBagianDetailInternal;
use App\Models\RekapJamDiklat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InternalController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $karyawan = \DB::table('karyawans')
            ->where('nrp', $user->nrp)
            ->first();

        if (!$karyawan) {
            return abort(403, 'Data karyawan tidak ditemukan.');
        }

        $search = $request->input('search');

        $query = PeriodeBagianDetailInternal::with([
            'periode.detail',
            'aksi'
        ])->where('nrp', $user->nrp);

        if ($search) {
            $query->whereHas('periode.detail', function ($q) use ($search) {
                $q->where('nama_diklat', 'ILIKE', "%{$search}%");
            })
                ->orWhereHas('periode', function ($q) use ($search) {
                    $q->where('nama_pengajar', 'ILIKE', "%{$search}%");
                });
        }

        $pesertaList = $query->get()->map(function ($peserta) {
            return [
                'id' => $peserta->id,
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
