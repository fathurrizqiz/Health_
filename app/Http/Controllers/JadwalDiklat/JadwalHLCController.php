<?php

namespace App\Http\Controllers\JadwalDiklat;

use App\Http\Controllers\Controller;
use App\Models\HLCManajement;
use App\Models\ProgramHlc;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JadwalHLCController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = ProgramHlc::query();

        // search on program name
        if ($search) {
            $query->where('nama_program', 'ILIKE', "%{$search}%");
        }

        $query->whereHas('hlc', function ($q) {
            $q->where('tanggal_mulai', '>=', Carbon::today());
        });

        $query->with([
            'hlc' => function ($q) {
                $q->select('id', 'program_id', 'nama_diklat', 'tanggal_mulai', 'nrp')
                    ->where('tanggal_mulai', '>=', Carbon::today())
                    ->orderBy('tanggal_mulai', 'asc');
            }
        ]);

        // ✅ Now get the results
        $HLC = $query->orderBy('tahun', 'desc')->get();

        return Inertia::render('Jadwal/AdminHLCJadwal', [
            'HLCJadwal' => $HLC,
            'search' => $search,
        ]);
    }

}
