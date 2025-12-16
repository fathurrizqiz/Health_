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

        $fonts = [];

        // Muat ScriptMTBold
        $scriptFont = storage_path('fonts/ScriptMTBold.ttf');
        if (file_exists($scriptFont)) {
            $fonts['ScriptMTBold'] = [
                'mime' => 'font/ttf',
                'base64' => base64_encode(file_get_contents($scriptFont)),
            ];
        } else {
            \Log::warning('ScriptMTBold.ttf tidak ditemukan di storage/fonts/');
        }

        $Arial = storage_path('fonts/ARIALBOLDMT.otf');
        if (file_exists($Arial)) {
            $fonts['ARIALBOLDMT'] = [
                'mime' => 'font/otf',
                'base64' => base64_encode(file_get_contents($Arial)),
            ];
        }

        // Optional: muat font lain
        $roundedFont = storage_path('fonts/ARIALROUNDEDMT.TTF');
        if (file_exists($roundedFont)) {
            $fonts['ArialRounded'] = [
                'mime' => 'font/ttf',
                'base64' => base64_encode(file_get_contents($roundedFont)),
            ];
        }

        $pdf = Pdf::loadView('sertifikat.sertifikat', [
            'nama' => $peserta->nama_karyawan,
            'nama_diklat' => $peserta->periode->detail->nama_diklat,
            'tanggal' => Carbon::parse($peserta->periode->tanggal)->format('d F Y'),
            'direktur' => 'Nama Direktur',
            'materi' => $materi,
            'fonts' => $fonts,
        ])->setPaper('A4', 'landscape');

        $path = "sertifikat/{$peserta->nrp}_{$peserta->id}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        $peserta->update([
            'sertifikat_path' => $path,
            'sertifikat_generated_at' => now(),
        ]);

        return back()->with('success', 'Sertifikat berhasil digenerate');
    }

    // Tambahkan fungsi baru untuk download
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
