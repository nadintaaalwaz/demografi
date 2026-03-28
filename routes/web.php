<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PendudukController;
use App\Models\Penduduk;
use App\Models\Wilayah;

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

// Test route untuk debug
Route::get('/test-auth', function() {
    if (Auth::check()) {
        $user = Auth::user();
        return response()->json([
            'logged_in' => true,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'nama' => $user->nama,
                'role' => $user->role
            ],
            'session_id' => session()->getId()
        ]);
    }
    return response()->json(['logged_in' => false]);
});

// Test direct login
Route::get('/test-direct-login', function(Request $request) {
    $credentials = [
        'username' => 'kasi',
        'password' => 'kasisebalor726'
    ];
    
    if (Auth::attempt($credentials, true)) {
        $request->session()->regenerate();
        $user = Auth::user();
        
        // Langsung cek apakah Auth::check() berhasil
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'auth_check' => Auth::check(),
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'nama' => $user->nama,
                'role' => $user->role
            ],
            'session_id' => session()->getId()
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'Login gagal!',
        'user_exists' => \App\Models\User::where('username', 'kasi')->exists(),
        'user_data' => \App\Models\User::where('username', 'kasi')->first(['id', 'username', 'nama'])
    ]);
});

// Test dashboard tanpa middleware
Route::get('/kasi/dashboard-test', function () {
    $totalPenduduk = Penduduk::count();
    $totalLakiLaki = Penduduk::where('jenis_kelamin', 'L')->count();
    $totalPerempuan = Penduduk::where('jenis_kelamin', 'P')->count();
    $persenLakiLaki = $totalPenduduk > 0 ? round(($totalLakiLaki / $totalPenduduk) * 100) : 0;
    $persenPerempuan = $totalPenduduk > 0 ? round(($totalPerempuan / $totalPenduduk) * 100) : 0;

    $totalBalita = Penduduk::where('kategori_usia', 'Balita')->count();
    $totalProduktif = Penduduk::where('kategori_usia', 'Produktif')->count();
    $totalLansia = Penduduk::where('kategori_usia', 'Lansia')->count();

    $ageRaw = Penduduk::query()->selectRaw('SUM(CASE WHEN umur BETWEEN 0 AND 5 THEN 1 ELSE 0 END) as usia_0_5')
        ->selectRaw('SUM(CASE WHEN umur BETWEEN 6 AND 12 THEN 1 ELSE 0 END) as usia_6_12')
        ->selectRaw('SUM(CASE WHEN umur BETWEEN 13 AND 17 THEN 1 ELSE 0 END) as usia_13_17')
        ->selectRaw('SUM(CASE WHEN umur BETWEEN 18 AND 60 THEN 1 ELSE 0 END) as usia_18_60')
        ->selectRaw('SUM(CASE WHEN umur > 60 THEN 1 ELSE 0 END) as usia_60_plus')
        ->first();

    $ageLabels = ['0-5', '6-12', '13-17', '18-60', '>60'];
    $ageValues = [
        (int) ($ageRaw->usia_0_5 ?? 0),
        (int) ($ageRaw->usia_6_12 ?? 0),
        (int) ($ageRaw->usia_13_17 ?? 0),
        (int) ($ageRaw->usia_18_60 ?? 0),
        (int) ($ageRaw->usia_60_plus ?? 0),
    ];

    $educationRows = Penduduk::query()
        ->selectRaw("COALESCE(NULLIF(TRIM(pendidikan), ''), 'Tidak diketahui') as label")
        ->selectRaw('COUNT(*) as total')
        ->groupBy('label')
        ->orderByDesc('total')
        ->limit(6)
        ->get();

    $educationLabels = $educationRows->pluck('label')->values()->all();
    $educationValues = $educationRows->pluck('total')->map(fn ($value) => (int) $value)->values()->all();

    if (empty($educationLabels)) {
        $educationLabels = ['Belum ada data'];
        $educationValues = [0];
    }

    $occupationRows = Penduduk::query()
        ->selectRaw("COALESCE(NULLIF(TRIM(pekerjaan), ''), 'Tidak diketahui') as label")
        ->selectRaw('COUNT(*) as total')
        ->groupBy('label')
        ->orderByDesc('total')
        ->get();

    $topOccupation = $occupationRows->take(5)->values();
    $otherOccupationTotal = (int) $occupationRows->slice(5)->sum('total');

    $occupationLabels = $topOccupation->pluck('label')->values()->all();
    $occupationValues = $topOccupation->pluck('total')->map(fn ($value) => (int) $value)->values()->all();

    if ($otherOccupationTotal > 0) {
        $occupationLabels[] = 'Lainnya';
        $occupationValues[] = $otherOccupationTotal;
    }

    if (empty($occupationLabels)) {
        $occupationLabels = ['Belum ada data'];
        $occupationValues = [0];
    }

    $dusunMapData = Wilayah::query()
        ->from('wilayah as w')
        ->leftJoin('penduduk as p', 'p.id_dusun', '=', 'w.id')
        ->where('w.tipe', 'dusun')
        ->select('w.id', 'w.nama', 'w.latitude', 'w.longitude')
        ->selectRaw('COUNT(p.nik) as total_penduduk')
        ->selectRaw("SUM(CASE WHEN p.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as total_laki_laki")
        ->selectRaw("SUM(CASE WHEN p.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as total_perempuan")
        ->groupBy('w.id', 'w.nama', 'w.latitude', 'w.longitude')
        ->orderBy('w.nama')
        ->get()
        ->map(function ($row) {
            return [
                'id' => (int) $row->id,
                'name' => $row->nama,
                'lat' => (float) $row->latitude,
                'lng' => (float) $row->longitude,
                'total_penduduk' => (int) $row->total_penduduk,
                'total_laki_laki' => (int) $row->total_laki_laki,
                'total_perempuan' => (int) $row->total_perempuan,
            ];
        })
        ->values()
        ->all();

    $totalPendudukTerpetakan = array_sum(array_map(fn ($item) => (int) ($item['total_penduduk'] ?? 0), $dusunMapData));

    $totalLuasDusun = (float) Wilayah::query()
        ->where('tipe', 'dusun')
        ->whereNotNull('luas_wilayah')
        ->sum('luas_wilayah');

    return view('kasi.dashboard', [
        'totalPenduduk' => $totalPenduduk,
        'totalLakiLaki' => $totalLakiLaki,
        'totalPerempuan' => $totalPerempuan,
        'persenLakiLaki' => $persenLakiLaki,
        'persenPerempuan' => $persenPerempuan,
        'totalBalita' => $totalBalita,
        'totalProduktif' => $totalProduktif,
        'totalLansia' => $totalLansia,
        'ageLabels' => $ageLabels,
        'ageValues' => $ageValues,
        'educationLabels' => $educationLabels,
        'educationValues' => $educationValues,
        'occupationLabels' => $occupationLabels,
        'occupationValues' => $occupationValues,
        'dusunMapData' => $dusunMapData,
        'totalPendudukTerpetakan' => $totalPendudukTerpetakan,
        'totalLuasDusun' => $totalLuasDusun,
        'sebalorBoundaryUrl' => asset('data/sebalor-boundary.geojson'),
    ]);
});

