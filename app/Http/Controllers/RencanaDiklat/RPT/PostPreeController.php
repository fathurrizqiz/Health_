<?php

namespace App\Http\Controllers\RencanaDiklat\RPT;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateCertificateJob;
use App\Models\AksiDetailInternal;
use App\Models\DetailInternal;
use App\Models\DiklatKaryawan;
use App\Models\EvaluasiDetailInternal;
use App\Models\HLCManajement;
use App\Models\PeriodeBagianDetailInternal;
use App\Models\PeriodeUtama;
use App\Models\PostPreeDetailInternal;
use App\Models\QuestionChoices;
use App\Models\QuestionTestDetailInternal;
use App\Models\RekapJamDiklat;
use App\Models\TemplatePembahasanSertifikat;
use App\Models\TestToken;
use App\Models\UserAnswerPostPreeDetail;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Log;
use Str;

class PostPreeController extends Controller
{


    public function preTest($detailId)
    {
        $tests = PostPreeDetailInternal::with('questions.choices')
            ->where('detail_program_id', $detailId)
            ->where('type', 'pree')
            ->first();

        return Inertia::render('RencanaDiklat/RPT/PendidikanFormal/PrePostTest/PreTest', [
            'detail_id' => $detailId,
            'test' => $tests
        ]);
    }

    public function postTest($detailId)
    {
        $tests = PostPreeDetailInternal::with('questions.choices')
            ->where('detail_program_id', $detailId)
            ->where('type', 'post')
            ->first();

        return Inertia::render('RencanaDiklat/RPT/PendidikanFormal/PrePostTest/Postest', [
            'detail_id' => $detailId,
            'test' => $tests
        ]);
    }

    public function savePre(Request $request)
    {
        return $this->saveQuestions($request, 'pree');
    }

    public function savePost(Request $request)
    {
        return $this->saveQuestions($request, 'post');
    }



    public function saveQuestions(Request $request, $type)
    {
        \Log::info("saveQuestions called", ['type' => $type, 'payload' => $request->all()]);

        $request->validate([
            'detail_id' => 'required|integer|exists:detail_internal,id',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.choices' => 'required|array|min:2',
            'questions.*.choices.*.text' => 'required|string',
            'questions.*.choices.*.is_correct' => 'boolean'
        ]);

        // --- TEMUKAN ATAU BUAT TEST ---
        $test = PostPreeDetailInternal::firstOrCreate([
            'detail_program_id' => $request->detail_id,
            'type' => $type,
        ]);
        \Log::info("Test record", ['test_id' => $test->id]);

        // Hapus semua question lama agar bersih
        $deleted = QuestionTestDetailInternal::where('test_id', $test->id)->delete();
        \Log::info("Deleted old questions", ['count' => $deleted]);

        $questions = $request->questions;
        $totalQuestions = count($questions);
        $bobotPerSoal = $totalQuestions > 0 ? 100.0 / $totalQuestions : 0;

        foreach ($request->questions as $qIndex => $q) {
            \Log::info("Saving question", ['index' => $qIndex, 'question' => $q['text']]);

            $question = QuestionTestDetailInternal::create([
                'test_id' => $test->id,
                'pertanyaan' => $q['text'],
                'tipe' => 'multiple_choice',
                'bobot' => round($bobotPerSoal, 2),
            ]);

            if (!$question) {
                \Log::error("Failed to create question", ['question' => $q['text']]);
                continue;
            }

            foreach ($q['choices'] as $cIndex => $c) {
                \Log::info("Saving choice", ['question_id' => $question->id, 'index' => $cIndex, 'choice' => $c]);

                $choice = QuestionChoices::create([
                    'question_id' => $question->id,
                    'text' => $c['text'],
                    'is_correct' => isset($c['is_correct']) ? (bool) $c['is_correct'] : false,
                ]);

                if (!$choice) {
                    \Log::error("Failed to create choice", ['choice' => $c]);
                }
            }
        }

        \Log::info("saveQuestions finished successfully", ['test_id' => $test->id]);

        return back()->with('success', 'Test berhasil disimpan!');
    }


