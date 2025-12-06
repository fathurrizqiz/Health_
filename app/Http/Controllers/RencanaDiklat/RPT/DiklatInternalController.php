<?php

namespace App\Http\Controllers\RencanaDiklat\RPT;

use App\Http\Controllers\Controller;
use App\Models\DetailInternal;
use App\Models\PendidikanFormalModels;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiklatInternalController extends Controller
{
    public function index()
    {
        $programs = PendidikanFormalModels::all();
        $detail = DetailInternal::all();
        return Inertia::render('RencanaDiklat/RPT/PendidikanFormal/index', [
            'programs' => $programs,
            'details' => $detail
        ]);
    }

    public function storeProgram(Request $request)
    {
        $validated = $request->validate([
            'nama_program' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'tahun' => 'nullable|string|max:255'
        ]);
        PendidikanFormalModels::create($validated);
        return redirect()->back();
    }

    public function storeDetail(Request $request)
    {
        $validate = $request->validate([
            'program_id' => 'required|exists:program_internal,id',
            'nama_diklat' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255',
            'pengajar' => 'required|string|max:255',
        ]);
        DetailInternal::create($validate);
        return redirect()->back();
    }

    public function destroy($id)
    {
        $delete = PendidikanFormalModels::findOrFail($id);
        $delete->delete();
        return redirect()->route('PF.index');
    }
}
