<?php

use App\Http\Controllers\KaryawanDiklat\ApprovDiklateController;
use App\Http\Controllers\KaryawanDiklat\DiklatController;
use App\Http\Controllers\jadwalDiklat\JadwalDiklatController;
use App\Http\Controllers\MasterData\MasterDataController;
use App\Http\Controllers\Materi\MateriController;
use App\Http\Controllers\RencanaDiklat\RPT\NonFormalController;
use App\Http\Controllers\RencanaDiklat\RPT\pendidikanController;
use App\Http\Controllers\Silabus\SilabusController;
use App\Http\Controllers\RencanaDiklat\HLC\HLCController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/Diklat', [DiklatController::class, 'index'])->name('diklat.home');
Route::get('/Diklat/create', [DiklatController::class, 'create'])->name('diklat.create');
Route::post('/Diklat/store', [DiklatController::class, 'store'])->name('diklat.store');
Route::get('/Diklat/pdf/preview/{id}', [DiklatController::class, 'preview'])->name('diklat.preview');
Route::get('/Diklat/edit/{id}', [DiklatController::class, 'edit'])->name('diklat.edit');
Route::put('/Diklat/update/{id}', [DiklatController::class, 'update'])->name('diklat.update');
Route::delete('/Diklat/destroy/{id}', [DiklatController::class, 'destroy'])->name('diklat.destroy');

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

//Pendidikan Formal
Route::get('/RencanaDiklat/RPT/PF', [pendidikanController::class, 'index'])->name('PF.index');
Route::post('/RencanaDiklat/RPT/PF/store', [pendidikanController::class, 'store'])->name('PF.store');
Route::delete('/RencanaDiklat/RPT/PF/delete/${id}', [pendidikanController::class, 'destroy'])->name('PF.destroy');
//Pendidikan Non Formal
Route::get('/RencanaDiklat/RPT/PN', [NonFormalController::class, 'index']);
//jadwal diklat
Route::get('/RencanaDiklat/jadwal', [JadwalDiklatController::class, 'index']);
//Silabus
Route::get('/silabus/diklat', [SilabusController::class, 'index']);

// HLC
Route::get('/HLC/Home/manajemen', [HLCController::class, 'index']);

// Master Data
Route::get('/MasterData/home', [MasterDataController::class, 'index']);
Route::get('/MasterData/create', [MasterDataController::class, 'pages']);
Route::post('/MasterData/store', [MasterDataController::class, 'store']);
Route::put('/MasterData/update', [MasterDataController::class, 'updateTargetJam'])->name('update.masterdata');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
