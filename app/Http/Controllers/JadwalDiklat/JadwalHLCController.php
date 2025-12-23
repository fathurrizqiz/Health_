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
        $HLC = ProgramHlc::with([
            'hlc:id,program_id,nama_diklat,tanggal_mulai'
        ])->orderBy('tahun','desc')
            ->select('id', 'nama_program', 'tahun')
            ->get();

        return Inertia::render('Jadwal/AdminHLCJadwal', [
            'HLCJadwal' => $HLC
        ]);
    }

}
