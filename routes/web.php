<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DosenController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\BrandingController;
use App\Http\Controllers\Admin\MataKuliahController;
use App\Http\Controllers\Admin\LaporanNilaiController as AdminLaporanNilaiController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\TahunAkademikController;
use App\Http\Controllers\Dosen\SoalController;
use App\Http\Controllers\Dosen\MahasiswaController as DosenMahasiswaController;
use App\Http\Controllers\Dosen\LaporanNilaiController as DosenLaporanNilaiController;
use App\Http\Controllers\Dosen\UjianController as DosenUjianController;
use App\Http\Controllers\Mahasiswa\UjianController as MahasiswaUjianController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dosen/template/download', [DosenController::class, 'downloadTemplate'])->name('dosen.template.download');
    Route::post('dosen/template/upload', [DosenController::class, 'uploadTemplate'])->name('dosen.template.upload');
    Route::resource('dosen', DosenController::class);
    Route::get('mahasiswa/template/download', [MahasiswaController::class, 'downloadTemplate'])->name('mahasiswa.template.download');
    Route::post('mahasiswa/template/upload', [MahasiswaController::class, 'uploadTemplate'])->name('mahasiswa.template.upload');
    Route::resource('mahasiswa', MahasiswaController::class);
    Route::resource('tahun-akademik', TahunAkademikController::class)->except(['show']);
    Route::resource('semester', SemesterController::class)->except(['show']);
    Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kelas'])->except(['show']);
    Route::resource('mata-kuliah', MataKuliahController::class)->except(['show']);
    Route::get('branding', [BrandingController::class, 'edit'])->name('branding.edit');
    Route::post('branding', [BrandingController::class, 'update'])->name('branding.update');
    Route::get('laporan-nilai', [AdminLaporanNilaiController::class, 'index'])->name('laporan-nilai.index');
    Route::get('laporan-nilai/{ujian}', [AdminLaporanNilaiController::class, 'show'])->name('laporan-nilai.show');
    Route::get('laporan-nilai/{ujian}/mahasiswa/{mahasiswa}', [AdminLaporanNilaiController::class, 'cetakMahasiswa'])->name('laporan-nilai.mahasiswa');
});

Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('mahasiswa', [DosenMahasiswaController::class, 'index'])->name('mahasiswa.index');
    Route::resource('ujian', DosenUjianController::class)->except(['show']);
    Route::get('ujian/{ujian}/hasil', [DosenUjianController::class, 'hasil'])->name('ujian.hasil');
    Route::get('ujian/{ujian}/hasil/{nilai}/koreksi-essay', [DosenUjianController::class, 'koreksiEssay'])->name('ujian.hasil.koreksi-essay');
    Route::post('ujian/{ujian}/hasil/{nilai}/koreksi-essay', [DosenUjianController::class, 'simpanKoreksiEssay'])->name('ujian.hasil.koreksi-essay.simpan');
    Route::get('ujian/{ujian}/akses-mahasiswa', [DosenUjianController::class, 'aksesMahasiswa'])->name('ujian.akses-mahasiswa');
    Route::post('ujian/{ujian}/akses-mahasiswa', [DosenUjianController::class, 'simpanAksesMahasiswa'])->name('ujian.akses-mahasiswa.simpan');
    Route::post('ujian/{ujian}/soal-reorder', [SoalController::class, 'reorder'])->name('ujian.soal.reorder');
    Route::patch('ujian/{ujian}/soal/{soal}/poin', [SoalController::class, 'updatePoin'])->name('ujian.soal.poin.update');
    Route::post('ujian/{ujian}/soal/poin-massal', [SoalController::class, 'bulkUpdatePoin'])->name('ujian.soal.poin.bulk');
    Route::resource('ujian.soal', SoalController::class)->except(['show']);
    Route::get('ujian/{ujian}/soal-import', [SoalController::class, 'importForm'])->name('ujian.soal.import.form');
    Route::post('ujian/{ujian}/soal-import', [SoalController::class, 'importStore'])->name('ujian.soal.import.store');
    Route::get('laporan-nilai', [DosenLaporanNilaiController::class, 'index'])->name('laporan-nilai.index');
    Route::get('laporan-nilai/{ujian}', [DosenLaporanNilaiController::class, 'show'])->name('laporan-nilai.show');
    Route::get('laporan-nilai/{ujian}/mahasiswa/{mahasiswa}', [DosenLaporanNilaiController::class, 'cetakMahasiswa'])->name('laporan-nilai.mahasiswa');
});

Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('ujian', [MahasiswaUjianController::class, 'index'])->name('ujian.index');
    Route::get('ujian/{ujian}', [MahasiswaUjianController::class, 'show'])->name('ujian.show');
    Route::post('ujian/{ujian}/start', [MahasiswaUjianController::class, 'start'])->name('ujian.start');
    Route::get('percobaan/{percobaan}/kerjakan', [MahasiswaUjianController::class, 'kerjakan'])->name('ujian.kerjakan');
    Route::post('percobaan/{percobaan}/submit', [MahasiswaUjianController::class, 'submit'])->name('ujian.submit');
    Route::get('percobaan/{percobaan}/hasil', [MahasiswaUjianController::class, 'hasil'])->name('ujian.hasil');
    Route::get('percobaan/{percobaan}/cetak', [MahasiswaUjianController::class, 'cetak'])->name('ujian.cetak');
});

require __DIR__.'/auth.php';
