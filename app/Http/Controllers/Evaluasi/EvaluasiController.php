<?php

namespace App\Http\Controllers\Evaluasi;

use App\Http\Controllers\Controller;
use App\Models\DiklatKaryawan;
use App\Models\PendidikanFormalModels;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EvaluasiController extends Controller
{
    public function index()
    {
        $evaluasi1 = DiklatKaryawan::all();
        $evaluasi2 = PendidikanFormalModels::with([
            'details:id,program_id,nama_diklat',
            'details.evaluasi:id,detail_id,evaluasi'
        ])
            ->select('id', 'nama_program')
            ->get();

        return Inertia::render('Evaluasi/evaluasi',[
            'evaluasiKaryawan'=>$evaluasi1,
            'evaluasiInternal'=>$evaluasi2
        ]);
    }
}
