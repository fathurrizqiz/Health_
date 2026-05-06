<?php

namespace App\Http\Controllers\no_hp;

use App\Http\Controllers\Controller;
use App\Models\Karyawans;
use App\Models\NoHpKaryawan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NohpController extends Controller
{
    public function index()
    {
        $karyawan = Karyawans::select('id', 'nama_karyawan', 'bagian')->get();
        $noHpKaryawan = NoHpKaryawan::latest()->get();
        return Inertia::render(
            'Jadwal/NoHP/index',
            [
                'karyawan' => $karyawan,
                'noHpKaryawan' => $noHpKaryawan
            ]
        );
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_wa' => 'required|string|max:20|unique:no_hp_karyawan,nomor_wa',
            'jabatan' => 'nullable|string|max:255',
        ]);

        NoHpKaryawan::create($validatedData);

        return redirect()->route('nohp.index')->with('success', 'Data nomor WA berhasil disimpan.');
    }
}
