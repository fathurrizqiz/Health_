<?php

namespace App\Jobs;

use App\Models\PeriodeBagianDetailInternal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $pesertaId)
    {
    }

    public function handle()
    {
        $peserta = PeriodeBagianDetailInternal::find($this->pesertaId);

        if (!$peserta) {
            return;
        }

        // 🔐 pengaman dobel
        if ($peserta->sertifikat_generated_at) {
            return;
        }

        $pdf = Pdf::loadView('sertifikat.template', [
            'nama' => $peserta->nama_karyawan,
            'bagian' => $peserta->bagian,
            'program' => $peserta->detailProgram->nama_program,
            'tanggal' => now()->format('d F Y'),
        ])->setPaper('A4', 'landscape');

        $path = 'sertifikat/' . $peserta->nrp . '_' . $peserta->id . '.pdf';

        Storage::disk('public')->put($path, $pdf->output());

        $peserta->update([
            'sertifikat_path' => $path,
            'sertifikat_generated_at' => now(),
        ]);
    }
}

