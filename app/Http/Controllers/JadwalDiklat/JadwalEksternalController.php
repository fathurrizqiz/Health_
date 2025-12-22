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
        $Eksternal = DiklatEksternal::with([
            'eksternal:id, nama_diklat, tahun, program_id, tanggal_mulai',
            
        ])->select('id', 'program_id')->get();
        return Inertia::render('Jadwal/AdminEksternalJadwal',[
            'DiklatEksternal'=>$Eksternal
        ]);
    }
}
