<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wilayah;
use App\Models\Penduduk;
use Illuminate\Support\Facades\DB;

class PublicStatisticController extends Controller
{
    // 1. Handle Landing Page / Beranda Utama Masyarakat (Menggantikan $buildPublicDashboardData)
    public function beranda()
    {
        $pendudukAktif = Penduduk::query()->where('status', 'Aktif');

        $totalPenduduk = (clone $pendudukAktif)->count();
        $totalKK = (clone $pendudukAktif)->where('status_keluarga', 'Kepala Keluarga')->count();

        if ($totalKK === 0) {
            $totalKK = (clone $pendudukAktif)->distinct('nomor_kartu_keluarga')->count('nomor_kartu_keluarga');
        }

        $totalLakiLaki = (clone $pendudukAktif)->where('jenis_kelamin', 'L')->count();
        $totalPerempuan = (clone $pendudukAktif)->where('jenis_kelamin', 'P')->count();
        $totalBalita = (clone $pendudukAktif)->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 0 AND 5')->count();
        $totalProduktif = (clone $pendudukAktif)->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 20 AND 59')->count();
        $totalLansia = (clone $pendudukAktif)->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60')->count();

        $ageRaw = (clone $pendudukAktif)
            ->selectRaw("SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 0 AND 5 THEN 1 ELSE 0 END) as bayi_balita")
            ->selectRaw("SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 6 AND 11 THEN 1 ELSE 0 END) as anak_anak")
            ->selectRaw("SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 12 AND 19 THEN 1 ELSE 0 END) as remaja")
            ->selectRaw("SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 20 AND 59 THEN 1 ELSE 0 END) as dewasa")
            ->selectRaw("SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60 THEN 1 ELSE 0 END) as lansia")
            ->first();

        $ageLabels = ['Bayi & Balita (0–5 Tahun)', 'Anak-anak (6–11 Tahun)', 'Remaja (10–19 Tahun)', 'Dewasa (19–59 Tahun)', 'Lansia (60+ Tahun)'];
        $ageValues = [
            (int) ($ageRaw->bayi_balita ?? 0),
            (int) ($ageRaw->anak_anak ?? 0),
            (int) ($ageRaw->remaja ?? 0),
            (int) ($ageRaw->dewasa ?? 0),
            (int) ($ageRaw->lansia ?? 0),
        ];

        $educationRaw = (clone $pendudukAktif)
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pendidikan, ''))) IN ('SD','TAMAT SD','SEKOLAH DASAR','TAMAT SD / SEDERAJAT','TAMAT SD/SEDERAJAT','SD/SEDERAJAT') THEN 1 ELSE 0 END) as tamat_sd")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pendidikan, ''))) IN ('SMP','TAMAT SMP','SEKOLAH MENENGAH PERTAMA') THEN 1 ELSE 0 END) as smp")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pendidikan, ''))) IN ('SMA','SMK','SLTA','TAMAT SMA') THEN 1 ELSE 0 END) as sma")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pendidikan, ''))) IN ('D3','D-3','DIII','DIPLOMA III','DIPLOMA 3','AKADEMI/ DIPLOMA III/S. MUDA','AKADEMI/DIPLOMA III/S. MUDA','AKADEMI / DIPLOMA III / S. MUDA','AKADEMI') THEN 1 ELSE 0 END) as diploma_iii")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pendidikan, ''))) IN ('D4','D-4','DIV','D IV','D.IV','DIPLOMA IV','DIPLOMA 4','DIPLOMA IV/STRATA I','DIPLOMA IV / STRATA I','DIPLOMA IV/ STRATA I','DIPLOMA IV /STRATA I','S1','S-1','S 1','S.1','STRATA I','STRATA 1','SARJANA') THEN 1 ELSE 0 END) as diploma_iv_s1")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pendidikan, ''))) IN ('S2','STRATA II','STRATA 2','MAGISTER') THEN 1 ELSE 0 END) as strata_ii")
            ->first();

        $educationFixedOrder = ['Tamat SD', 'SMP', 'SMA', 'DIPLOMA III', 'DIPLOMA IV/STRATA I', 'STRATA II'];
        $educationValuesMap = [
            'Tamat SD' => (int) ($educationRaw->tamat_sd ?? 0),
            'SMP' => (int) ($educationRaw->smp ?? 0),
            'SMA' => (int) ($educationRaw->sma ?? 0),
            'DIPLOMA III' => (int) ($educationRaw->diploma_iii ?? 0),
            'DIPLOMA IV/STRATA I' => (int) ($educationRaw->diploma_iv_s1 ?? 0),
            'STRATA II' => (int) ($educationRaw->strata_ii ?? 0),
        ];

        $matchedTotal = array_sum(array_values($educationValuesMap));
        $othersTotal = max(0, $totalPenduduk - $matchedTotal);
        $educationLabels = $educationFixedOrder;
        $educationValues = array_map(fn($label) => $educationValuesMap[$label] ?? 0, $educationLabels);

        if ($othersTotal > 0) {
            $educationLabels[] = 'Lainnya';
            $educationValues[] = $othersTotal;
        }

        $dusunRows = Wilayah::query()
            ->from('wilayah as w')
            ->leftJoin('penduduk as p', function ($join) {
                $join->on('p.id_dusun', '=', 'w.id')->where('p.status', '=', 'Aktif');
            })
            ->where('w.tipe', 'dusun')
            ->select('w.id', 'w.nama', 'w.latitude', 'w.longitude', 'w.luas_wilayah')
            ->selectRaw('COUNT(p.nik) as total_penduduk')
            ->groupBy('w.id', 'w.nama', 'w.latitude', 'w.longitude', 'w.luas_wilayah')
            ->orderBy('w.nama')->get();

        $dusunMapData = $dusunRows->map(function ($row) {
            return [
                'id' => (int) $row->id,
                'name' => $row->nama,
                'lat' => $row->latitude !== null ? (float) $row->latitude : null,
                'lng' => $row->longitude !== null ? (float) $row->longitude : null,
                'total_penduduk' => (int) $row->total_penduduk,
            ];
        })->values()->all();

        $coordinatedDusun = collect($dusunMapData)->filter(fn ($dusun) => $dusun['lat'] !== null && $dusun['lng'] !== null)->values();
        $mapCenterLat = $coordinatedDusun->isNotEmpty() ? (float) $coordinatedDusun->avg('lat') : -7.50;
        $mapCenterLng = $coordinatedDusun->isNotEmpty() ? (float) $coordinatedDusun->avg('lng') : 110.50;

        $totalDusun = Wilayah::query()->where('tipe', 'dusun')->count();
        $totalLuasDusun = (float) Wilayah::query()->where('tipe', 'dusun')->whereNotNull('luas_wilayah')->sum('luas_wilayah');
        $kepadatan = $totalLuasDusun > 0 ? round($totalPenduduk / $totalLuasDusun, 2) : 0;

        return view('masyarakat.beranda', compact(
            'totalPenduduk', 'totalKK', 'totalLakiLaki', 'totalPerempuan', 'totalBalita', 
            'totalProduktif', 'totalLansia', 'ageLabels', 'ageValues', 'educationLabels', 
            'educationValues', 'dusunMapData', 'mapCenterLat', 'mapCenterLng', 'totalDusun', 'kepadatan'
        ));
    }

    // 2. Handle Halaman Profil Desa
    public function profil()
    {
        $totalDusun = Wilayah::query()->where('tipe', 'dusun')->count();
        $totalRw = Wilayah::query()->where('tipe', 'rw')->count();
        $totalRt = Wilayah::query()->where('tipe', 'rt')->count();

        $totalLuasDusunKm2 = (float) Wilayah::query()
            ->where('tipe', 'dusun')
            ->whereNotNull('luas_wilayah')
            ->sum('luas_wilayah');

        $totalWilayahHa = round($totalLuasDusunKm2 * 100, 1);
        $totalPenduduk = Penduduk::query()->where('status', 'Aktif')->count();

        return view('masyarakat.profil', compact(
            'totalWilayahHa', 'totalDusun', 'totalRw', 'totalRt', 'totalPenduduk'
        ));
    }

    // 3. Handle Halaman Statistik Publik
    public function statistik(Request $request)
    {
        $privacyThreshold = 5;
        $analysisYear = (int) $request->query('tahun_analisis', now()->year);

        $tahunOptions = [];
        for ($y = now()->year; $y >= now()->year - 3; $y--) {
            $tahunOptions[$y] = $y;
        }

        $wilayahCounts = Wilayah::query()
            ->select('tipe', DB::raw('count(*) as total'), DB::raw("SUM(CASE WHEN tipe = 'dusun' AND luas_wilayah IS NOT NULL THEN luas_wilayah ELSE 0 END) as luas_dusun"))
            ->groupBy('tipe')->get()->keyBy('tipe');

        $totalDusun = (int) ($wilayahCounts['dusun']->total ?? 0);
        $totalRw = (int) ($wilayahCounts['rw']->total ?? 0);
        $totalRt = (int) ($wilayahCounts['rt']->total ?? 0);
        $totalLuasDesaKm2 = (float) ($wilayahCounts['dusun']->luas_dusun ?? 0);
        $totalWilayahHa = round($totalLuasDesaKm2 * 100, 1);

        $pendudukAktif = Penduduk::query()->where('status', 'Aktif');
        $totalPendudukAktif = $pendudukAktif->count();

        $totalKK = (clone $pendudukAktif)->where('status_keluarga', 'Kepala Keluarga')->count();
        if ($totalKK === 0) {
            $totalKK = (clone $pendudukAktif)->distinct('nomor_kartu_keluarga')->count('nomor_kartu_keluarga');
        }

        $kepadatan = $totalLuasDesaKm2 > 0 ? round($totalPendudukAktif / $totalLuasDesaKm2, 2) : 0;

        $genderRows = (clone $pendudukAktif)->select('jenis_kelamin', DB::raw('count(*) as total'))->groupBy('jenis_kelamin')->pluck('total', 'jenis_kelamin');
        $totalLakiLaki = (int) ($genderRows['L'] ?? 0); $totalPerempuan = (int) ($genderRows['P'] ?? 0);
        $genderLabels = ['Laki-laki', 'Perempuan']; $genderValues = [$totalLakiLaki, $totalPerempuan];
        $genderPercent = [
            'L' => $totalPendudukAktif > 0 ? round(($totalLakiLaki / $totalPendudukAktif) * 100, 1) : 0,
            'P' => $totalPendudukAktif > 0 ? round(($totalPerempuan / $totalPendudukAktif) * 100, 1) : 0,
        ];

        $usiaRaw = (clone $pendudukAktif)
            ->selectRaw('SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 0 AND 5 THEN 1 ELSE 0 END) as usia_bayi_balita')
            ->selectRaw('SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 6 AND 11 THEN 1 ELSE 0 END) as usia_anak')
            ->selectRaw('SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 12 AND 18 THEN 1 ELSE 0 END) as usia_remaja')
            ->selectRaw('SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 19 AND 59 THEN 1 ELSE 0 END) as usia_dewasa')
            ->selectRaw('SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60 THEN 1 ELSE 0 END) as usia_lansia')
            ->first();

        $ageLabels = ['Bayi & Balita (0–5 Tahun)', 'Anak-anak (6–11 Tahun)', 'Remaja (12–18 Tahun)', 'Dewasa (19–59 Tahun)', 'Lansia (60+ Tahun)'];
        $ageValues = [(int)($usiaRaw->usia_bayi_balita ?? 0), (int)($usiaRaw->usia_anak ?? 0), (int)($usiaRaw->usia_remaja ?? 0), (int)($usiaRaw->usia_dewasa ?? 0), (int)($usiaRaw->usia_lansia ?? 0)];

        $statusRows = Penduduk::query()->select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status');
        $statusLabels = ['Aktif', 'Keluar', 'Meninggal'];
        $statusValues = [(int)($statusRows['Aktif'] ?? 0), (int)($statusRows['Keluar'] ?? 0), (int)($statusRows['Meninggal'] ?? 0)];
        $statusTotal = array_sum($statusValues);
        $statusPercentages = array_map(fn($val) => $statusTotal > 0 ? round(($val / $statusTotal) * 100, 1) : 0, $statusValues);

        $educationRaw = (clone $pendudukAktif)
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pendidikan, ''))) IN ('SD','TAMAT SD','SEKOLAH DASAR','SDA','TAMAT SD / SEDERAJAT','TAMAT SD/SEDERAJAT','SD/SEDERAJAT') THEN 1 ELSE 0 END) as tamat_sd")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pendidikan, ''))) IN ('SMP','TAMAT SMP','SEKOLAH MENENGAH PERTAMA') THEN 1 ELSE 0 END) as smp")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pendidikan, ''))) IN ('SMA','SMK','SLTA','TAMAT SMA','SEKOLAH MENENGAH ATAS','MA') THEN 1 ELSE 0 END) as sma")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pendidikan, ''))) IN ('D3','D-3','DIII','DIPLOMA III','DIPLOMA 3','AKADEMI/ DIPLOMA III/S. MUDA','AKADEMI/DIPLOMA III/S. MUDA','AKADEMI / DIPLOMA III / S. MUDA','AKADEMI') THEN 1 ELSE 0 END) as diploma_iii")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pendidikan, ''))) IN ('D4','D-4','DIV','D IV','D.IV','DIPLOMA IV','DIPLOMA 4','DIPLOMA IV/STRATA I','DIPLOMA IV / STRATA I','DIPLOMA IV/ STRATA I','DIPLOMA IV /STRATA I','S1','S-1','S 1','S.1','STRATA I','STRATA 1','SARJANA') THEN 1 ELSE 0 END) as diploma_iv_s1")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pendidikan, ''))) IN ('S2','STRATA II','STRATA 2','MAGISTER') THEN 1 ELSE 0 END) as strata_ii")
            ->first();

        $educationFixedOrder = ['Tamat SD', 'SMP', 'SMA', 'DIPLOMA III', 'DIPLOMA IV/STRATA I', 'STRATA II'];
        $educationValuesMap = ['Tamat SD' => (int)($educationRaw->tamat_sd ?? 0), 'SMP' => (int)($educationRaw->smp ?? 0), 'SMA' => (int)($educationRaw->sma ?? 0), 'DIPLOMA III' => (int)($educationRaw->diploma_iii ?? 0), 'DIPLOMA IV/STRATA I' => (int)($educationRaw->diploma_iv_s1 ?? 0), 'STRATA II' => (int)($educationRaw->strata_ii ?? 0)];
        $educationLabels = $educationFixedOrder;
        $educationValues = array_map(fn($label) => $educationValuesMap[$label] ?? 0, $educationLabels);
        $othersTotal = max(0, $totalPendudukAktif - array_sum($educationValues));
        if ($othersTotal > 0) { $educationLabels[] = 'Lainnya'; $educationValues[] = $othersTotal; }

        $occupationRaw = (clone $pendudukAktif)
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('PELAJAR','PELAJAR/MAHASISWA','PELAJAR / MAHASISWA','PELAJAR/ MAHASISWA','PELAJAR /MAHASISWA','MAHASISWA','PELAJAR/SISWA','SISWA') THEN 1 ELSE 0 END) as pelajar")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('PETANI','PETANI/PETERNAK','PETANI / PETERNAK','PETANI/ PETERNAK','PETANI /PETERNAK','PETERNAK','PETANI/NELAYAN','NELAYAN') THEN 1 ELSE 0 END) as petani")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('IRT','IBU RUMAH TANGGA','IBU RUMAH TANGGA (IRT)','IBU / RUMAH TANGGA','IBU/ RUMAH TANGGA','IBURUNAH TANGGA','IBURUMAHTANGGA') THEN 1 ELSE 0 END) as irt")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('WIRASWASTA','WIRAUSAHA','WIRA SWASTA','WIRA/SWASTA','PENGUSAHA','PEDAGANG','PEDAGANG/WIRASWASTA','PEDAGANG / WIRASWASTA') THEN 1 ELSE 0 END) as wiraswasta")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('GURU','GURU/PENDIDIK','GURU / PENDIDIK','PENDIDIK','TENAGA PENDIDIK') THEN 1 ELSE 0 END) as guru")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('DOSEN','DOSEN/TENAGA PENGAJAR','DOSEN / TENAGA PENGAJAR','TENAGA PENGAJAR','TENAGA PENGAJAR PERGURUAN TINGGI') THEN 1 ELSE 0 END) as dosen")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('PNS','PEGAWAI NEGERI SIPIL','PEGAWAI NEGERI SIPIL (PNS)','PEGAWAI NEGERI','PNS/PEGAWAI NEGERI SIPIL','ASN','APARATUR SIPIL NEGARA','APARATUR SIPIL NEGARA (ASN)') THEN 1 ELSE 0 END) as pns")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('TNI','TENTARA NASIONAL INDONESIA','TENTARA NASIONAL INDONESIA (TNI)','ANGGOTA TNI','TNI/TENTARA NASIONAL INDONESIA','MILITER') THEN 1 ELSE 0 END) as tni")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('POLRI','POLISI','POLISI NEGARA RI','ANGGOTA POLRI','KEPOLISIAN NEGARA RI','POLRI/ANGGOTA POLRI','POLRI / ANGGOTA POLRI') THEN 1 ELSE 0 END) as polri")
            ->first();

        $occupationFixedOrder = ['Pelajar', 'Petani', 'IRT', 'Wiraswasta', 'Guru', 'Dosen', 'PNS', 'TNI', 'POLRI'];
        $occupationValuesMap = ['Pelajar' => (int)($occupationRaw->pelajar ?? 0), 'Petani' => (int)($occupationRaw->petani ?? 0), 'IRT' => (int)($occupationRaw->irt ?? 0), 'Wiraswasta' => (int)($occupationRaw->wiraswasta ?? 0), 'Guru' => (int)($occupationRaw->guru ?? 0), 'Dosen' => (int)($occupationRaw->dosen ?? 0), 'PNS' => (int)($occupationRaw->pns ?? 0), 'TNI' => (int)($occupationRaw->tni ?? 0), 'POLRI' => (int)($occupationRaw->polri ?? 0)];
        $occupationLabels = $occupationFixedOrder; $occupationValues = array_map(fn($label) => $occupationValuesMap[$label] ?? 0, $occupationLabels);
        $occupationOthersTotal = max(0, $totalPendudukAktif - array_sum($occupationValues));
        if ($occupationOthersTotal > 0) { $occupationLabels[] = 'Lainnya'; $occupationValues[] = $occupationOthersTotal; }

        // --- BAGIAN YANG DIPERBAIKI (ANALISIS DINAMIKA) ---
        
        // 1. Ambil data Lahir dan Masuk dari tabel 'dinamika_penduduk'
        $dynamicRows = DB::table('dinamika_penduduk')
            ->where('tahun', $analysisYear)
            ->select('bulan')
            ->selectRaw('SUM(jumlah_lahir) as jumlah_lahir, SUM(jumlah_masuk) as jumlah_masuk')
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        // 2. Ambil data Meninggal dan Keluar dari tabel 'penduduk' berdasarkan filter tahun analisis (menggunakan updated_at)
        $pendudukMeninggalKeluarRows = Penduduk::query()
            ->whereIn('status', ['Meninggal', 'Keluar'])
            ->whereYear('updated_at', $analysisYear) // Memfilter berdasarkan tahun yang dipilih user
            ->select(DB::raw('MONTH(updated_at) as bulan'), 'status', DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('MONTH(updated_at)'), 'status')
            ->get()
            ->groupBy('bulan');

        $bulanLabels = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
        $trendLabels = []; $kelahiranSeries = []; $kematianSeries = []; $migrasiMasukSeries = []; $migrasiKeluarSeries = [];

        for ($m = 1; $m <= 12; $m++) {
            $trendLabels[] = $bulanLabels[$m]; 
            
            // Data Lahir & Masuk (dari dinamika_penduduk)
            $rowDynamic = $dynamicRows->get($m);
            $kelahiranSeries[] = (int) ($rowDynamic->jumlah_lahir ?? 0); 
            $migrasiMasukSeries[] = (int) ($rowDynamic->jumlah_masuk ?? 0); 

            // Data Kematian & Keluar (dari penduduk)
            $rowPenduduk = $pendudukMeninggalKeluarRows->get($m);
            
            $totalMeninggalBulanIni = 0;
            $totalKeluarBulanIni = 0;

            if ($rowPenduduk) {
                $totalMeninggalBulanIni = $rowPenduduk->where('status', 'Meninggal')->sum('total');
                $totalKeluarBulanIni = $rowPenduduk->where('status', 'Keluar')->sum('total');
            }

            $kematianSeries[] = (int) $totalMeninggalBulanIni; 
            $migrasiKeluarSeries[] = (int) $totalKeluarBulanIni;
        }

        // --- AKHIR BAGIAN YANG DIPERBAIKI ---

        $dusunRows = Wilayah::query()->where('tipe', 'dusun')->select('id', 'nama', 'latitude', 'longitude')
            ->selectRaw('(SELECT COUNT(*) FROM penduduk WHERE id_dusun = wilayah.id AND status = "Aktif") as total_aktif')
            ->selectRaw('(SELECT COUNT(*) FROM penduduk WHERE id_dusun = wilayah.id AND status = "Meninggal") as total_meninggal')
            ->selectRaw('(SELECT COUNT(*) FROM penduduk WHERE id_dusun = wilayah.id AND status = "Keluar") as total_keluar')
            ->orderBy('nama')->get();

        $rwRows = Wilayah::query()->where('tipe', 'rw')->whereNotNull('id_dusun')->whereNotNull('nomor_rw')->get(['id_dusun', 'nomor_rw'])->groupBy('id_dusun');
        $rtRows = Wilayah::query()->where('tipe', 'rt')->whereNotNull('id_dusun')->whereNotNull('nomor_rw')->whereNotNull('nomor_rt')->get(['id_dusun', 'nomor_rw', 'nomor_rt']);

        $dusunPopulationRows = $dusunRows->map(function ($row) use ($rwRows, $rtRows) {
            $dusunId = (int) $row->id;
            $rwFromMaster = collect($rwRows->get($dusunId, []))->pluck('nomor_rw')->map(fn($v) => (int)$v);
            $rwFromRt = $rtRows->where('id_dusun', $dusunId)->pluck('nomor_rw')->map(fn($v) => (int)$v);
            $rwNumbers = $rwFromMaster->merge($rwFromRt)->unique()->sort()->values();
            $rwDetail = $rwNumbers->map(function ($rwNumber) use ($rtRows, $dusunId) {
                $rtNumbers = $rtRows->where('id_dusun', $dusunId)->where('nomor_rw', $rwNumber)->pluck('nomor_rt')->map(fn($v) => (int)$v)->unique()->sort()->values();
                return ['nomor_rw' => $rwNumber, 'rt_list' => $rtNumbers->all()];
            })->values()->all();
            return ['id' => $dusunId, 'nama' => $row->nama, 'lat' => $row->latitude !== null ? (float) $row->latitude : null, 'lng' => $row->longitude !== null ? (float) $row->longitude : null, 'total_penduduk' => (int) $row->total_aktif, 'total_meninggal' => (int) $row->total_meninggal, 'total_keluar' => (int) $row->total_keluar, 'rw_detail' => $rwDetail];
        })->values()->all();

        $wilayahStructureRows = collect($dusunPopulationRows)->map(function ($dusun) {
            return ['dusun' => $dusun['nama'], 'rw_list' => collect($dusun['rw_detail'])->pluck('nomor_rw')->all(), 'rw_detail' => collect($dusun['rw_detail'])->map(fn($rw) => ['nomor_rw' => $rw['nomor_rw'], 'jumlah_rt' => count($rw['rt_list']), 'rt_list' => $rw['rt_list']])->all()];
        })->values()->all();

        $rwMapRows = Wilayah::query()->where('tipe', 'rw')->whereNotNull('id_dusun')->whereNotNull('nomor_rw')->get(['id_dusun', 'nomor_rw', 'latitude', 'longitude', 'nama']);
        $rtMapRows = Wilayah::query()->where('tipe', 'rt')->whereNotNull('id_dusun')->whereNotNull('nomor_rw')->whereNotNull('nomor_rt')->get(['id_dusun', 'nomor_rw', 'nomor_rt', 'latitude', 'longitude', 'nama']);
        $coordinatedDusun = collect($dusunPopulationRows)->filter(fn ($row) => $row['lat'] !== null && $row['lng'] !== null)->values();
        $mapCenterLat = $coordinatedDusun->isNotEmpty() ? (float) $coordinatedDusun->avg('lat') : -7.50;
        $mapCenterLng = $coordinatedDusun->isNotEmpty() ? (float) $coordinatedDusun->avg('lng') : 110.50;

        // Data titik pin untuk dusun/rw/rt agar peta sebaran di statistik publik bisa menampilkan pin sesuai tabel wilayah
        $wilayahMapPoints = Wilayah::query()
            ->select(['id','tipe','nama','nomor_rt','nomor_rw','latitude','longitude','id_dusun','luas_wilayah'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('tipe')
            ->orderBy('nama')
            ->get()
            ->map(fn($w) => [
                'id' => (int)$w->id,
                'tipe' => $w->tipe,
                'nama' => $w->nama,
                'nomor_rt' => $w->nomor_rt !== null ? (int)$w->nomor_rt : null,
                'nomor_rw' => $w->nomor_rw !== null ? (int)$w->nomor_rw : null,
                'id_dusun' => $w->id_dusun !== null ? (int)$w->id_dusun : null,
                'latitude' => $w->latitude !== null ? (float)$w->latitude : null,
                'longitude' => $w->longitude !== null ? (float)$w->longitude : null,
                'luas_wilayah' => $w->luas_wilayah !== null ? (float)$w->luas_wilayah : null,
            ])
            ->values()
            ->all();

        return view('masyarakat.statistik', compact(
            'privacyThreshold', 'totalPendudukAktif', 'totalKK', 'totalDusun', 'totalRw', 'totalRt', 
            'totalLuasDesaKm2', 'totalWilayahHa', 'kepadatan', 'genderLabels', 'genderValues', 'genderPercent', 
            'ageLabels', 'ageValues', 'statusLabels', 'statusValues', 'statusPercentages', 'educationLabels', 
            'educationValues', 'occupationLabels', 'occupationValues', 'trendLabels', 'kelahiranSeries', 
            'kematianSeries', 'migrasiMasukSeries', 'migrasiKeluarSeries', 'analysisYear', 'tahunOptions', 
            'dusunPopulationRows', 'wilayahStructureRows', 'mapCenterLat', 'mapCenterLng', 'rwMapRows', 'rtMapRows',
            'wilayahMapPoints'
        ));
    }
}