<?php

namespace App\Http\Controllers\Persetujuan;

use App\Http\Controllers\Controller;
use App\Models\DiklatEksternal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EksternalAdminController extends Controller
{
    public function Persetujuan()
    {
        return Inertia::render('Diklat/Persetujuan/EksternalAdmin/Index');
    }

    public function EksternalAdmin()
    {
        $eksternal = DiklatEksternal::with(['karyawan', 'kehadiran'])
            ->where('status', 'Hadir')
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->total_hadir = $item->kehadiran->where('status', 'Hadir')->count();
                $item->total_tidak_hadir = $item->kehadiran->where('status', '!=', 'Hadir')->count();
                $item->is_today_absent = $item->kehadiranHariIni ? $item->kehadiranHariIni->status : null;
                return $item;
            });

        return Inertia::render('Diklat/Persetujuan/EksternalAdmin/Eksternal', [
            'eksternal' => $eksternal
        ]);
    }
}
