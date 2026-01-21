<?php

namespace App\Http\Controllers\JadwalDiklat;

use App\Http\Controllers\Controller;
use App\Models\PeriodeBagianDetailInternal;
use App\Models\PeriodeUtama;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JadwalInternalController extends Controller
{
    public function index()
    {
        $nrp= Auth::user()->nrp;
        $internal = PeriodeUtama::with('peserta')->whereHas('peserta', function($q) use ($nrp){
            $q->where('nrp', $nrp);
        })->orderBy('tanggal', 'desc')->get();

        return Inertia::render('Jadwal/AdminInternalJadwal', [
            'JadwalInternal' => $internal
        ]);
    }
}
