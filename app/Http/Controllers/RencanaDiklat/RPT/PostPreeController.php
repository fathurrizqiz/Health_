<?php

namespace App\Http\Controllers\RencanaDiklat\RPT;

use App\Http\Controllers\Controller;
use App\Models\DetailInternal;
use App\Models\EvaluasiDetailInternal;
use App\Models\PeriodeUtama;
use App\Models\PostPreeDetailInternal;
use App\Models\QuestionChoices;
use App\Models\QuestionTestDetailInternal;
use App\Models\TestToken;
use App\Models\UserAnswerPostPreeDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;
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

        foreach ($request->questions as $qIndex => $q) {
            \Log::info("Saving question", ['index' => $qIndex, 'question' => $q['text']]);

            $question = QuestionTestDetailInternal::create([
                'test_id' => $test->id,
                'pertanyaan' => $q['text'],
                'tipe' => 'multiple_choice',
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

        return back()->with('success', 'Jawaban berhasil disimpan!');
    }


    // generate link and user input
    public function startPeriode(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_detail_internal,id'
        ]);

        $periode = PeriodeUtama::findOrFail($request->periode_id);

        // token pree
        $tokenPree = TestToken::create([
            'periode_id' => $periode->id,
            'token' => Str::random(64),
            'type' => 'pree',
            'expires_at' => now()->addHours(8),
            'is_used' => false,
        ]);

        // token post
        $tokenPost = TestToken::create([
            'periode_id' => $periode->id,
            'token' => Str::random(64),
            'type' => 'post',
            'expires_at' => now()->addHours(8),
            'is_used' => false,
        ]);

        // token evaluasi
        $tokenEvaluasi = TestToken::create([
            'periode_id' => $periode->id,
            'token' => Str::random(64),
            'type' => 'evaluasi', // ⬅️ BARU
            'expires_at' => now()->addHours(8),
            'is_used' => false,
        ]);


        return Inertia::render('RencanaDiklat/RPT/PendidikanFormal/aksilanjut', [
            'token_link' => [
                'pree' => url("/test/token/pree/{$tokenPree->token}"),
                'post' => url("/test/token/post/{$tokenPost->token}"),
                'evaluasi' => url("/test/token/evaluasi/{$tokenEvaluasi->token}"),
            ]
        ]);


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

        if (!$tokenData->isValid()) {
            abort(403, 'Token tidak valid');
        }

        // asumsi relasi sudah ada
        $periode = $tokenData->periode;

        $data = DetailInternal::findOrFail($periode->detail_id);

        return Inertia::render(
            'RencanaDiklat/RPT/PendidikanFormal/PrePostTest/evaluasi',
            [
                'data' => $data,
                'token' => $tokenData->token
            ]
        );
    }

    public function submitEvaluasi(Request $request)
    {
        $request->validate([
            'detail_id' => 'required|exists:detail_internal,id',
            'evaluasi' => 'required|string',
            'token' => 'required'
        ]);

        $token = TestToken::where('token', $request->token)
            ->where('type', 'evaluasi')
            ->firstOrFail();

        if (!$token->isValid()) {
            abort(403, 'Token tidak valid');
        }

        EvaluasiDetailInternal::updateOrCreate(
            ['detail_id' => $request->detail_id],
            ['evaluasi' => $request->evaluasi]
        );

        $token->update(['is_used' => true]);

        return back()->with('success', 'Evaluasi berhasil disimpan');
    }





}
