<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterDataModels;
use App\Models\TargetJam;
use App\Models\TargetJamModels;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterDataController extends Controller
{
    public function index()
    {
        $data = MasterDataModels::orderBy('nama_karyawan')->get();

        // Hitung total seluruh karyawan
        $totalKaryawan = MasterDataModels::count();

        // Ambil semua kategori unik dari kolom klinis_non_klinis
        $kategoriList = MasterDataModels::select('klinis_non_klinis')
            ->distinct()
            ->pluck('klinis_non_klinis')
            ->toArray();

        // Buat total dinamis per kategori
        $totalsByCategory = [];
        foreach ($kategoriList as $kategori) {
            $totalsByCategory[$kategori] = MasterDataModels::where('klinis_non_klinis', $kategori)->count();
        }

        // Ambil target jam dari database, ubah jadi array key-value
        $targetJam = TargetJamModels::pluck('target_jam', 'kategori')->toArray();

        // Pastikan semua kategori punya nilai default target jam (jaga-jaga)
        foreach ($kategoriList as $kategori) {
            if (!isset($targetJam[$kategori])) {
                $targetJam[$kategori] = 0;
            }
        }

        return Inertia::render('MasterData/index', [
            'data' => $data,
            'totals' => [
                'totalKaryawan' => $totalKaryawan,
                'byCategory' => $totalsByCategory,
            ],
            'targetJam' => $targetJam,
            'kategoriList' => $kategoriList,
        ]);
    }

    public function pages()
    {
        return Inertia::render('MasterData/create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|string|max:255',
            'target_jam' => 'required|numeric'
        ]);
        TargetJamModels::create($validated);
        return redirect()->back();
    }


    public function updateTargetJam(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|string',
            'target_jam' => 'required|numeric|min:0',
        ]);

        TargetJamModels::updateOrCreate(
            ['kategori' => $validated['kategori']],
            ['target_jam' => $validated['target_jam']]
        );

        return back()->with('success', 'Target jam berhasil diperbarui.');
    }
}
