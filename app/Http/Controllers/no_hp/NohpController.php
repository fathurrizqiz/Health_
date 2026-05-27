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
        $karyawan = Karyawans::select('id', 'nama_karyawan', 'bagian','nrp')->get();
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
            'nrp' => 'nullable|string|max:255',
        ]);

        NoHpKaryawan::create($validatedData);

        return redirect()->route('nohp.index')->with('success', 'Data nomor WA berhasil disimpan.');
    }

    public function destroy($id)
    {
        $noHpKaryawan = NoHpKaryawan::findOrFail($id);
        $noHpKaryawan->delete();

        return redirect()->route('nohp.index')->with('success', 'Data nomor WA berhasil dihapus.');
    }

    public function userrequest()
    {
        // Ambil data user yang sedang login
        $user = auth()->user();

        return Inertia::render('Jadwal/NoHP/userinput', [
            'auth_user' => [
                'nama' => $user->name, 
                'bagian' => $user->role ?? 'karyawan', 
            ]
        ]); 
    }

    public function storeuser(Request $request)
    {
        // Validasi input nomor WA
        $request->validate([
            'nomor_wa' => 'required|numeric|min:9',
        ]);

   
        NoHpKaryawan::create([
            'nama' => $request->nama,
            'nomor_wa' => $request->nomor_wa,
            'bagian' => $request->bagian,
        ]);

        
        return redirect()->back()->with('message', 'Nomor HP berhasil disimpan!');
    }
}
