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

        // 2. HLC (Status Offered/Undangan)
        $hlc = ProgramHlc::whereHas('hlc', function ($q) use ($nrp) {
            $q->where('nrp', $nrp)
                ->whereIn('status', [
                    'Setuju',
                    'Hadir',
                    'approved',
                    'rejected'
                ])
                ->whereDate('tanggal_selesai', '>=', Carbon::today());
        })
            ->with([
                'hlc' => function ($q) use ($nrp) {
                    $q->where('nrp', $nrp)
                        ->whereIn('status', [
                            'Setuju',
                            'Hadir',
                            'approved',
                            'rejected'
                        ])
                        ->whereDate('tanggal_selesai', '>=', Carbon::today())
                        ->with([
                            'kehadiranHariIni'
                        ])
                        ->orderBy('tanggal_mulai', 'asc');
                }
            ])
            ->when(
                $search,
                fn($q) =>
                $q->where('nama_diklat', 'ILIKE', "%{$search}%")
            )
            ->get();

        // 3. Eksternal (Status Offered/Undangan)
        $eksternal = ProgramEksternal::whereHas('eksternal', function ($q) use ($nrp) {
            $q->where('nrp', $nrp)
                ->where('status', 'Setuju')
                ->whereDate('tanggal_selesai', '>=', Carbon::today());
        })
            ->with([
                'eksternal' => function ($q) use ($nrp) {
                    $q->where('nrp', $nrp)
                        ->where('status', 'Setuju')
                        ->whereDate('tanggal_selesai', '>=', Carbon::today())
                        ->with([
                            'kehadiranHariIni'
                        ])
                        ->orderBy('tanggal_mulai', 'asc');
                }
            ])
            ->when(
                $search,
                fn($q) =>
                $q->where('nama_diklat', 'ILIKE', "%{$search}%")
            )
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

        // 1. Ambil data jadwal utama.
        // Kita load juga relasi 'detail' untuk mengambil nama program/kegiatan secara aman.
        $jadwal = PeriodeUtama::with('detail')->findOrFail($idJadwal);

        // 2. Ambil semua NRP karyawan yang terdaftar khusus di pelatihan ini
        $daftarNrp = PeriodeBagianDetailInternal::where('periode_id', $idJadwal)
            ->pluck('nrp')
            ->toArray();

        // Antisipasi jika ternyata belum ada peserta yang didaftarkan di kelas ini
        if (empty($daftarNrp)) {
            return redirect()->back()->with('error', 'Gagal mengirim: Belum ada peserta yang terdaftar di pelatihan ini.');
        }

        // 3. Ambil data nomor HP HANYA untuk karyawan yang berada di daftar NRP kelas tersebut
        // Catatan: Pastikan kolom di tabel NoHpKaryawan Anda bernama 'nrp', jika bukan silakan sesuaikan.
        $penerima = NoHpKaryawan::whereIn('nrp', $daftarNrp)->get();

        // Antisipasi jika peserta ada, tapi tidak ada satu pun yang punya data nomor HP
        if ($penerima->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal mengirim: Tidak ada nomor WhatsApp karyawan yang terdaftar untuk pelatihan ini.');
        }

        $tanggal = Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y');

        // 4. Looping pengiriman pesan ke Job Queue
        foreach ($penerima as $karyawan) {
            SendWhatsappJob::dispatch(
                $karyawan->nomor_wa,
                $karyawan->nama,
                $slugDicari,
                [
                    'nama' => $karyawan->nama,
                    // Mengambil nama kegiatan dari relasi detail, fallback ke 'Diklat' jika null
                    'judul' => $jadwal->detail->nama_program ?? 'Diklat',
                    'tanggal' => $tanggal,
                    // Berdasarkan fillable model PeriodeUtama Anda, kolomnya adalah 'tempat'
                    'lokasi' => $jadwal->tempat ?? 'Kantor'
                ]
            );
        }

        return redirect()->back()->with('success', 'Antrean notifikasi untuk ' . count($penerima) . ' peserta berhasil dimulai.');
    }

    public function history(Request $request)
    {
        $nrp = Auth::user()->nrp;
        $search = $request->input('search');

        // 1. Internal
        $internal = PeriodeUtama::whereHas('peserta', fn($q) => $q->where('nrp', $nrp))
            ->where('tanggal', '<', Carbon::today())
            ->when($search, fn($q) => $q->where('nama_kegiatan', 'ILIKE', "%{$search}%"))
            ->orderBy('tanggal', 'desc')->get();

        // 2. HLC (Status Offered/Undangan)
        $hlc = ProgramHlc::whereHas('hlc', function ($q) use ($nrp) {
            $q->where('nrp', $nrp)
                ->where('status', 'approved') // Filter status sebelum pending
                ->whereDate('tanggal_mulai', '<', Carbon::today());
        })
            ->with([
                'hlc' => function ($q) use ($nrp) {
                    $q->where('nrp', $nrp)
                        ->where('status', 'approved') // Pastikan detail yang dimuat juga status offered
                        ->orderBy('tanggal_mulai', 'desc');
                }
            ])
            ->when($search, fn($q) => $q->where('nama_program', 'ILIKE', "%{$search}%"))
            ->get();

        // 3. Eksternal (Status Offered/Undangan)
        $eksternal = ProgramEksternal::whereHas('eksternal', function ($q) use ($nrp) {
            $q->where('nrp', $nrp)
                ->where('status', 'approved') // Filter status sebelum pending
                ->whereDate('tanggal_mulai', '<', Carbon::today());
        })
            ->with([
                'eksternal' => function ($q) use ($nrp) {
                    $q->where('nrp', $nrp)
                        ->where('status', 'approved') // Pastikan detail yang dimuat juga status offered
                        ->orderBy('tanggal_mulai', 'desc');
                }
            ])
            ->when($search, fn($q) => $q->where('nama_diklat', 'ILIKE', "%{$search}%"))
            ->get();
        return Inertia::render('Jadwal/History/Historyjadwal', [
            'jadwalInternal' => $internal,
            'jadwalHLC' => $hlc,
            'jadwalEksternal' => $eksternal,
            'filters' => ['search' => $search],
        ]);

    }
}