    // jawab pertanyaan (user)
    public function showTest($type, $detailId)
    {
        $test = PostPreeDetailInternal::with('questions.choices')
            ->where('detail_program_id', $detailId)
            ->where('type', $type)
            ->firstOrFail();

        return Inertia::render('RencanaDiklat/RPT/PendidikanFormal/PrePostTest/userview', [
            'test' => $test,
            'type' => $type,
            'detail_id' => $detailId,
            'user_nrp' => auth()->user()->nrp ?? null
        ]);
    }

    public function submitTest(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'type' => 'required|in:pree,post',
            'detail_id' => 'required|integer|exists:detail_internal,id',
            'nrp' => 'required',
        ]);

        $exists = UserAnswerPostPreeDetail::where('nrp', $request->nrp)
            ->whereIn('question_id', array_keys($request->answers))
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah mengerjakan test ini.');
        }


        foreach ($request->answers as $questionId => $choiceId) {

            $choice = QuestionChoices::where('id', $choiceId)->first();

            UserAnswerPostPreeDetail::create([
                'question_id' => $questionId,
                'choice_id' => $choiceId,
                'nrp' => $request->nrp,
                'is_correct' => $choice ? $choice->is_correct : false,
            ]);
        }

        $totalScore = 0;
        foreach ($request->answers as $questionId => $choiceId) {
            // Ambil soal + pastikan milik test yang benar
            $question = QuestionTestDetailInternal::with('test')
                ->where('id', $questionId)
                ->whereHas('test', function ($q) use ($request) {
                    $q->where('type', $request->type)
                        ->where('detail_program_id', $request->detail_id);
                })->first();

            if (!$question)
                continue;

            $choice = QuestionChoices::find($choiceId);
            if ($choice && $choice->is_correct) {
                $totalScore += $question->bobot;
            }
        }

        $totalScore = round($totalScore, 2);

        $peserta = PeriodeBagianDetailInternal::where('nrp', $request->nrp)
            ->where('detail_program_id', $request->detail_id)
            ->firstOrFail();

        if ($request->type === 'post') {
            $peserta->update(['post_done_at' => now()]);

            $periode = PeriodeUtama::where('detail_id', $request->detail_id)->first();
            if ($periode) {
                $this->updateRekapBulanan(
                    $peserta->nrp,
                    Carbon::parse($periode->tanggal)->year,
                    Carbon::parse($periode->tanggal)->month
                );
            }
        }


        if ($request->type === 'pree') {
            $peserta->update(['pree_done_at' => now()]);
        }

        // ===============================
