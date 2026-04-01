<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\MataKuliahDosenController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\ValidasiController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\TahapController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Mata Kuliah — read accessible by both roles, writes GKMP only
    Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])->name('mata-kuliah.index');

    // GKMP-only routes
    Route::middleware('role:gkmp')->group(function () {
        // Mata Kuliah CRUD (write) — create/edit must come BEFORE {mataKuliah} wildcard
        Route::get('/mata-kuliah/create', [MataKuliahController::class, 'create'])->name('mata-kuliah.create');
        Route::post('/mata-kuliah', [MataKuliahController::class, 'store'])->name('mata-kuliah.store');
        Route::get('/mata-kuliah/{mataKuliah}/edit', [MataKuliahController::class, 'edit'])->name('mata-kuliah.edit');
        Route::put('/mata-kuliah/{mataKuliah}', [MataKuliahController::class, 'update'])->name('mata-kuliah.update');
        Route::patch('/mata-kuliah/{mataKuliah}', [MataKuliahController::class, 'update']);
        Route::delete('/mata-kuliah/{mataKuliah}', [MataKuliahController::class, 'destroy'])->name('mata-kuliah.destroy');

        // Mata Kuliah Dosen Access Management
        Route::get('/mata-kuliah/{mataKuliah}/dosen/edit', [MataKuliahDosenController::class, 'edit'])->name('mata-kuliah-dosen.edit');
        Route::put('/mata-kuliah/{mataKuliah}/dosen', [MataKuliahDosenController::class, 'update'])->name('mata-kuliah-dosen.update');
        Route::delete('/mata-kuliah/{mataKuliah}/dosen/{dosen}', [MataKuliahDosenController::class, 'destroy'])->name('mata-kuliah-dosen.destroy');

        // Kelola Dosen — manage dosen assignments
        Route::resource('dosen', DosenController::class);

        // Semester management
        Route::resource('semester', SemesterController::class);
        Route::post('/semester/{semester}/set-active', [SemesterController::class, 'setActive'])->name('semester.set-active');

        // Tahap management
        Route::resource('tahap', TahapController::class);

        // User management
        Route::resource('users', UserManagementController::class)->except(['show', 'destroy']);
        Route::patch('/users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggle-active');

        // Validasi
        Route::get('/validasi', [ValidasiController::class, 'index'])->name('validasi.index');
        Route::get('/validasi/{dokumen}', [ValidasiController::class, 'show'])->name('validasi.show');
        Route::patch('/validasi/{dokumen}', [ValidasiController::class, 'update'])->name('validasi.update');
        Route::post('/validasi/bulk-approve', [ValidasiController::class, 'bulkApprove'])->name('validasi.bulk-approve');

        // Laporan
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
    });

    // Mata Kuliah show — wildcard defined AFTER the GKMP 'create' route so it doesn't shadow it
    Route::get('/mata-kuliah/{mataKuliah}', [MataKuliahController::class, 'show'])->name('mata-kuliah.show');

    // Dokumen
    Route::get('/dokumen', [DokumenController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/create', [DokumenController::class, 'create'])
        ->middleware('role:dosen')->name('dokumen.create');
    Route::post('/dokumen', [DokumenController::class, 'store'])
        ->middleware('role:dosen')->name('dokumen.store');
    Route::get('/dokumen/check', [DokumenController::class, 'checkExistingDokumen'])->name('dokumen.check');
    Route::get('/dokumen/{dokumen}', [DokumenController::class, 'show'])->name('dokumen.show');
    Route::delete('/dokumen/{dokumen}', [DokumenController::class, 'destroy'])
        ->middleware('role:dosen,gkmp')->name('dokumen.destroy');
    Route::get('/api/jenis-dokumen', [DokumenController::class, 'getJenisDokumen'])->name('dokumen.jenis');
    Route::get('/api/mata-kuliah-by-semester', [DokumenController::class, 'getMatakuliahBySemester'])->name('dokumen.mata-kuliah-by-semester');
    Route::get('/api/tahap-by-semester', [DokumenController::class, 'getTahapBySemester'])->name('dokumen.tahap-by-semester');

    // Password Change — Dosen Only
    Route::middleware('role:dosen')->group(function () {
        Route::get('/ubah-password', [PasswordChangeController::class, 'edit'])->name('password-change.edit');
    });
});

require __DIR__.'/auth.php';
