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
        $internal = PeriodeUtama::with('peserta')->orderBy('tanggal','desc')->get();

        return Inertia::render('Jadwal/AdminInternalJadwal', [
            'JadwalInternal' => $internal
        ]);
    }
}
