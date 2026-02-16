<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RepositorySkpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SkpPengajuanController;
use App\Http\Controllers\SkpVerifikasiController;
use App\Http\Controllers\VerifikasiTtdController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('redirect.role')
        : redirect()->route('login');
});

Route::get('/redirect-role', function () {
    $role = strtolower(auth()->user()->role); 
    return match ($role) {
        'admin'  => redirect()->route('admin.dashboard'),
        'kepala' => redirect()->route('kepala.dashboard'),
        'pegawai' => redirect()->route('dashboard'),
        default   => redirect()->route('dashboard'),
    };
})->middleware('auth')->name('redirect.role');

Route::middleware('auth')->group(function () {

    // ======================
    // UMUM
    // ======================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/show/done/{id}', [DashboardController::class,'showfinish'])->name('skp.showskpdone');
    
    // SKP BARU & DETAIL UMUM
    Route::get('/skp/baru', [SkpPengajuanController::class, 'create'])->name('skp.baru');
    Route::post('/skp/upload-temp', [SkpPengajuanController::class, 'uploadTemp'])->name('skp.uploadTemp');
    Route::get('/skp/riwayat', [SkpPengajuanController::class, 'index'])->name('skp.riwayat');
    Route::get('/skp/show/{id}', [SkpPengajuanController::class, 'show'])->name('skp.show');
    Route::post('/skp', [SkpPengajuanController::class, 'store'])->name('skp.store');
    
    // TAMBAHAN: Rute untuk melihat/download SKP yang sudah selesai (Final)
    Route::get('/skp/final/{id}', [SkpPengajuanController::class, 'downloadFinal'])->name('skp.final');

    // ======================
    // PEGAWAI
    // ======================
    Route::middleware('role:pegawai')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // TAMBAHAN: Rute supaya pegawai bisa akses halaman edit pas status "Perbaikan"
        Route::get('/pegawai/skp/edit/{id}', [SkpPengajuanController::class, 'edit'])->name('skp.edit');
        Route::put('/pegawai/skp/update/{id}', [SkpPengajuanController::class, 'update'])->name('skp.update');
    });

    // ======================
    // ADMIN
    // ======================
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/skp/{id}', [DashboardController::class, 'show'])->name('admin.skp.show');
        Route::put('/skp/{id}/update-status', [DashboardController::class, 'updateStatus'])->name('admin.skp.update-status');

        Route::get('/skp/verifikasi', [SkpPengajuanController::class, 'index'])->name('admin.skp.verifikasi');

        Route::post('/skp/{id}/verifikasi', [SkpVerifikasiController::class, 'verifikasi']);
        Route::post('/skp/{id}/perbaikan', [SkpVerifikasiController::class, 'perbaikan']);
    });

    // ======================
    // KEPALA RM
    // ======================
    Route::middleware('role:kepala')->prefix('kepala')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('kepala.dashboard');

        // TAMBAHAN: Rute buat Kepala liat dokumen dulu sebelum di-TTD (Halaman Review)
        Route::get('/skp/ttd/get/{id}', [VerifikasiTtdController::class, 'getTTD'])->name('skp.getTTD');
        Route::post('/ttd/{doc}', [VerifikasiTtdController::class, 'simpan'])->name('skp.saveTTD');
        Route::put('/skp/{id}/update-status', [VerifikasiTtdController::class, 'updateStatus'])->name('kepala.skp.update-status');

        Route::get('/skp/detail/{id}', [VerifikasiTtdController::class, 'show'])->name('skp.show.detail');

        // Route untuk menampilkan HALAMAN tanda tangan (GET)
        Route::get('/skp/{id}/ttd', [VerifikasiTtdController::class, 'showTtd'])->name('kepala.skp.ttd');

        Route::get('/skp/riwayat', [SkpPengajuanController::class, 'indexKepala'])->name('kepala.riwayat');
        Route::get('/skp/riwayat/{id_user}', [SkpPengajuanController::class, 'indexDetail'])->name('kepala.riwayat-user');
    });
});

require __DIR__.'/auth.php';