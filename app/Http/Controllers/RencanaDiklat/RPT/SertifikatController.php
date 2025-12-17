<?php

namespace App\Http\Controllers\RencanaDiklat\RPT;

use App\Http\Controllers\Controller;
use App\Models\PeriodeBagianDetailInternal;
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

        TemplatePembahasanSertifikat::where('periode_id', $request->periode_id)->delete();

        foreach ($request->materi as $item) {
            TemplatePembahasanSertifikat::create([
                'periode_id' => $request->periode_id,
                'materi' => $item
            ]);
        }

        return back()->with('success', 'Pembahasan disimpan');
    }

    public function generate(PeriodeBagianDetailInternal $peserta)
    {
        if (!$peserta->pree_done_at || !$peserta->post_done_at) {
            return back()->with('error', 'Pre dan Post test belum lengkap');
        }

        if ($peserta->sertifikat_generated_at) {
            return back()->with('warning', 'Sertifikat sudah dibuat');
        }

        if (!$peserta->periode || !$peserta->periode->detail) {
            return back()->with('error', 'Data periode / diklat tidak lengkap');
        }

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
            Log::info("Font ScriptMTBold dimuat. Panjang base64: " . strlen($fonts['ScriptMTBold']['base64']));
        } else {
            Log::error("Font ScriptMTBold TIDAK DITEMUKAN atau tidak bisa dibaca: $scriptFont");
            return back()->with('error', 'Font ScriptMTBold tidak tersedia.');
        }

        $Arial = storage_path('fonts/ARIALBOLDMT.otf');
        if (file_exists($Arial) && is_readable($Arial)) {
            $fonts['ARIALBOLDMT'] = [
                'mime' => 'font/otf',
                'base64' => base64_encode(file_get_contents($Arial)),
            ];
            Log::info("Font ARIALBOLDMT dimuat. Panjang base64: " . strlen($fonts['ARIALBOLDMT']['base64']));
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
                Log::error("ASET TIDAK DITEMUKAN: $key → $path");
                return back()->with('error', "Template sertifikat tidak lengkap: $key.png hilang.");
            }

            if (!is_readable($path)) {
                Log::error("ASET TIDAK BISA DIBACA (permission?): $key → $path");
                return back()->with('error', "File $key tidak bisa diakses.");
            }

            // Opsional: cek ukuran file
            $size = filesize($path);
            if ($size === 0) {
                Log::error("ASET KOSONG: $key → $path");
                return back()->with('error', "File $key rusak (ukuran 0 byte).");
            }

            // Baca file
            $content = file_get_contents($path);
            if ($content === false) {
                Log::error("GAGAL MEMBACA FILE: $key → $path");
                return back()->with('error', "Gagal membaca file $key.");
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
            ])->setPaper('a4', 'landscape'); // lowercase 'a4'

            $path = "sertifikat/{$peserta->nrp}_{$peserta->id}.pdf";
            $pdfContent = $pdf->output();

            if (empty($pdfContent)) {
                Log::error("PDF output KOSONG untuk peserta ID: {$peserta->id}");
                return back()->with('error', 'Gagal membuat PDF (output kosong).');
            }

            Storage::disk('public')->put($path, $pdfContent);

            $peserta->update([
                'sertifikat_path' => $path,
                'sertifikat_generated_at' => now(),
            ]);

            Log::info("Sertifikat berhasil digenerate untuk: {$peserta->nama_karyawan} (ID: {$peserta->id})");

            return back()->with('success', 'Sertifikat berhasil digenerate');

        } catch (\Exception $e) {
            Log::error("GAGAL GENERATE PDF: " . $e->getMessage() . " (File: {$e->getFile()}, Line: {$e->getLine()})");
            return back()->with('error', 'Terjadi kesalahan saat membuat sertifikat: ' . $e->getMessage());
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