// 🎯 CEK & GENERATE SERTIFIKAT
// ===============================
        $this->checkAndGenerateCertificate($peserta);

        return back()
            ->with('success', 'Jawaban berhasil disimpan!')
            ->with('nilai_akhir', $totalScore); // ← kirim nilai ke frontend
    }

    protected function checkAndGenerateCertificate($peserta)
    {
        // Sudah pernah dibuat → STOP
        if ($peserta->sertifikat_generated_at) {
            return;
        }

        /**
         * Karena:
         * post  = sebelum pelatihan
         * pree  = setelah pelatihan (yang terakhir)
         */
        if (!$peserta->post_done_at) {
            return;
        }

        if (!$peserta->pree_done_at) {
            return;
        }


        // GenerateCertificateJob::dispatch($peserta->id);
    }


    // generate link and user input
    public function startPeriode(Request $request)
    {
        Log::info('Start periode diklat dimulai', [
            'user_id' => auth()->id(),
            'request' => $request->only(['periode_id', 'jam_diklat']),
        ]);

        $request->validate([
            'periode_id' => 'required|exists:periode_detail_internal,id',
            'jam_diklat' => 'required|integer|min:1',
        ]);

        $periode = PeriodeUtama::findOrFail($request->periode_id);

        // Log::info('Periode ditemukan', [
        //     'periode_id' => $periode->id,
        //     'nama_periode' => $periode->nama ?? null,
        // ]);

        $existingStartedPeriode = AksiDetailInternal::whereHas('periodeUtama', function ($query) use ($periode) {
            $query->where('detail_id', $periode->detail_id);
        })->whereNull('ended_at') // tidak dapat start periode lain selama ada pelatihan yang sedang berlangsung
            ->exists();



        if ($existingStartedPeriode) {
            Log::warning('Gagal start periode: sudah ada periode lain untuk program yang sama yang sudah dijalankan', [
                'detail_id' => $periode->detail_id,
                'periode_id' => $periode->id,
                'user_id' => auth()->id(),
            ]);

            return back()->withErrors([
                'periode_id' => 'Tidak dapat memulai periode ini karena sudah ada periode lain untuk program diklat yang sama yang sedang berjalan atau telah selesai.'
            ]);
        }

        if (AksiDetailInternal::where('periode_id', $periode->id)->exists()) {
            Log::warning('Gagal start periode: periode sudah dimulai', [
                'periode_id' => $periode->id,
                'user_id' => auth()->id(),
            ]);

            return back()->withErrors(['periode_id' => 'Periode ini sudah dimulai.']);
        }

        AksiDetailInternal::create([
            'periode_id' => $periode->id,
            'jam_diklat' => $request->jam_diklat,
            'ended_at' => null,
        ]);

        $periode = PeriodeUtama::findOrFail($request->periode_id);

        // Ambil semua peserta (nrp) di periode ini
        // $pesertas = PeriodeBagianDetailInternal::where('periode_id', $periode->id)->get();

        // $tahun = Carbon::parse($periode->tanggal)->year;
        // $bulan = Carbon::parse($periode->tanggal)->month;

        // foreach ($pesertas as $peserta) {
        //     if ($peserta->nrp) {
        //         $this->updateRekapBulanan($peserta->nrp, $tahun, $bulan);
        //         // Lebih baik: panggil via service, bukan $this jika di controller berbeda
        //     }
        // }

        Log::info('AksiDetailInternal dibuat', [
            'periode_id' => $periode->id,
            'jam_diklat' => $request->jam_diklat,
        ]);

        // ================= TOKEN =================

        $tokenPree = TestToken::create([
            'periode_id' => $periode->id,
            'token' => Str::random(64),
            'type' => 'pree',
            'expires_at' => now()->addHours(8),
            'is_used' => false,
        ]);

        Log::info('Token PRE TEST dibuat', [
            'periode_id' => $periode->id,
            'token_id' => $tokenPree->id,
            'expired_at' => $tokenPree->expires_at,
        ]);

        $tokenPost = TestToken::create([
            'periode_id' => $periode->id,
            'token' => Str::random(64),
            'type' => 'post',
            'expires_at' => now()->addHours(8),
            'is_used' => false,
        ]);

        Log::info('Token POST TEST dibuat', [
            'periode_id' => $periode->id,
            'token_id' => $tokenPost->id,
            'expired_at' => $tokenPost->expires_at,
        ]);

        $tokenEvaluasi = TestToken::create([
            'periode_id' => $periode->id,
            'token' => Str::random(64),
            'type' => 'evaluasi',
            'expires_at' => now()->addHours(8),
            'is_used' => false,
        ]);

        Log::info('Token EVALUASI dibuat', [
            'periode_id' => $periode->id,
            'token_id' => $tokenEvaluasi->id,
            'expired_at' => $tokenEvaluasi->expires_at,
        ]);

        Log::info('Start periode diklat SELESAI', [
            'periode_id' => $periode->id,
            'user_id' => auth()->id(),
        ]);

        //    $validasiStart = AksiDetailInternal::pluck('periode_id')->toArray();

        return Inertia::render('RencanaDiklat/RPT/PendidikanFormal/aksilanjut', [
            'token_link' => [
                'pree' => url("/test/token/pree/{$tokenPree->token}"),
                'post' => url("/test/token/post/{$tokenPost->token}"),
                'evaluasi' => url("/test/token/evaluasi/{$tokenEvaluasi->token}"),
            ],
            'ValidasiStart' => AksiDetailInternal::pluck('periode_id')->map(fn($id) => (string) $id)->toArray(),

        ]);
    }

    // end diklatnya
    public function endPeriode(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_detail_internal,id',
        ]);

        $aksi = AksiDetailInternal::where('periode_id', $request->periode_id)->firstOrFail();

        if ($aksi->ended_at) {
            return back()->with('warning', 'Periode ini sudah diakhiri sebelumnya.');
        }

        // Nonaktifkan semua token terkait
        TestToken::where('periode_id', $request->periode_id)
            ->update(['expires_at' => now()->subMinute()]); // expired sekarang

        // Tandai sebagai selesai
        $aksi->update(['ended_at' => now()]);

        // Optional: generate sertifikat untuk semua peserta yang memenuhi syarat
        $pesertas = PeriodeBagianDetailInternal::where('periode_id', $request->periode_id)->get();
        foreach ($pesertas as $peserta) {
            $this->checkAndGenerateCertificate($peserta);
        }

        Log::info('Periode diklat diakhiri', [
            'periode_id' => $request->periode_id,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Pelatihan berhasil diakhiri. Token telah dinonaktifkan.');
    }

    // rekap jam
    public function updateRekapBulanan($nrp, $tahun, $bulan)
    {
        Log::info('Memanggil updateRekapBulanan well', compact('nrp', 'tahun', 'bulan'));
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

        $totalJam = $jamDiklatKaryawan + $jamHLC + $jamInternal;

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


    public function openByToken($type, $token)
    {
        // dd($token);
        $tokenData = TestToken::where('token', $token)
            ->where('type', $type)
            ->firstOrFail();

        if (!$tokenData->isValid()) {
            abort(403, 'Token sudah tidak valid atau sudah digunakan.');
        }

        $periode = $tokenData->periode;

        // ambil test sesuai type token
        $test = PostPreeDetailInternal::with('questions.choices')
            ->where('detail_program_id', $periode->detail_id)
            ->where('type', $type)
            ->firstOrFail();

        return Inertia::render('RencanaDiklat/RPT/PendidikanFormal/PrePostTest/userview', [
            'test' => $test,
            'type' => $type,
            'detail_id' => $periode->detail_id,
            'token' => $tokenData->token
        ]);
    }

    // function evaluasi
    public function openEvaluasiByToken($token)
    {
        $tokenData = TestToken::where('token', $token)
            ->where('type', 'evaluasi')
            ->firstOrFail();


        // token → periode → detail_internal
        $periode = $tokenData->periode; // relasi
        if (!$periode) {
            abort(404, 'Periode tidak ditemukan');
        }

        $detail = DetailInternal::with('evaluasi')
            ->findOrFail($periode->detail_id);

        return Inertia::render(
            'RencanaDiklat/RPT/PendidikanFormal/PrePostTest/evaluasi',
            [
                'data' => $detail,
                'token' => $tokenData->token
            ]
        );
    }



    public function submitEvaluasi(Request $request)
    {
        Log::info('Submit evaluasi dipanggil', [
            'detail_id' => $request->detail_id,
            'token' => $request->token
        ]);

        $request->validate([
            'detail_id' => 'required|exists:detail_internal,id',
            'evaluasi' => 'required|string',
            'token' => 'required'
        ]);

        Log::debug('Validasi request evaluasi berhasil');

        $token = TestToken::where('token', $request->token)
            ->where('type', 'evaluasi')
            ->firstOrFail();

        Log::info('Token evaluasi ditemukan', [
            'token_id' => $token->id,
            'is_used' => $token->is_used,
            'expired_at' => $token->expired_at ?? null
        ]);

        if (!$token->isValid()) {
            Log::warning('Token evaluasi tidak valid', [
                'token' => $request->token
            ]);

            abort(403, 'Token tidak valid');
        }

        $evaluasi = EvaluasiDetailInternal::updateOrCreate(
            ['detail_id' => $request->detail_id],
            ['evaluasi' => $request->evaluasi]
        );

        Log::info('Evaluasi berhasil disimpan / diperbarui', [
            'evaluasi_id' => $evaluasi->id,
            'detail_id' => $evaluasi->detail_id
        ]);

        $token->update(['is_used' => true]);

        Log::info('Token evaluasi ditandai sebagai digunakan', [
            'token_id' => $token->id
        ]);

        return back()->with('success', 'Evaluasi berhasil disimpan');
    }






}