// ==================== KASI PEMERINTAHAN ROUTES ====================
Route::prefix('kasi')->name('kasi.')->middleware(['auth', 'role:kasi'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        $totalPenduduk = Penduduk::count();
        $totalLakiLaki = Penduduk::where('jenis_kelamin', 'L')->count();
        $totalPerempuan = Penduduk::where('jenis_kelamin', 'P')->count();
        $persenLakiLaki = $totalPenduduk > 0 ? round(($totalLakiLaki / $totalPenduduk) * 100) : 0;
        $persenPerempuan = $totalPenduduk > 0 ? round(($totalPerempuan / $totalPenduduk) * 100) : 0;

        $totalBalita = Penduduk::where('kategori_usia', 'Balita')->count();
        $totalProduktif = Penduduk::where('kategori_usia', 'Produktif')->count();
        $totalLansia = Penduduk::where('kategori_usia', 'Lansia')->count();

        $ageRaw = Penduduk::query()->selectRaw('SUM(CASE WHEN umur BETWEEN 0 AND 5 THEN 1 ELSE 0 END) as usia_0_5')
            ->selectRaw('SUM(CASE WHEN umur BETWEEN 6 AND 12 THEN 1 ELSE 0 END) as usia_6_12')
            ->selectRaw('SUM(CASE WHEN umur BETWEEN 13 AND 17 THEN 1 ELSE 0 END) as usia_13_17')
            ->selectRaw('SUM(CASE WHEN umur BETWEEN 18 AND 60 THEN 1 ELSE 0 END) as usia_18_60')
            ->selectRaw('SUM(CASE WHEN umur > 60 THEN 1 ELSE 0 END) as usia_60_plus')
            ->first();

        $ageLabels = ['0-5', '6-12', '13-17', '18-60', '>60'];
        $ageValues = [
            (int) ($ageRaw->usia_0_5 ?? 0),
            (int) ($ageRaw->usia_6_12 ?? 0),
            (int) ($ageRaw->usia_13_17 ?? 0),
            (int) ($ageRaw->usia_18_60 ?? 0),
            (int) ($ageRaw->usia_60_plus ?? 0),
        ];

        $educationRows = Penduduk::query()
            ->selectRaw("COALESCE(NULLIF(TRIM(pendidikan), ''), 'Tidak diketahui') as label")
            ->selectRaw('COUNT(*) as total')
            ->groupBy('label')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $educationLabels = $educationRows->pluck('label')->values()->all();
        $educationValues = $educationRows->pluck('total')->map(fn ($value) => (int) $value)->values()->all();

        if (empty($educationLabels)) {
            $educationLabels = ['Belum ada data'];
            $educationValues = [0];
        }

        $occupationRows = Penduduk::query()
            ->selectRaw("COALESCE(NULLIF(TRIM(pekerjaan), ''), 'Tidak diketahui') as label")
            ->selectRaw('COUNT(*) as total')
            ->groupBy('label')
            ->orderByDesc('total')
            ->get();

        $topOccupation = $occupationRows->take(5)->values();
        $otherOccupationTotal = (int) $occupationRows->slice(5)->sum('total');

        $occupationLabels = $topOccupation->pluck('label')->values()->all();
        $occupationValues = $topOccupation->pluck('total')->map(fn ($value) => (int) $value)->values()->all();

        if ($otherOccupationTotal > 0) {
            $occupationLabels[] = 'Lainnya';
            $occupationValues[] = $otherOccupationTotal;
        }

        if (empty($occupationLabels)) {
            $occupationLabels = ['Belum ada data'];
            $occupationValues = [0];
        }

        $dusunMapData = Wilayah::query()
            ->from('wilayah as w')
            ->leftJoin('penduduk as p', 'p.id_dusun', '=', 'w.id')
            ->where('w.tipe', 'dusun')
            ->select('w.id', 'w.nama', 'w.latitude', 'w.longitude')
            ->selectRaw('COUNT(p.nik) as total_penduduk')
            ->selectRaw("SUM(CASE WHEN p.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as total_laki_laki")
            ->selectRaw("SUM(CASE WHEN p.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as total_perempuan")
            ->groupBy('w.id', 'w.nama', 'w.latitude', 'w.longitude')
            ->orderBy('w.nama')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => (int) $row->id,
                    'name' => $row->nama,
                    'lat' => (float) $row->latitude,
                    'lng' => (float) $row->longitude,
                    'total_penduduk' => (int) $row->total_penduduk,
                    'total_laki_laki' => (int) $row->total_laki_laki,
                    'total_perempuan' => (int) $row->total_perempuan,
                ];
            })
            ->values()
            ->all();

        $totalPendudukTerpetakan = array_sum(array_map(fn ($item) => (int) ($item['total_penduduk'] ?? 0), $dusunMapData));

        $totalLuasDusun = (float) Wilayah::query()
            ->where('tipe', 'dusun')
            ->whereNotNull('luas_wilayah')
            ->sum('luas_wilayah');

        return view('kasi.dashboard', [
            'totalPenduduk' => $totalPenduduk,
            'totalLakiLaki' => $totalLakiLaki,
            'totalPerempuan' => $totalPerempuan,
            'persenLakiLaki' => $persenLakiLaki,
            'persenPerempuan' => $persenPerempuan,
            'totalBalita' => $totalBalita,
            'totalProduktif' => $totalProduktif,
            'totalLansia' => $totalLansia,
            'ageLabels' => $ageLabels,
            'ageValues' => $ageValues,
            'educationLabels' => $educationLabels,
            'educationValues' => $educationValues,
            'occupationLabels' => $occupationLabels,
            'occupationValues' => $occupationValues,
            'dusunMapData' => $dusunMapData,
            'totalPendudukTerpetakan' => $totalPendudukTerpetakan,
            'totalLuasDusun' => $totalLuasDusun,
            'sebalorBoundaryUrl' => asset('data/sebalor-boundary.geojson'),
        ]);
    })->name('dashboard');
    
    // Data Penduduk
    Route::get('/penduduk', [PendudukController::class, 'index'])->name('penduduk.index');
    Route::get('/penduduk/{nik}', [PendudukController::class, 'show'])->name('penduduk.show');
    
    Route::get('/penduduk/create', function () {
        return view('kasi.penduduk.create');
    })->name('penduduk.create');
    
    Route::get('/penduduk/{id}/edit', function ($id) {
        return view('kasi.penduduk.edit', ['id' => $id]);
    })->name('penduduk.edit');
    
    Route::delete('/penduduk/{id}', function ($id) {
        return redirect()->route('kasi.penduduk.index')->with('success', 'Data berhasil dihapus');
    })->name('penduduk.destroy');
    
    // Upload Data Bank KK
    Route::get('/upload', [PendudukController::class, 'uploadForm'])->name('upload.form');
    Route::post('/upload/process', [PendudukController::class, 'upload'])->name('upload.process');
    
    Route::get('/upload/template', function () {
        // Return download template Excel (implementation later)
        return response()->download(public_path('templates/template-penduduk.xlsx'));
    })->name('upload.template');
    
    // Wilayah (CRUD)
    Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    Route::get('/wilayah/create', [WilayahController::class, 'create'])->name('wilayah.create');
    Route::post('/wilayah', [WilayahController::class, 'store'])->name('wilayah.store');
    Route::get('/wilayah/{id}/edit', [WilayahController::class, 'edit'])->name('wilayah.edit');
    Route::put('/wilayah/{id}', [WilayahController::class, 'update'])->name('wilayah.update');
    Route::delete('/wilayah/{id}', [WilayahController::class, 'destroy'])->name('wilayah.destroy');

    // Dinamika Penduduk (Monitoring)
    Route::get('/dinamika-penduduk', function () {
        return view('kasi.dinamika-penduduk');
    })->name('dinamika');
    
    // Laporan
    Route::get('/laporan', function () {
        return view('kasi.laporan');
    })->name('laporan');
    
    // Manajemen User Kasun
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    
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
