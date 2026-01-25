<?php

namespace App\Http\Controllers\JadwalDiklat;

use App\Http\Controllers\Controller;
use App\Models\PeriodeBagianDetailInternal;
use App\Models\PeriodeUtama;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class JadwalInternalController extends Controller
{

public function index(Request $request)
{
    $nrp = Auth::user()->nrp;
    $search = $request->input('search');

    $query = PeriodeUtama::with([
        'peserta' => function ($q) use ($nrp) {
            $q->where('nrp', $nrp);
        }
    ])
    ->whereHas('peserta', fn($q) => $q->where('nrp', $nrp))
    ->where('tanggal', '>=', Carbon::today()) 
    ->orderBy('tanggal', 'asc'); 

    // if ($search) {
    //     $query->where(function ($q) use ($search) {
    //         $q->where('nama_kegiatan', 'ILIKE', "%{$search}%")
    //           ->orWhere('nama_pengajar', 'ILIKE', "%{$search}%")
    //           ->orWhere('lokasi', 'ILIKE', "%{$search}%");
    //     });
    // }

    $internal = $query->get();

    return Inertia::render('Jadwal/AdminInternalJadwal', [
        'JadwalInternal' => $internal,
        'search' => $search,
    ]);
}
}
