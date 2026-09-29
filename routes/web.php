<?php

use App\Http\Controllers\BayiBaruLahirController;
use App\Http\Controllers\CatatanPersalinanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Edukasi_KIE_Controller;
use App\Http\Controllers\ObservasiPartografController;
use App\Http\Controllers\PartografCetakController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\Pemantuan_Kala_4_Controller;
use App\Http\Controllers\PersalinanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('/test', DashboardController::class);
Route::resource('pasien', PasienController::class);
Route::get('/pasien/{pasien_id}/input', [PasienController::class, 'inputTerpadu'])->name('pasien.input_terpadu');
Route::resource('persalinan', PersalinanController::class);
Route::resource('observasi-partograf', ObservasiPartografController::class);
Route::resource('catatan-persalinan', CatatanPersalinanController::class);
Route::resource('bayi-baru-lahir', BayiBaruLahirController::class);
Route::resource('pemantauan-kala-4', Pemantuan_Kala_4_Controller::class);
Route::resource('edukasi-kie', Edukasi_KIE_Controller::class);

// Rute Cetak Dokumen Resmi Partograf (PDF 2 Halaman Sesuai Template)
Route::get('/partograf/cetak/{persalinan_id}', [PartografCetakController::class, 'cetak'])->name('partograf.cetak');
Route::get('/partograf/cetak-pasien/{pasien_id}', [PartografCetakController::class, 'cetakByPasien'])->name('partograf.cetak_by_pasien');
Route::get('/partograf/json/{persalinan_id}', [PartografCetakController::class, 'apiJson'])->name('partograf.json');
