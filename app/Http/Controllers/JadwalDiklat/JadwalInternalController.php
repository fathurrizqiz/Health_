<?php

namespace App\Http\Controllers\JadwalDiklat;

use App\Helpers\WhatsappHelper;
use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsappJob;
use App\Models\NoHpKaryawan;
use App\Models\PeriodeBagianDetailInternal;
use App\Models\PeriodeUtama;
use App\Models\ProgramEksternal;
use App\Models\ProgramHlc;
use App\Models\WaTemplate;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Carbon\Carbon;

class JadwalInternalController extends Controller
{

    public function index(Request $request)
    {
        $nrp = Auth::user()->nrp;
        $search = $request->input('search');

        // 1. Internal
        $internal = PeriodeUtama::whereHas('peserta', fn($q) => $q->where('nrp', $nrp))
            ->where('tanggal', '>=', Carbon::today())
            ->when($search, fn($q) => $q->where('nama_kegiatan', 'ILIKE', "%{$search}%"))
            ->orderBy('tanggal', 'asc')->get();

        // 2. HLC
        $hlc = ProgramHlc::whereHas('hlc', function ($q) use ($nrp) {
            $q->where('nrp', $nrp)->whereDate('tanggal_mulai', '>=', Carbon::today());
        })
            ->with(['hlc' => fn($q) => $q->where('nrp', $nrp)->orderBy('tanggal_mulai', 'asc')])
            ->when($search, fn($q) => $q->where('nama_program', 'ILIKE', "%{$search}%"))
            ->get();

        // 3. Eksternal
        $eksternal = ProgramEksternal::whereHas('eksternal', function ($q) use ($nrp) {
            $q->where('nrp', $nrp)->whereDate('tanggal_mulai', '>=', Carbon::today());
        })
            ->with(['eksternal' => fn($q) => $q->where('nrp', $nrp)->orderBy('tanggal_mulai', 'asc')])
            ->when($search, fn($q) => $q->where('nama_diklat', 'ILIKE', "%{$search}%"))
            ->get();

        $templates = WaTemplate::all(['id', 'nama_template', 'slug']);
        return Inertia::render('Jadwal/AdminInternalJadwal', [
            'jadwalInternal' => $internal,
            'jadwalHLC' => $hlc,
            'jadwalEksternal' => $eksternal,
            'filters' => ['search' => $search],
            'templates' => $templates
        ]);
    }

    public function sendWhatsappNotification(Request $request)
    {
        $idJadwal = $request->id;
        $slugDicari = $request->template_slug;

        $jadwal = PeriodeUtama::findOrFail($idJadwal);
        $penerima = NoHpKaryawan::all();
        $tanggal = Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y');

        foreach ($penerima as $karyawan) {
            // Panggil Job (ini instan, tidak menunggu API)
            SendWhatsappJob::dispatch(
                $karyawan->nomor_wa,
                $karyawan->nama,
                $slugDicari,
                [
                    'nama' => $karyawan->nama,
                    'judul' => $jadwal->nama_kegiatan ?? 'Diklat',
                    'tanggal' => $tanggal,
                    'lokasi' => $jadwal->lokasi ?? 'Kantor'
                ]
            );
        }

        return redirect()->back()->with('success', 'Antrean dimulai');
    }
}
