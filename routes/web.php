<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Evaluasi\EvaluasiController;
use App\Http\Controllers\JadwalDiklat\JadwalEksternalController;
use App\Http\Controllers\JadwalDiklat\JadwalHLCController;
use App\Http\Controllers\JadwalDiklat\JadwalInternalController;
use App\Http\Controllers\KaryawanDiklat\ApprovDiklateController;
use App\Http\Controllers\KaryawanDiklat\DiklatController;
use App\Http\Controllers\jadwalDiklat\JadwalDiklatController;
use App\Http\Controllers\KaryawanDiklat\InternalController;
use App\Http\Controllers\MasterData\MasterDataController;
use App\Http\Controllers\Materi\MateriController;
use App\Http\Controllers\RencanaDiklat\RPT\DetailPeriodeController;
use App\Http\Controllers\RencanaDiklat\RPT\DiklatInternalController;
use App\Http\Controllers\RencanaDiklat\RPT\DokumentasiController;
use App\Http\Controllers\RencanaDiklat\RPT\NonFormalController;
use App\Http\Controllers\RencanaDiklat\RPT\pendidikanController;
use App\Http\Controllers\RencanaDiklat\RPT\PostPreeController;
use App\Http\Controllers\RencanaDiklat\RPT\PresensiDetailController;
use App\Http\Controllers\RencanaDiklat\RPT\SertifikatController;
use App\Http\Controllers\Silabus\SilabusController;
use App\Http\Controllers\RencanaDiklat\HLC\HLCController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard',[DashboardController::class,'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/Diklat', [DiklatController::class, 'index'])->name('diklat.home');
Route::get('/Diklat/create', [DiklatController::class, 'create'])->name('diklat.create');
Route::post('/Diklat/store', [DiklatController::class, 'store'])->name('diklat.store');
Route::get('/Diklat/pdf/preview/{id}', [DiklatController::class, 'preview'])->name('diklat.preview');
Route::get('/Diklat/edit/{id}', [DiklatController::class, 'edit'])->name('diklat.edit');
Route::put('/Diklat/update/{id}', [DiklatController::class, 'update'])->name('diklat.update');
Route::delete('/Diklat/destroy/{id}', [DiklatController::class, 'destroy'])->name('diklat.destroy');

// halaman lihat Internal
Route::get('/DiklatInternal/user',[InternalController::class,'index'])->name('diklat.internal.index');

// Appprove HLC dan Eksternal
Route::get('/Approve/Diklat', [ApprovDiklateController::class,'index']);
Route::put('/diklat/{id}/approve', [ApprovDiklateController::class, 'approve'])->name('diklat.approve');
Route::put('/diklat/{id}/reject', [ApprovDiklateController::class, 'reject'])->name('diklat.reject');

//Materi Diklat
// Route::get('/Materi', [MateriController::class, 'index']);
Route::get('/Materi', [MateriController::class, 'index']);
Route::get('/Materi/folder/{folderId}', [MateriController::class, 'index']);
Route::get('/Materi/view', [MateriController::class, 'getAll']);
Route::post('/Materi/store', [MateriController::class, 'store']);
Route::put('/Materi/verify/{id}', [MateriController::class, 'verify']);
Route::put('/Materi/reject/{id}', [MateriController::class, 'reject']);
Route::delete('/Materi/delete/{id}', [MateriController::class, 'delete']);

//Internal
Route::get('/RencanaDiklat/RPT/PF', [DiklatInternalController::class, 'index'])->name('PF.index');
Route::post('/RencanaDiklat/RPT/PF/store', [DiklatInternalController::class, 'storeProgram'])->name('PF.store');
Route::delete('/RencanaDiklat/RPT/PF/delete/{id}', [DiklatInternalController::class, 'destroyProgram'])->name('PF.destroy');
Route::delete('/RencanaDiklat/RPT/PF/detail/delete/{id}', [DiklatInternalController::class, 'destroyDetail'])->name('PF.destroy-detail');
Route::post('/RencanaDiklat/RPT/PF/DetailStore',[DiklatInternalController::class, 'storeDetail'])->name('diklat.detail-internal');
// aksi detail internal
Route::get('/RencanaDiklat/Internal/detail/aksi/{id}',[DiklatInternalController::class,'aksi'])->name('aksi-internal');
// Program Detail Internal
Route::get('/RencanaDiklat/Internal/detail/periode/{id}',[DiklatInternalController::class,'periode'])->name('periode-internal');
Route::post('/RencanaDiklat/Internal/detail/periode/store',[DiklatInternalController::class,'storePeriode'])->name('periode-internal.store');
Route::delete('/RencanaDiklat/Internal/detail/periode/delete/{id}',[DiklatInternalController::class,'destroyPeriod'])->name('periode-internal.delete');
// PostTest
Route::get('/DiklatInternal/pree/{detailId}', [PostPreeController::class, 'preTest']);
Route::get('/DiklatInternal/post/{detailId}', [PostPreeController::class, 'postTest']);
Route::post('/DiklatInternal/preetest', [PostPreeController::class, 'savePre']);
Route::post('/DiklatInternal/posttest', [PostPreeController::class, 'savePost']);
// Route::get('/DiklatInternal/evaluasi', [PostPreeController::class, 'openEvaluasiByToken']);
// user post and pree
Route::get('/DiklatInternal/test/{type}/{detail_id}', [PostPreeController::class, 'showTest']);
Route::post('/DiklatInternal/test/submit', [PostPreeController::class, 'submitTest']);
// by token
    Route::post('/DiklatInternal/periode/start', [PostPreeController::class, 'startPeriode']);
