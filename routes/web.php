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
use App\Models\User;
use App\Models\Wilayah;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Demografi Desa Sebalor
|--------------------------------------------------------------------------
*/

// ==================== PUBLIC ROUTES ====================
// Landing page untuk masyarakat umum (Read-only, tanpa login)
$buildPublicDashboardData = function () {
    $pendudukAktif = Penduduk::query()->where('status', 'Aktif');

    $totalPenduduk = (clone $pendudukAktif)->count();
    $totalKK = (clone $pendudukAktif)->distinct('nomor_kk')->count('nomor_kk');
    $totalLakiLaki = (clone $pendudukAktif)->where('jenis_kelamin', 'L')->count();
    $totalPerempuan = (clone $pendudukAktif)->where('jenis_kelamin', 'P')->count();
    $totalBalita = (clone $pendudukAktif)->where('kategori_usia', 'Balita')->count();
    $totalProduktif = (clone $pendudukAktif)->where('kategori_usia', 'Produktif')->count();
    $totalLansia = (clone $pendudukAktif)->where('kategori_usia', 'Lansia')->count();

    $ageRaw = (clone $pendudukAktif)
        ->selectRaw('SUM(CASE WHEN umur BETWEEN 0 AND 5 THEN 1 ELSE 0 END) as usia_0_5')
        ->selectRaw('SUM(CASE WHEN umur BETWEEN 6 AND 12 THEN 1 ELSE 0 END) as usia_6_12')
        ->selectRaw('SUM(CASE WHEN umur BETWEEN 13 AND 17 THEN 1 ELSE 0 END) as usia_13_17')
        ->selectRaw('SUM(CASE WHEN umur BETWEEN 18 AND 25 THEN 1 ELSE 0 END) as usia_18_25')
        ->selectRaw('SUM(CASE WHEN umur BETWEEN 26 AND 40 THEN 1 ELSE 0 END) as usia_26_40')
        ->selectRaw('SUM(CASE WHEN umur BETWEEN 41 AND 60 THEN 1 ELSE 0 END) as usia_41_60')
        ->selectRaw('SUM(CASE WHEN umur > 60 THEN 1 ELSE 0 END) as usia_60_plus')
        ->first();

    $ageLabels = ['0-5', '6-12', '13-17', '18-25', '26-40', '41-60', '>60'];
    $ageValues = [
        (int) ($ageRaw->usia_0_5 ?? 0),
        (int) ($ageRaw->usia_6_12 ?? 0),
        (int) ($ageRaw->usia_13_17 ?? 0),
        (int) ($ageRaw->usia_18_25 ?? 0),
        (int) ($ageRaw->usia_26_40 ?? 0),
        (int) ($ageRaw->usia_41_60 ?? 0),
        (int) ($ageRaw->usia_60_plus ?? 0),
    ];

    $educationRows = (clone $pendudukAktif)
        ->selectRaw("COALESCE(NULLIF(TRIM(pendidikan), ''), 'Tidak diketahui') as label")
        ->selectRaw('COUNT(*) as total')
        ->groupBy('label')
        ->orderByDesc('total')
        ->limit(7)
        ->get();

    $educationLabels = $educationRows->pluck('label')->values()->all();
    $educationValues = $educationRows->pluck('total')->map(fn ($value) => (int) $value)->values()->all();

    if (empty($educationLabels)) {
        $educationLabels = ['Belum ada data'];
        $educationValues = [0];
    }

    $dusunRows = Wilayah::query()
        ->from('wilayah as w')
        ->leftJoin('penduduk as p', function ($join) {
            $join->on('p.id_dusun', '=', 'w.id')
                ->where('p.status', '=', 'Aktif');
        })
        ->where('w.tipe', 'dusun')
        ->select('w.id', 'w.nama', 'w.latitude', 'w.longitude', 'w.luas_wilayah')
        ->selectRaw('COUNT(p.nik) as total_penduduk')
        ->groupBy('w.id', 'w.nama', 'w.latitude', 'w.longitude', 'w.luas_wilayah')
        ->orderBy('w.nama')
        ->get();

    $dusunMapData = $dusunRows
        ->map(function ($row) {
            return [
                'id' => (int) $row->id,
                'name' => $row->nama,
                'lat' => $row->latitude !== null ? (float) $row->latitude : null,
                'lng' => $row->longitude !== null ? (float) $row->longitude : null,
                'total_penduduk' => (int) $row->total_penduduk,
            ];
        })
        ->values()
        ->all();

    $coordinatedDusun = collect($dusunMapData)
        ->filter(fn ($dusun) => $dusun['lat'] !== null && $dusun['lng'] !== null)
        ->values();

    $mapCenterLat = $coordinatedDusun->isNotEmpty()
        ? (float) $coordinatedDusun->avg('lat')
        : -7.50;

    $mapCenterLng = $coordinatedDusun->isNotEmpty()
        ? (float) $coordinatedDusun->avg('lng')
        : 110.50;

    $totalDusun = Wilayah::query()->where('tipe', 'dusun')->count();
    $totalLuasDusun = (float) Wilayah::query()
        ->where('tipe', 'dusun')
        ->whereNotNull('luas_wilayah')
        ->sum('luas_wilayah');

    $kepadatan = $totalLuasDusun > 0
        ? round($totalPenduduk / $totalLuasDusun, 2)
        : 0;

    return [
        'totalPenduduk' => $totalPenduduk,
        'totalKK' => $totalKK,
        'totalLakiLaki' => $totalLakiLaki,
        'totalPerempuan' => $totalPerempuan,
        'totalBalita' => $totalBalita,
        'totalProduktif' => $totalProduktif,
        'totalLansia' => $totalLansia,
        'ageLabels' => $ageLabels,
        'ageValues' => $ageValues,
        'educationLabels' => $educationLabels,
        'educationValues' => $educationValues,
        'dusunMapData' => $dusunMapData,
        'mapCenterLat' => $mapCenterLat,
        'mapCenterLng' => $mapCenterLng,
        'totalDusun' => $totalDusun,
        'kepadatan' => $kepadatan,
    ];
};

