<?php

namespace App\Http\Controllers\JadwalDiklat;

use App\Http\Controllers\Controller;
use App\Models\PeriodeBagianDetailInternal;
use App\Models\PeriodeUtama;
use App\Models\ProgramEksternal;
use App\Models\ProgramHlc;
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

        // 1. Internal
        $internal = PeriodeUtama::whereHas('peserta', fn($q) => $q->where('nrp', $nrp))
            ->where('tanggal', '>=', Carbon::today())
            ->when($search, fn($q) => $q->where('nama_kegiatan', 'ILIKE', "%{$search}%"))
            ->orderBy('tanggal', 'asc')->get();

        // 2. HLC
        $hlc = ProgramHlc::whereHas('hlc', function ($q) use ($nrp) {
            $q->where('nrp', $nrp)->whereDate('tanggal_mulai', '>=', Carbon::today());
        })
            ->with(['hlc' => fn($q) => $q->where('nrp', $nrp)->orderBy('tanggal_mulai', 'asc')])
            ->when($search, fn($q) => $q->where('nama_program', 'ILIKE', "%{$search}%"))
            ->get();

        // 3. Eksternal
        $eksternal = ProgramEksternal::whereHas('eksternal', function ($q) use ($nrp) {
            $q->where('nrp', $nrp)->whereDate('tanggal_mulai', '>=', Carbon::today());
        })
            ->with(['eksternal' => fn($q) => $q->where('nrp', $nrp)->orderBy('tanggal_mulai', 'asc')])
            ->when($search, fn($q) => $q->where('nama_diklat', 'ILIKE', "%{$search}%"))
            ->get();

        return Inertia::render('Jadwal/AdminInternalJadwal', [
            'jadwalInternal' => $internal,
            'jadwalHLC' => $hlc,
            'jadwalEksternal' => $eksternal,
            'filters' => ['search' => $search]
        ]);
    }
}
