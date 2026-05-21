<?php

namespace App\Http\Controllers\RencanaDiklat\RPT;

use App\Http\Controllers\Controller;
use App\Models\PeriodeBagianDetailInternal;
use App\Models\PresensiDetailDiklat;
use App\Models\TemplatePembahasanSertifikat;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;
use Log;

class SertifikatController extends Controller
{
    public function template($periodeId)
    {
        $materi = TemplatePembahasanSertifikat::where('periode_id', $periodeId)
            ->pluck('materi');

        return Inertia::render(
            'RencanaDiklat/RPT/PendidikanFormal/Setifikat/templatepembahasan',
            [
                'periode_id' => $periodeId,
                'existing' => $materi
            ]
        );
    }


    public function storeTemplate(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_detail_internal,id',
            'materi' => 'required|array',
            'materi.*' => 'required|string'
        ]);

        $detailInternal = PeriodeBagianDetailInternal::where('periode_id', $request->periode_id)->first();

        // Jika tidak ketemu di tabel tersebut, coba cari di tabel relasi tempat detail_program_id berada
        $detailProgramId = $detailInternal ? $detailInternal->detail_program_id : null;

        TemplatePembahasanSertifikat::where('periode_id', $request->periode_id)->delete();

        foreach ($request->materi as $item) {
            TemplatePembahasanSertifikat::create([
                'periode_id' => $request->periode_id,
                'materi' => $item
            ]);
        }

       
        if ($detailProgramId) {
            return redirect()->route('aksi-internal', ['id' => $detailProgramId])
                ->with('success', 'Materi Berhasil Disimpan');
        }

        // Fallback jika tidak ketemu
        return redirect()->back()->with('success', 'Materi Berhasil Disimpan');
    }

    public function generate(PeriodeBagianDetailInternal $peserta)
    {
        // cek kehadiran (sesuai NRP & detail_program_id)
        if (!$peserta->pree_done_at || !$peserta->post_done_at) {
            return back()->withErrors([
                'message' => 'Gagal! Karyawan belum menyelesaikan Pre-test atau Post-test sebagai syarat kehadiran.'
            ]);
        }

        // 2. CEK KELENGKAPAN DATA PERIODE (Tetap diperlukan untuk nama diklat di sertifikat)
        if (!$peserta->periode || !$peserta->periode->detail) {
            return back()->withErrors(['message' => 'Gagal! Data periode / diklat tidak lengkap.']);
        }

        // Ambil materi untuk halaman belakang sertifikat
        $materi = TemplatePembahasanSertifikat::where('periode_id', $peserta->periode_id)
            ->get()
            ->values()
            ->map(fn($m, $i) => ['no' => $i + 1, 'materi' => $m->materi])
            ->toArray();

        // === LOAD FONTS ===
        $fonts = [];

        $scriptFont = storage_path('fonts/ScriptMTBold.ttf');
        if (file_exists($scriptFont) && is_readable($scriptFont)) {
            $fonts['ScriptMTBold'] = [
                'mime' => 'font/ttf',
                'base64' => base64_encode(file_get_contents($scriptFont)),
            ];
            // Log::info("Font ScriptMTBold dimuat. Panjang base64: " . strlen($fonts['ScriptMTBold']['base64']));
        } else {
            // Log::error("Font ScriptMTBold TIDAK DITEMUKAN atau tidak bisa dibaca: $scriptFont");
            return back()->withErrors(['message' => 'Gagal! Font ScriptMTBold tidak tersedia di server.']);
        }

        $Arial = storage_path('fonts/ARIALBOLDMT.otf');
        if (file_exists($Arial) && is_readable($Arial)) {
            $fonts['ARIALBOLDMT'] = [
                'mime' => 'font/otf',
                'base64' => base64_encode(file_get_contents($Arial)),
            ];
            // Log::info("Font ARIALBOLDMT dimuat. Panjang base64: " . strlen($fonts['ARIALBOLDMT']['base64']));
        } else {
            Log::warning("Font ARIALBOLDMT tidak ditemukan: $Arial");
        }

        // === LOAD ASSETS (GAMBAR) ===
        $assetPaths = [
            'bg1' => public_path('diklat_template/sertifikat/bg1.png'),
            'bg2' => public_path('diklat_template/sertifikat/bg2.png'),
            'logo' => public_path('diklat_template/sertifikat/logo1.png'),
            'ttd' => public_path('diklat_template/sertifikat/ttd.png'),
        ];

        $assets = [];
        foreach ($assetPaths as $key => $path) {
            if (!file_exists($path)) {
                // Log::error("ASET TIDAK DITEMUKAN: $key → $path");
                return back()->withErrors(['message' => "Gagal! Template sertifikat tidak lengkap: $key.png hilang."]);
            }

            if (!is_readable($path)) {
                // Log::error("ASET TIDAK BISA DIBACA (permission?): $key → $path");
                return back()->withErrors(['message' => "Gagal! File $key tidak bisa diakses (masalah permission)."]);
            }

            $size = filesize($path);
            if ($size === 0) {
                // Log::error("ASET KOSONG: $key → $path");
                return back()->withErrors(['message' => "Gagal! File $key rusak (ukuran 0 byte)."]);
            }

            // untuk baca file
            $content = file_get_contents($path);
            if ($content === false) {
                // Log::error("GAGAL MEMBACA FILE: $key → $path");
                return back()->withErrors(['message' => "Gagal membaca file $key."]);
            }

            $assets[$key] = base64_encode($content);
            Log::info("ASET $key dimuat. Ukuran file: $size byte, Panjang base64: " . strlen($assets[$key]));
        }

        // === GENERATE PDF ===
        try {
            $pdf = Pdf::loadView('sertifikat.sertifikat', [
                'nama' => $peserta->nama_karyawan,
                'nama_diklat' => $peserta->periode->detail->nama_diklat,
                'tanggal' => Carbon::parse($peserta->periode->tanggal)->format('d F Y'),
                'direktur' => 'Nama Direktur',
                'materi' => $materi,
                'fonts' => $fonts,
                'assets' => $assets,
            ])->setPaper('a4', 'landscape');

            $path = "sertifikat/{$peserta->nrp}_{$peserta->id}.pdf";
            Storage::disk('public')->put($path, $pdf->output());

            // Update status sertifikat
            $peserta->update([
                'sertifikat_path' => $path,
                'sertifikat_generated_at' => now(),
            ]);

            return back()->with('success', 'Sertifikat berhasil digenerate');

        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function download(PeriodeBagianDetailInternal $peserta)
    {
        if (!$peserta->sertifikat_path || !Storage::disk('public')->exists($peserta->sertifikat_path)) {
            abort(404, 'Sertifikat tidak ditemukan');
        }

        $file = Storage::disk('public')->path($peserta->sertifikat_path);
        return Response::make(file_get_contents($file), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($file) . '"'
        ]);
    }

}