$buildPublicProfileData = function () {
    $totalDusun = Wilayah::query()->where('tipe', 'dusun')->count();
    $totalRw = Wilayah::query()->where('tipe', 'rw')->count();
    $totalRt = Wilayah::query()->where('tipe', 'rt')->count();

    $totalLuasDusunKm2 = (float) Wilayah::query()
        ->where('tipe', 'dusun')
        ->whereNotNull('luas_wilayah')
        ->sum('luas_wilayah');

    // Ditampilkan dalam satuan hektar agar konsisten dengan teks pada blade profil
    $totalWilayahHa = round($totalLuasDusunKm2 * 100, 1);

    $totalPenduduk = Penduduk::query()->where('status', 'Aktif')->count();

    return [
        'totalWilayahHa' => $totalWilayahHa,
        'totalDusun' => $totalDusun,
        'totalRw' => $totalRw,
        'totalRt' => $totalRt,
        'totalPenduduk' => $totalPenduduk,
    ];
};

$buildPublicStatisticsData = function () {
    $privacyThreshold = 5;

    $pendudukAktif = Penduduk::query()->where('status', 'Aktif');
    $pendudukSemuaStatus = Penduduk::query();

    $totalPendudukAktif = (clone $pendudukAktif)->count();
    $totalKK = (clone $pendudukAktif)->distinct('nomor_kk')->count('nomor_kk');
    $totalDusun = Wilayah::query()->where('tipe', 'dusun')->count();
    $totalRw = Wilayah::query()->where('tipe', 'rw')->count();
    $totalRt = Wilayah::query()->where('tipe', 'rt')->count();

    $totalLuasDusunKm2 = (float) Wilayah::query()
        ->where('tipe', 'dusun')
        ->whereNotNull('luas_wilayah')
        ->sum('luas_wilayah');

    $kepadatan = $totalLuasDusunKm2 > 0
        ? round($totalPendudukAktif / $totalLuasDusunKm2, 2)
        : 0;

    $totalLakiLaki = (clone $pendudukAktif)->where('jenis_kelamin', 'L')->count();
    $totalPerempuan = (clone $pendudukAktif)->where('jenis_kelamin', 'P')->count();

    $genderLabels = ['Laki-laki', 'Perempuan'];
    $genderValues = [$totalLakiLaki, $totalPerempuan];
    $genderPercent = [
        'L' => $totalPendudukAktif > 0 ? round(($totalLakiLaki / $totalPendudukAktif) * 100, 1) : 0,
        'P' => $totalPendudukAktif > 0 ? round(($totalPerempuan / $totalPendudukAktif) * 100, 1) : 0,
    ];

    $usiaRaw = (clone $pendudukAktif)
        ->selectRaw('SUM(CASE WHEN umur BETWEEN 0 AND 5 THEN 1 ELSE 0 END) as usia_0_5')
        ->selectRaw('SUM(CASE WHEN umur BETWEEN 6 AND 12 THEN 1 ELSE 0 END) as usia_6_12')
        ->selectRaw('SUM(CASE WHEN umur BETWEEN 13 AND 17 THEN 1 ELSE 0 END) as usia_13_17')
        ->selectRaw('SUM(CASE WHEN umur BETWEEN 18 AND 59 THEN 1 ELSE 0 END) as usia_18_59')
        ->selectRaw('SUM(CASE WHEN umur >= 60 THEN 1 ELSE 0 END) as usia_60_plus')
        ->first();

    $ageLabels = ['0-5', '6-12', '13-17', '18-59', '60+'];
    $ageValues = [
        (int) ($usiaRaw->usia_0_5 ?? 0),
        (int) ($usiaRaw->usia_6_12 ?? 0),
        (int) ($usiaRaw->usia_13_17 ?? 0),
        (int) ($usiaRaw->usia_18_59 ?? 0),
        (int) ($usiaRaw->usia_60_plus ?? 0),
    ];

    $statusRows = (clone $pendudukSemuaStatus)
        ->select('status')
        ->selectRaw('COUNT(*) as total')
        ->groupBy('status')
        ->pluck('total', 'status');

    $statusLabels = ['Aktif', 'Keluar', 'Meninggal'];
    $statusValues = [
        (int) ($statusRows['Aktif'] ?? 0),
        (int) ($statusRows['Keluar'] ?? 0),
        (int) ($statusRows['Meninggal'] ?? 0),
    ];
    $statusTotal = array_sum($statusValues);
    $statusPercentages = [
        $statusTotal > 0 ? round(($statusValues[0] / $statusTotal) * 100, 1) : 0,
        $statusTotal > 0 ? round(($statusValues[1] / $statusTotal) * 100, 1) : 0,
        $statusTotal > 0 ? round(($statusValues[2] / $statusTotal) * 100, 1) : 0,
    ];

    $educationRows = (clone $pendudukAktif)
        ->selectRaw("COALESCE(NULLIF(TRIM(pendidikan), ''), 'Tidak diketahui') as label")
        ->selectRaw('COUNT(*) as total')
        ->groupBy('label')
        ->orderByDesc('total')
        ->get();

    $educationFiltered = $educationRows->filter(fn ($row) => (int) $row->total >= $privacyThreshold);
    $educationOthersTotal = (int) $educationRows->filter(fn ($row) => (int) $row->total < $privacyThreshold)->sum('total');

    $educationLabels = $educationFiltered->pluck('label')->values()->all();
    $educationValues = $educationFiltered->pluck('total')->map(fn ($value) => (int) $value)->values()->all();

    if ($educationOthersTotal > 0) {
        $educationLabels[] = 'Lainnya';
        $educationValues[] = $educationOthersTotal;
    }

    if (empty($educationLabels)) {
        $educationLabels = ['Belum ada data'];
        $educationValues = [0];
    }

    $occupationRows = (clone $pendudukAktif)
        ->selectRaw("COALESCE(NULLIF(TRIM(pekerjaan), ''), 'Tidak diketahui') as label")
        ->selectRaw('COUNT(*) as total')
        ->groupBy('label')
        ->orderByDesc('total')
        ->get();

    $occupationFiltered = $occupationRows->filter(fn ($row) => (int) $row->total >= $privacyThreshold);
    $occupationOthersTotal = (int) $occupationRows->filter(fn ($row) => (int) $row->total < $privacyThreshold)->sum('total');

    $occupationLabels = $occupationFiltered->pluck('label')->take(8)->values()->all();
    $occupationValues = $occupationFiltered->pluck('total')->take(8)->map(fn ($value) => (int) $value)->values()->all();

    $occupationTopTotal = array_sum($occupationValues);
    $occupationRemainingTotal = (int) $occupationFiltered->slice(8)->sum('total');

    $occupationOthersCombined = $occupationOthersTotal + $occupationRemainingTotal;
    if ($occupationOthersCombined > 0) {
        $occupationLabels[] = 'Lainnya';
        $occupationValues[] = $occupationOthersCombined;
    }

    if (empty($occupationLabels)) {
        $occupationLabels = ['Belum ada data'];
        $occupationValues = [0];
    }

    $dynamicRows = DB::table('dinamika_penduduk')
        ->where('tanggal_peristiwa', '>=', now()->subMonths(5)->startOfMonth())
        ->selectRaw("DATE_FORMAT(tanggal_peristiwa, '%Y-%m') as period")
        ->select('jenis_dinamika')
        ->selectRaw('COUNT(*) as total')
        ->groupByRaw("DATE_FORMAT(tanggal_peristiwa, '%Y-%m'), jenis_dinamika")
        ->orderByRaw("DATE_FORMAT(tanggal_peristiwa, '%Y-%m') ASC")
        ->get();

    $dynamicMap = $dynamicRows->groupBy('period');
    $trendLabels = [];
    $kelahiranSeries = [];
    $kematianSeries = [];
    $migrasiMasukSeries = [];
    $migrasiKeluarSeries = [];

    for ($i = 5; $i >= 0; $i--) {
        $month = now()->subMonths($i);
        $period = $month->format('Y-m');
        $trendLabels[] = $month->format('M');

        $records = collect($dynamicMap->get($period, []))->keyBy('jenis_dinamika');

        $kelahiranSeries[] = (int) ($records['Kelahiran']->total ?? 0);
        $kematianSeries[] = (int) ($records['Kematian']->total ?? 0);
        $migrasiMasukSeries[] = (int) ($records['Migrasi Masuk']->total ?? 0);
        $migrasiKeluarSeries[] = (int) ($records['Migrasi Keluar']->total ?? 0);
    }

    $dusunRows = Wilayah::query()
        ->from('wilayah as w')
        ->leftJoin('penduduk as p', function ($join) {
            $join->on('p.id_dusun', '=', 'w.id')
                ->where('p.status', '=', 'Aktif');
        })
        ->where('w.tipe', 'dusun')
        ->select('w.id', 'w.nama', 'w.latitude', 'w.longitude')
        ->selectRaw('COUNT(p.nik) as total_penduduk')
        ->groupBy('w.id', 'w.nama', 'w.latitude', 'w.longitude')
        ->orderBy('w.nama')
        ->get();

    $dusunPopulationRows = $dusunRows->map(function ($row) {
        return [
            'id' => (int) $row->id,
            'nama' => $row->nama,
            'lat' => $row->latitude !== null ? (float) $row->latitude : null,
            'lng' => $row->longitude !== null ? (float) $row->longitude : null,
            'total_penduduk' => (int) $row->total_penduduk,
        ];
    })->values()->all();

    $coordinatedDusun = collect($dusunPopulationRows)
        ->filter(fn ($row) => $row['lat'] !== null && $row['lng'] !== null)
        ->values();

    $mapCenterLat = $coordinatedDusun->isNotEmpty()
        ? (float) $coordinatedDusun->avg('lat')
        : -7.50;
    $mapCenterLng = $coordinatedDusun->isNotEmpty()
        ? (float) $coordinatedDusun->avg('lng')
        : 110.50;

    return [
        'privacyThreshold' => $privacyThreshold,
        'totalPendudukAktif' => $totalPendudukAktif,
        'totalKK' => $totalKK,
        'totalDusun' => $totalDusun,
        'totalRw' => $totalRw,
        'totalRt' => $totalRt,
        'totalLuasDusunKm2' => $totalLuasDusunKm2,
        'kepadatan' => $kepadatan,
        'genderLabels' => $genderLabels,
        'genderValues' => $genderValues,
        'genderPercent' => $genderPercent,
        'ageLabels' => $ageLabels,
        'ageValues' => $ageValues,
        'statusLabels' => $statusLabels,
        'statusValues' => $statusValues,
        'statusPercentages' => $statusPercentages,
        'educationLabels' => $educationLabels,
        'educationValues' => $educationValues,
        'occupationLabels' => $occupationLabels,
        'occupationValues' => $occupationValues,
        'trendLabels' => $trendLabels,
        'kelahiranSeries' => $kelahiranSeries,
        'kematianSeries' => $kematianSeries,
        'migrasiMasukSeries' => $migrasiMasukSeries,
        'migrasiKeluarSeries' => $migrasiKeluarSeries,
        'dusunPopulationRows' => $dusunPopulationRows,
        'mapCenterLat' => $mapCenterLat,
        'mapCenterLng' => $mapCenterLng,
    ];
};

