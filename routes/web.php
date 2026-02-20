<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Demografi Desa Sebalor
|--------------------------------------------------------------------------
*/

// ==================== PUBLIC ROUTES ====================
// Landing page untuk masyarakat umum (Read-only, tanpa login)
Route::get('/', function () {
    return view('masyarakat.beranda');
})->name('public.home');

Route::get('/profil-desa', function () {
    return view('masyarakat.profil');
})->name('public.profil');

Route::get('/statistik', function () {
    return view('masyarakat.statistik');
})->name('public.statistik');

Route::get('/peta-wilayah', function () {
    return view('masyarakat.peta');
})->name('public.peta');

Route::get('/kontak', function () {
    return view('masyarakat.kontak');
})->name('public.kontak');

// ==================== LOGIN ROUTES ====================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==================== KASI PEMERINTAHAN ROUTES ====================
Route::prefix('kasi')->name('kasi.')->middleware(['auth', 'role:kasi'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('kasi.dashboard', [
            'totalPenduduk' => 1450,
            'totalLakiLaki' => 754,
            'totalPerempuan' => 696,
            'persenLakiLaki' => 52,
            'persenPerempuan' => 48,
            'totalBalita' => 145,
            'totalProduktif' => 980,
            'totalLansia' => 325,
        ]);
    })->name('dashboard');
    
    // Data Penduduk
    Route::get('/penduduk', function () {
        return view('kasi.penduduk.index', [
            'penduduk' => [] // Empty untuk sample data di blade
        ]);
    })->name('penduduk.index');
    
    Route::get('/penduduk/create', function () {
        return view('kasi.penduduk.create');
    })->name('penduduk.create');
    
    Route::get('/penduduk/{id}/edit', function ($id) {
        return view('kasi.penduduk.edit', ['id' => $id]);
    })->name('penduduk.edit');
    
    Route::delete('/penduduk/{id}', function ($id) {
        return redirect()->route('kasi.penduduk.index')->with('success', 'Data berhasil dihapus');
    })->name('penduduk.destroy');
    
    // Upload Data
    Route::get('/upload', function () {
        return view('kasi.upload');
    })->name('upload');
    
    Route::post('/upload/process', function () {
        return redirect()->route('kasi.penduduk.index')->with('success', 'Data berhasil diupload dan diimport');
    })->name('upload.process');
    
    Route::get('/upload/template', function () {
        // Return download template Excel (implementation later)
        return response()->download(public_path('templates/template-penduduk.xlsx'));
    })->name('upload.template');
    
    // Wilayah
    Route::get('/wilayah', function () {
        return view('kasi.wilayah.index');
    })->name('wilayah.index');
    
    Route::get('/wilayah/create', function () {
        return view('kasi.wilayah.create');
    })->name('wilayah.create');
    
    Route::get('/wilayah/{id}/edit', function ($id) {
        return view('kasi.wilayah.edit', ['id' => $id]);
    })->name('wilayah.edit');
    
    // Laporan
    Route::get('/laporan', function () {
        return view('kasi.laporan');
    })->name('laporan');
    
    // Manajemen User
    Route::get('/users', function () {
        return view('kasi.users.index');
    })->name('users.index');
    
    Route::get('/users/create', function () {
        return view('kasi.users.create');
    })->name('users.create');
    
    // Settings
    Route::get('/settings', function () {
        return view('kasi.settings');
    })->name('settings');
});

// ==================== KASUN ROUTES ====================
Route::prefix('kasun')->name('kasun.')->middleware(['auth', 'role:kasun'])->group(function () {
    
    // Dashboard Kasun
    Route::get('/dashboard', function () {
        return view('kasun.dashboard', [
            'totalPenduduk' => 450,
            'totalLakiLaki' => 234,
            'totalPerempuan' => 216,
            'totalBalita' => 45,
            'totalProduktif' => 320,
            'totalLansia' => 85,
            'kelahiran' => 3,
            'kematian' => 1,
            'migrasiMasuk' => 2,
            'migrasiKeluar' => 1,
        ]);
    })->name('dashboard');
    
    // Statistik
    Route::get('/statistik', function () {
        return view('kasun.statistik');
    })->name('statistik');
    
    // Peta
    Route::get('/peta', function () {
        return view('kasun.peta');
    })->name('peta');
    
    // Profile
    Route::get('/profile', function () {
        return view('kasun.profile');
    })->name('profile');
});
