<?php

namespace App\Http\Controllers\RencanaDiklat\RPT;

use App\Http\Controllers\Controller;
use App\Models\DetailInternal;
use App\Models\Karyawans;
use App\Models\PeriodeBagianDetailInternal;
use App\Models\PeriodeUtama;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Log;

class DetailPeriodeController extends Controller
{
    public function index($id)
    {
        $detail = DetailInternal::findOrFail($id);
        $karyawan = Karyawans::all();
        $periode = PeriodeBagianDetailInternal::all();
        $utama = PeriodeUtama::where('detail_id', $id)->first();
        return Inertia::render('RencanaDiklat/RPT/PendidikanFormal/DetailPeriode/index', [
            'detail' => $detail,
            'karyawan' => $karyawan,
            'periode' => $periode,
            'utama'=>$utama
        ]);
    }

    public function store(Request $request)
    {
        // Log::info('STORE PeriodeBagianDetailInternal - Request masuk', $request->all());

        $validated = $request->validate([
            'bagian' => 'required|array',
            'bagian.*' => 'string',
            'detail_program_id' => 'required|integer',
            'periode_id' => 'required|integer'
        ]);

        // Log::info('Data tervalidasi', $validated);

        $bagianDipilih = $validated['bagian'];

        $karyawan = Karyawans::whereIn('bagian', $bagianDipilih)->get();

        // Log::info('Query karyawan', [
        //     'bagianDipilih' => $bagianDipilih,
        //     'jumlah_karyawan' => $karyawan->count()
        // ]);

        foreach ($karyawan as $k) {
            try {
                $data = PeriodeBagianDetailInternal::create([
                    'detail_program_id' => $request->detail_program_id,
                    'periode_id' => $request->periode_id,
                    'nama_karyawan' => $k->nama_karyawan,
                    'tmt' => $k->tmt,
                    'nrp' => $k->nrp,
                    'bagian' => $k->bagian,
                    'unit_kerja' => $k->unit_kerja,
                    'posisi_jabatan' => $k->posisi_jabatan,
                    'klinis_non_klinis' => $k->klinis_non_klinis,
                    'jenis_kelamin' => $k->jenis_kelamin,
                ]);

                // Log::info('Insert berhasil', [
                //     'nrp' => $k->nrp,
                //     'id' => $data->id ?? null
                // ]);

            } catch (\Exception $e) {
                Log::error('Insert gagal', [
                    'nrp' => $k->nrp,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Log::info('STORE selesai');

        return back()->with('success', 'Data berhasil disimpan');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || count($ids) === 0) {
            return back()->withErrors('Tidak ada data yang dipilih');
        }

        Log::info('Bulk delete PeriodeBagianDetailInternal', ['ids' => $ids]);

        PeriodeBagianDetailInternal::whereIn('id', $ids)->delete();

        return back()->with('success', 'Data terpilih berhasil dihapus!');
    }

}
