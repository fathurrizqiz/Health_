<?php

namespace App\Http\Controllers\JadwalDiklat;

use App\Http\Controllers\Controller;
use App\Models\HLCManajement;
use App\Models\ProgramHlc;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JadwalHLCController extends Controller
{
    public function index()
    {
        $HLC = HLCManajement::with([
            'hlc:id,program_id,nama_program,nama_diklat,tanggal_mulai,tahun'
        ])
            ->select('id', 'program_id')
            ->get();

        return Inertia::render('Jadwal/AdminHLCJadwal', [
            'HLCJadwal' => $HLC
        ]);
    }
}
