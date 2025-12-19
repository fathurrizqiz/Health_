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
    public function index(Request $request, $detail_id)
    {
        $detail = DetailInternal::findOrFail($detail_id);

        $periodes = PeriodeUtama::where('detail_id', $detail_id)
            ->orderBy('tanggal')
            ->get();

        // Ambil semua bagian unik
        $bagians = Karyawans::whereNotNull('bagian')
            ->distinct()
            ->orderBy('bagian')
            ->pluck('bagian');



        // Siapkan variabel untuk karyawan yang akan ditampilkan
        $rows = PeriodeBagianDetailInternal::with('karyawan')
            ->when($request->periode_id, function ($query, $periodeId) {
                return $query->where('periode_id', $periodeId);
            })
            ->get()
            ->filter(function ($item) {
                return $item->karyawan !== null;
            })
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_karyawan' => $item->karyawan->nama_karyawan,
                    'tmt' => $item->karyawan->tmt,
                    'nrp' => $item->karyawan->nrp,
                    'bagian' => $item->karyawan->bagian,
                    'unit_kerja' => $item->karyawan->unit_kerja,
                    'posisi_jabatan' => $item->karyawan->posisi_jabatan,
                    'klinis_non_klinis' => $item->karyawan->klinis_non_klinis,
                    'jenis_kelamin' => $item->karyawan->jenis_kelamin,
                ];
            });


        return Inertia::render('RencanaDiklat/RPT/PendidikanFormal/DetailPeriode/index', [
            'detail' => $detail,
            'periodes' => $periodes,
            'rows' => $rows,
            'bagians' => $bagians,
            'selectedPeriodeId' => $request->periode_id,
            'selectedBagian' => $request->bagian ?? [],
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

        Log::info('Data tervalidasi', $validated);

        $bagianDipilih = $validated['bagian'];

        $karyawan = Karyawans::whereIn('bagian', $bagianDipilih)->get();

        Log::info('Query karyawan', [
            'bagianDipilih' => $bagianDipilih,
            'jumlah_karyawan' => $karyawan->count()
        ]);

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

                Log::info('Insert berhasil', [
                    'nrp' => $k->nrp,
                    'id' => $data->id ?? null
                ]);

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
