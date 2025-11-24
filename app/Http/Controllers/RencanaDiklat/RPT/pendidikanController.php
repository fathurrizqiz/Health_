<?php

namespace App\Http\Controllers\RencanaDiklat\RPT;

use App\Http\Controllers\Controller;
use App\Models\PendidikanFormalModels;
use Illuminate\Http\Request;
use Inertia\Inertia;

class pendidikanController extends Controller
{
    public function index()
    {
        $programs = PendidikanFormalModels::all();
        return Inertia::render('RencanaDiklat/RPT/PendidikanFormal/index',[
            'programs' => $programs
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_program' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'tahun' => 'nullable|string|max:255'
        ]);
        PendidikanFormalModels::create($validated);
        return redirect()->back();
    }

    public function destroy($id)
    {
        $delete = PendidikanFormalModels::findOrFail($id);
        $delete->delete();
        return redirect()->route('PF.index');
    }
}