Route::get('/', function () use ($buildPublicDashboardData) {
    return view('masyarakat.beranda', $buildPublicDashboardData());
})->name('public.home');

Route::get('/profil-desa', function () use ($buildPublicProfileData) {
    return view('masyarakat.profil', $buildPublicProfileData());
})->name('public.profil');

Route::get('/statistik', function () use ($buildPublicStatisticsData) {
    return view('masyarakat.statistik', $buildPublicStatisticsData());
})->name('public.statistik');

Route::get('/peta-wilayah', function () use ($buildPublicDashboardData) {
    return view('masyarakat.beranda', $buildPublicDashboardData());
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
        $authUser = Auth::user();
        $idDusun = $authUser->id_dusun;

        $dusun = null;
        if ($idDusun) {
            $dusun = Wilayah::query()
                ->where('tipe', 'dusun')
                ->find($idDusun);
        }

        $pendudukDusunQuery = Penduduk::query()
            ->where('status', 'Aktif')
            ->when(
                $idDusun,
                fn ($query) => $query->where('id_dusun', $idDusun),
                fn ($query) => $query->whereRaw('1 = 0')
            );

        $totalPenduduk = (clone $pendudukDusunQuery)->count();
        $totalLakiLaki = (clone $pendudukDusunQuery)->where('jenis_kelamin', 'L')->count();
        $totalPerempuan = (clone $pendudukDusunQuery)->where('jenis_kelamin', 'P')->count();
        $totalKK = (clone $pendudukDusunQuery)->distinct('nomor_kk')->count('nomor_kk');

        $totalBalita = (clone $pendudukDusunQuery)->where('kategori_usia', 'Balita')->count();
        $totalProduktif = (clone $pendudukDusunQuery)->where('kategori_usia', 'Produktif')->count();
        $totalLansia = (clone $pendudukDusunQuery)->where('kategori_usia', 'Lansia')->count();

        $ageRaw = (clone $pendudukDusunQuery)
            ->selectRaw('SUM(CASE WHEN umur BETWEEN 0 AND 5 THEN 1 ELSE 0 END) as usia_0_5')
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

        $educationRows = (clone $pendudukDusunQuery)
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

        $occupationRows = (clone $pendudukDusunQuery)
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

        $trendRows = (clone $pendudukDusunQuery)
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period")
            ->selectRaw('COUNT(*) as total')
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $trendLabels = [];
        $trendValues = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $period = $month->format('Y-m');
            $trendLabels[] = $month->translatedFormat('M');
            $trendValues[] = (int) ($trendRows[$period]->total ?? 0);
        }

        $dinamikaRows = DB::table('dinamika_penduduk as dp')
            ->join('penduduk as p', 'p.nik', '=', 'dp.nik')
            ->whereMonth('dp.tanggal_peristiwa', now()->month)
            ->whereYear('dp.tanggal_peristiwa', now()->year)
            ->when(
                $idDusun,
                fn ($query) => $query->where('p.id_dusun', $idDusun),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->select('dp.jenis_dinamika')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('dp.jenis_dinamika')
            ->pluck('total', 'jenis_dinamika');

        $kelahiran = (int) ($dinamikaRows['Kelahiran'] ?? 0);
        $kematian = (int) ($dinamikaRows['Kematian'] ?? 0);
        $migrasiMasuk = (int) ($dinamikaRows['Migrasi Masuk'] ?? 0);
        $migrasiKeluar = (int) ($dinamikaRows['Migrasi Keluar'] ?? 0);

        $totalPerDusun = Wilayah::query()
            ->from('wilayah as w')
            ->leftJoin('penduduk as p', function ($join) {
                $join->on('p.id_dusun', '=', 'w.id')
                    ->where('p.status', '=', 'Aktif');
            })
            ->where('w.tipe', 'dusun')
            ->select('w.id', 'w.nama')
            ->selectRaw('COUNT(p.nik) as total_penduduk')
            ->groupBy('w.id', 'w.nama')
            ->orderBy('w.nama')
            ->get();

        $latitude = $dusun?->latitude ? (float) $dusun->latitude : -7.5;
        $longitude = $dusun?->longitude ? (float) $dusun->longitude : 110.5;
        $kepadatan = ($dusun && (float) $dusun->luas_wilayah > 0)
            ? round($totalPenduduk / (float) $dusun->luas_wilayah, 2)
            : 0;

        return view('kasun.dashboard', [
            'dusun' => $dusun,
            'totalPenduduk' => $totalPenduduk,
            'totalLakiLaki' => $totalLakiLaki,
            'totalPerempuan' => $totalPerempuan,
            'totalKK' => $totalKK,
            'totalBalita' => $totalBalita,
            'totalProduktif' => $totalProduktif,
            'totalLansia' => $totalLansia,
            'kelahiran' => $kelahiran,
            'kematian' => $kematian,
            'migrasiMasuk' => $migrasiMasuk,
            'migrasiKeluar' => $migrasiKeluar,
            'ageLabels' => $ageLabels,
            'ageValues' => $ageValues,
            'educationLabels' => $educationLabels,
            'educationValues' => $educationValues,
            'occupationLabels' => $occupationLabels,
            'occupationValues' => $occupationValues,
            'trendLabels' => $trendLabels,
            'trendValues' => $trendValues,
            'totalPerDusun' => $totalPerDusun,
            'mapLat' => $latitude,
            'mapLng' => $longitude,
            'kepadatan' => $kepadatan,
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
        $user = User::with('dusun')->find(Auth::id()) ?? Auth::user();

        return view('kasun.profile', compact('user'));
    })->name('profile');
});
