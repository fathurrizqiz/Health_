<?php

namespace App\Http\Controllers\RencanaDiklat\HLC;

use App\Http\Controllers\Controller;
use App\Models\DiklatEksternal;
use App\Models\DiklatKaryawan;
use App\Models\HLCManajement;
use App\Models\Karyawans;
use App\Models\ProgramHlc;
use App\Models\RekapJamDiklat;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HLCController extends Controller
{
    public function index()
    {
        $karyawan = Karyawans::all();
        $program = ProgramHlc::with('hlc.karyawan')->orderBy('tahun', 'desc')->get();


        return Inertia::render('RencanaDiklat/HLC/index', [
            'program' => $program,
            'karyawans' => $karyawan,

        ]);
    }

    public function updateRekapBulanan($nrp, $tahun, $bulan)
    {
        // Log::info('Memanggil updateRekapBulanan well', compact('nrp', 'tahun', 'bulan'));
        // 1. Diklat Karyawan (eksternal lama)
        $jamDiklatKaryawan = DiklatKaryawan::where('nrp', $nrp)
            ->where('status', 'approved')
            ->whereYear('tanggal_mulai', $tahun)
            ->whereMonth('tanggal_mulai', $bulan)
            ->sum('jam_diklat');

        // 2. HLC
        $jamHLC = HLCManajement::where('nrp', $nrp)
            ->where('status', 'approved')
            ->whereYear('tanggal_mulai', $tahun)
            ->whereMonth('tanggal_mulai', $bulan)
            ->sum('jam_diklat');

        // 3. Diklat Internal: JOIN ketiga tabel
        $jamInternal = DB::table('periode_bagian_detail_internal as p')
            ->join('periode_detail_internal as periode', 'p.periode_id', '=', 'periode.id')
            ->join('aksi_detail_internal as aksi', 'aksi.periode_id', '=', 'periode.id')
            ->where('p.nrp', $nrp)
            ->whereNotNull('p.post_done_at')
            ->whereYear('periode.tanggal', $tahun)
            ->whereMonth('periode.tanggal', $bulan)
            ->sum('aksi.jam_diklat');

        $jamDiklatEksternal = DiklatEksternal::where('nrp', $nrp)
            ->where('status', 'approved')
            ->whereYear('tanggal_mulai', $tahun)
            ->whereMonth('tanggal_mulai', $bulan)
            ->sum('jam_diklat');


        $totalJam = $jamDiklatKaryawan + $jamHLC + $jamInternal + $jamDiklatEksternal;

        RekapJamDiklat::updateOrCreate(
            [
                'nrp' => $nrp,
                'tahun' => $tahun,
                'bulan' => $bulan
            ],
            [
                'total_jam' => $totalJam
            ]
        );
    }

    public function storeProgram(Request $request)
    {
        $validated = $request->validate([
            'nama_program' => 'required|string|max:255',
            'tahun' => 'required|digits:4'
        ]);

        ProgramHlc::create($validated);
        return redirect()->route('diklat.hlc.admin');
    }
    public function storeDetail(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:program_diklat_hlc,id',
            'nama_diklat' => 'required|string',
            'pengajar' => 'nullable|string',
            'penyelenggara' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'nrp' => 'nullable|string|max:255',
            'jam_diklat' => 'nullable|integer'
        ]);

        // Default approved karena admin yang input
        $validated['status'] = 'approved';

        $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
        $tanggalSelesai = Carbon::parse($validated['tanggal_selesai']);

        $selisihHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;
        $validated['jam_diklat'] = $validated['jam_diklat'] * $selisihHari;

        // create HLC
        $hlc = HLCManajement::create($validated);

        // Panggil rekap
        $this->updateRekapBulanan(
            $hlc->nrp,
            date('Y', strtotime($hlc->tanggal_mulai)),
            date('n', strtotime($hlc->tanggal_mulai))
        );

        return redirect()->route('diklat.hlc.admin');
    }
    public function updateProgram(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_program' => 'required|string|max:255',
            'tahun' => 'required|digits:4'
        ]);

        ProgramHlc::where('id', $id)->update($validated);
        return redirect()->back()->with('success', 'Program diperbarui');
    }


    public function destroyProgram($id)
    {
        $delete = ProgramHlc::findOrFail($id);
        $delete->delete();

        return redirect()->route('diklat.hlc.admin');
    }

    public function updateDetail(Request $request, $id)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:program_diklat_hlc,id',
            'nama_diklat' => 'required|string',
            'pengajar' => 'nullable|string',
            'penyelenggara' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'nrp' => 'nullable|string|max:255',
            'jam_diklat' => 'nullable|integer' // Ini adalah jam per hari dari input
        ]);

        $hlc = HLCManajement::findOrFail($id);

        // Hitung ulang total jam diklat (Jam per hari * Selisih hari)
        $tanggalMulai = \Carbon\Carbon::parse($validated['tanggal_mulai']);
        $tanggalSelesai = \Carbon\Carbon::parse($validated['tanggal_selesai']);

        $selisihHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        // Total jam = (input jam per hari) * jumlah hari
        $validated['jam_diklat'] = ($validated['jam_diklat'] ?? 0) * $selisihHari;

        $hlc->update($validated);

        // Update rekap bulanan untuk peserta tersebut
        $this->updateRekapBulanan(
            $hlc->nrp,
            $tanggalMulai->format('Y'),
            $tanggalMulai->format('n')
        );

        return redirect()->route('diklat.hlc.admin')->with('success', 'Detail diklat berhasil diperbarui.');
    }

    public function destroyDetail($id)
    {
        $hlc = HLCManajement::findOrFail($id);
        $nrp = $hlc->nrp;
        $tahun = date('Y', strtotime($hlc->tanggal_mulai));
        $bulan = date('n', strtotime($hlc->tanggal_mulai));

        $hlc->delete();

        // Jalankan rekap ulang setelah hapus agar total jam di rekap berkurang
        $this->updateRekapBulanan($nrp, $tahun, $bulan);

        return redirect()->route('diklat.hlc.admin')->with('success', 'Data diklat berhasil dihapus.');
    }

}
