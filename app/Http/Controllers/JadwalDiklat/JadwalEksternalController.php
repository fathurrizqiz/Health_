<?php

namespace App\Http\Controllers\JadwalDiklat;

use App\Http\Controllers\Controller;
use App\Models\DiklatEksternal;
use App\Models\ProgramEksternal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use function Laravel\Prompts\select;

class JadwalEksternalController extends Controller
{
    public function index()
    {
        $Eksternal = ProgramEksternal::query()
            ->whereHas('eksternal', function ($q) {
                $q->where('tanggal_mulai', '>=', Carbon::today());
            })

            ->with([
                'eksternal' => function ($q) {
                    $q->select('id', 'program_id', 'nama_diklat', 'tanggal_mulai', 'nrp', 'nama_karyawan')
                        ->whereDate('tanggal_mulai', '>=', Carbon::today())
                        ->orderBy('tanggal_mulai', 'asc');
                }
            ])
            ->select('id', 'nama_diklat', 'tahun')->orderBy('tahun', 'desc')
            ->get();



        return Inertia::render('Jadwal/AdminEksternalJadwal', [
            'DiklatEksternal' => $Eksternal,
            'today' => $Eksternal
        ]);
    }

}