Route::post('/DiklatInternal/periode/end', [PostPreeController::class, 'endPeriode']);
Route::get('/test/token/evaluasi/{token}', [PostPreeController::class, 'openEvaluasiByToken']);
Route::get('/test/token/{type}/{token}', [PostPreeController::class, 'openByToken']);
// post evaluasi
Route::post('/test/evaluasi/post',[PostPreeController::class,'submitEvaluasi']);
// Route Detail Periode
Route::get('/DiklatInternal/detailperiod/list/{detail_id}',[DetailPeriodeController::class,'index'])->name('Detail.periode');
Route::post('/DiklatInternal/detailperiod/list/store',[DetailPeriodeController::class,'store'])->name('Detail.periode-store');
Route::delete('/DiklatInternal/detailperiod/list/delete',[DetailPeriodeController::class,'bulkDelete'])->name('Detail.periode-store');
// Presensi Detail
Route::get('/DiklatInternal/detail/presensi/{periode_id}',[PresensiDetailController::class,'index']);
// Template Pembahasan Sertifikat
Route::get('/DiklatInternal/detail/pembahasan/template/{periode}',[SertifikatController::class,'template']);
Route::post('/DiklatInternal/detail/pembahasan/template/store',[SertifikatController::class,'storeTemplate']);
Route::post('/sertifikat/generate/{peserta}', [SertifikatController::class, 'generate']);
Route::get('/sertifikat/download/{peserta}', [SertifikatController::class, 'download']);

// Dokumentasi
Route::get('/DetailInternal/Dokumentasi/view/{periode_id}', [DokumentasiController::class,'index']);
Route::post('/DetailInternal/Dokumentasi/store', [DokumentasiController::class,'storeDokumentasi']);

// Evaluasi
Route::get('/Diklat/Evaluasi',[EvaluasiController::class,'index']);

//Pendidikan Non Formal / Eksternal
Route::get('/RencanaDiklat/RPT/PN', [NonFormalController::class, 'index'])->name('Diklat.eksternal');
Route::post('/RencanaDiklat/RPT/PN/Program',[NonFormalController::class,'storeProgram'])->name('Diklat.eksternal-program');
Route::post('/RencanaDiklat/RPT/PN/Detail',[NonFormalController::class,'storeDetail'])->name('Diklat.eksternal-program');
//jadwal diklat
Route::get('/JadwalDiklat/HLC', [JadwalHLCController::class, 'index']);
Route::get('/JadwalDiklat/Eksternal', [JadwalEksternalController::class, 'index']);
Route::get('/JadwalDiklat/Internal', [JadwalInternalController::class, 'index']);
//Silabus
Route::get('/silabus/diklat', [SilabusController::class, 'index']);

// HLC
Route::get('/HLC/Home/manajemen', [HLCController::class, 'index'])->name('diklat.hlc.admin');
Route::post('/HLC/Home/storeProgram', [HLCController::class, 'storeProgram'])->name('diklat.hlc.admin.store-program');
Route::post('/HLC/Home/storeDetail', [HLCController::class, 'storeDetail'])->name('diklat.hlc.admin.store-detail');

// Master Data
Route::get('/MasterData/home', [MasterDataController::class, 'index']);
Route::get('/MasterData/create', [MasterDataController::class, 'pages']);
Route::post('/MasterData/store', [MasterDataController::class, 'store']);
Route::put('/MasterData/update', [MasterDataController::class, 'updateTargetJam'])->name('update.masterdata');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
