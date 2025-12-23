<?php

namespace App\Http\Controllers\JadwalDiklat;

use App\Http\Controllers\Controller;
use App\Models\DiklatEksternal;
use App\Models\ProgramEksternal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JadwalEksternalController extends Controller
{
    public function index()
    {
        $Eksternal = ProgramEksternal::with([
            'eksternal:id,program_id,tanggal_mulai'
        ])
            ->select('id', 'nama_diklat', 'tahun')->orderBy('tahun','desc')
            ->get();

        return Inertia::render('Jadwal/AdminEksternalJadwal', [
            'DiklatEksternal' => $Eksternal
        ]);
    }

}
