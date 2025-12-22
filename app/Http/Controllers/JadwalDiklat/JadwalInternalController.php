<?php

namespace App\Http\Controllers\JadwalDiklat;

use App\Http\Controllers\Controller;
use App\Models\PeriodeBagianDetailInternal;
use App\Models\PeriodeUtama;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JadwalInternalController extends Controller
{
    public function index()
    {
        $internal = PeriodeBagianDetailInternal::with([
            'periode:id,tanggal,nama_pengajar',
        ])
            ->select(
                'id',
                'periode_id',
                'nrp',
                'nama_karyawan'
            )
            ->get();

        return Inertia::render('Jadwal/AdminInternalJadwal', [
            'JadwalInternal' => $internal
        ]);
    }

}
