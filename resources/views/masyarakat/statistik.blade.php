@extends('masyarakat.layout')

@section('title', 'Statistik')

@push('styles')
<style>
    .main-content {
        padding: 0;
        max-width: 100%;
    }

    .stats-hero {
        background: linear-gradient(135deg, rgba(12, 52, 44, 0.96), rgba(7, 102, 83, 0.95));
        padding: 48px 32px 36px;
        color: #fff;
    }

    .stats-hero h1 {
        font-size: 36px;
        margin-bottom: 10px;
        font-weight: 800;
    }

    .stats-hero p {
        max-width: 860px;
        margin: 0;
        color: rgba(255, 255, 255, 0.88);
        font-size: 15px;
        line-height: 1.6;
    }

    .section-wrap {
        background: #f4f9f6;
        padding: 36px 32px;
    }

    .kpi-grid {
        max-width: 1280px;
        margin: 0 auto 32px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 24px;
        align-items: stretch;
    }

    /* KARTU UTAMA - PUTIH BERSIH */
    .kpi-card {
        background: #ffffff; 
        border-radius: 16px;
        padding: 24px;
        display: flex;
        flex-direction: row; 
        align-items: center;
        gap: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        border: 1px solid rgba(0, 0, 0, 0.04);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    /* Wadah Icon Pendukung di dalam Card */
    .kpi-icon-wrapper {
        background: #fcfdfa;
        color: #076653;
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    /* Khusus variasi warna background ikon */
    .kpi-card:nth-child(1) .kpi-icon-wrapper { background-color: #fefde8; color: #eab308; }
    .kpi-card:nth-child(2) .kpi-icon-wrapper { background-color: #f0fdf4; color: #16a34a; }
    .kpi-card:nth-child(3) .kpi-icon-wrapper { background-color: #eff6ff; color: #2563eb; }

    .kpi-content-wrapper {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .kpi-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .kpi-value {
        color: #0f172a;
        font-size: 36px;
        font-weight: 800;
        line-height: 1.1;
    }

    .kpi-sub {
        margin-top: 4px;
        font-size: 12px;
        color: #94a3b8;
    }

    .kpi-card:hover, .panel:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
    }

    /* STRUKTUR LIST INFORMASI WILAYAH */
    .list-wrap {
        margin-top: 16px;
        background: transparent;
    }

    .mini-list {
        margin: 0;
        padding: 0;
        list-style: none;
        font-size: 13px;
        color: #334155;
    }

    .mini-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .mini-list li:last-child {
        border-bottom: none;
    }

    .badge-count {
        background: #fefde8;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        color: #ca8a04;
    }

    /* PANEL GRAFIK & DIAGRAM BAWAH */
    .chart-grid {
        max-width: 1280px;
        margin: 0 auto 24px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 24px;
    }

    .panel {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        border: 1px solid rgba(0, 0, 0, 0.04);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .panel h3 {
        margin: 0 0 4px;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }

    .panel-sub {
        font-size: 12px;
        color: #94a3b8;
        margin-bottom: 20px;
    }

    .panel canvas {
        width: 100% !important;
        max-height: 240px !important;
    }

    /* LAYOUT KHUSUS RINGKASAN STATUS KEPENDUDUKAN */
    .status-summary-box {
        display: flex;
        flex-direction: column;
        gap: 12px;
        height: 100%;
        justify-content: center;
    }

    .status-card-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        border-radius: 12px;
        font-weight: 600;
    }
    .status-card-item.aktif { background: #f0fdf4; color: #15803d; }
    .status-card-item.keluar { background: #fffbeb; color: #b45309; }
    .status-card-item.meninggal { background: #fff5f5; color: #b91c1c; }

    .status-card-item .label-left {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
    }
    
    .status-card-item .label-left .icon-circle {
        width: 32px !important;
        height: 32px !important;
        background: #ffffff !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: none !important;
    }

    .status-card-item .value-right {
        font-size: 20px;
        font-weight: 800;
    }
    .status-card-item .value-right span {
        font-size: 11px;
        font-weight: 400;
        margin-left: 2px;
    }

    /* TABEL DATA & PETA SEBARAN */
    .wide-panel {
        max-width: 1280px;
        margin: 24px auto 0;
    }

    .table-wrap {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        margin-top: 16px;
    }

    .data-table th, .data-table td {
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 16px;
        text-align: left;
    }

    .data-table th {
        color: #475569;
        font-weight: 700;
        background: #f8fafc;
    }

    .text-right {
        text-align: right !important;
    }

    #publicStatMap {
        height: 400px;
        border-radius: 16px;
        margin-top: 20px;
        border: 1px solid #e2e8f0;
        z-index: 1;
    }

    /* CUSTOM LEGEND FOR GENDER CHART */
    .custom-gender-legend {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #64748b;
        margin-top: 16px;
        border-top: 1px solid #f1f5f9;
        padding-top: 12px;
    }
    .custom-gender-legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .legend-val {
        font-weight: 700;
        color: #0f172a;
        margin-left: auto;
    }

    /* KUSTOM PROGRESS BAR GENDER */
    .gender-progress-bar {
        display: flex;
        height: 12px;
        border-radius: 999px;
        overflow: hidden;
        margin-top: 16px;
        background: #f1f5f9;
    }
    .gender-progress-fill {
        height: 100%;
    }

    /* MEDIA QUERIES (RESPONSIVE) */
    @media (max-width: 1024px) {
        .kpi-grid, .chart-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .kpi-grid, .chart-grid {
            grid-template-columns: 1fr;
        }
        .stats-hero {
            padding: 32px 20px;
        }
        .section-wrap {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
<section class="stats-hero">
    <div style="max-width: 1280px; margin: 0 auto; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(227, 239, 38, 0.12); color: #E3EF26; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-bottom: 12px;">
                <i class="fas fa-map-marker-alt"></i> Portal Publik · Desa Sebalor
            </div>
            <h1>Statistik Demografi Desa Sebalor</h1>
            <p>Data ditampilkan dalam bentuk agregat untuk menjaga privasi perorangan. Tidak ada data individu yang ditampilkan.</p>
        </div>
    </div>
</section>

<section class="section-wrap">
    @php
        // Pemetaan struktur RW dinamis per Dusun dari relasi tabel wilayah
        $rwPerDusun = collect($wilayahStructureRows ?? [])->map(function ($row) {
            $rwList = $row['rw_list'] ?? [];
            return [
                'dusun' => $row['dusun'] ?? '-',
                'jumlah_rw' => count($rwList),
                'rw_list' => $rwList,
                'total_jiwa' => $row['total_jiwa'] ?? 0,
                'total_rt' => $row['total_rt'] ?? 0
            ];
        })->values();

        // Hitung persentase rasio jenis kelamin penduduk aktif secara aman
        $totalAktif = (int)($statusValues[0] ?? $totalPendudukAktif ?? 0);
        $totalKeluar = (int)($statusValues[1] ?? 0);
        $totalMeninggal = (int)($statusValues[2] ?? 0);
        
        $totalGender = array_sum($genderValues ?? [0,0]);
        $pctLaki = $genderPercent['L'] ?? 0;
        $pctPerempuan = $genderPercent['P'] ?? 0;
        
        $totalSemuaStatus = $totalAktif + $totalKeluar + $totalMeninggal;
    @endphp

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon-wrapper">
                <i class="fas fa-users"></i>
            </div>
            <div class="kpi-content-wrapper">
                <div class="kpi-label">Total Penduduk Aktif</div>
                <div class="kpi-value">{{ number_format($totalAktif) }} <span style="font-size: 14px; color:#64748b; font-weight: normal;">Jiwa</span></div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-wrapper">
                <i class="fas fa-home"></i>
            </div>
            <div class="kpi-content-wrapper">
                <div class="kpi-label">Total Kepala Keluarga</div>
                <div class="kpi-value">{{ number_format($totalKK ?? 0) }} <span style="font-size: 14px; color:#64748b; font-weight: normal;">KK</span></div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-wrapper">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <div class="kpi-content-wrapper">
                <div class="kpi-label">Luas Wilayah Desa</div>
                <div class="kpi-value">{{ number_format($totalLuasDesaKm2 ?? 0, 2) }} <span style="font-size: 14px; color:#64748b; font-weight: normal;">km²</span></div>
                <div class="kpi-sub">Equivalent: ~{{ number_format($kepadatan ?? 0, 0) }} jiwa/km²</div>
            </div>
        </div>
    </div>

    <div style="max-width: 1280px; margin: 0 auto 20px; display: flex; align-items: center; gap: 16px;">
        <div style="background: #fefde8; color: #eab308; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
            <i class="fas fa-map"></i>
        </div>
        <div style="flex-grow: 1;">
            <h2 style="font-size: 20px; color: #0f172a; font-weight: 800; margin: 0;">Informasi Wilayah</h2>
            <span style="font-size: 13px; color: #64748b;">Struktur administratif desa</span>
        </div>
        <div style="flex-grow: 8; border-bottom: 1px solid #e2e8f0;"></div>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card" style="flex-direction: column; align-items: stretch; justify-content: flex-start;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:32px; height:32px; background:#fefde8; color:#eab308; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:14px;"><i class="fas fa-map-pin"></i></div>
                    <span style="font-weight: 700; color:#0f172a; font-size:14px;">Informasi Dusun</span>
                </div>
                <div style="font-size: 32px; font-weight: 800; color: #eab308;">{{ $totalDusun ?? 0 }}</div>
            </div>
            <div class="kpi-sub" style="margin-bottom: 4px;">Total {{ $totalDusun ?? 0 }} Dusun Terdaftar</div>
            <div class="list-wrap">
                <ul class="mini-list">
                    @forelse($wilayahStructureRows ?? [] as $row)
                        <li>
                            <span>
                                <strong style="color:#0f172a; font-size:14px;">Dusun {{ $row['dusun'] }}</strong>
                                <br><small style="color: #94a3b8;">
                                    RW {{ !empty($row['rw_list']) ? implode(' — RW ', $row['rw_list']) : '-' }} · {{ count($row['rw_detail'] ?? []) }} Kelompok RW
                                </small>
                            </span>
                        </li>
                    @empty
                        <li>Belum ada data dusun.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="kpi-card" style="flex-direction: column; align-items: stretch; justify-content: flex-start;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:32px; height:32px; background:#f0fdf4; color:#16a34a; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:14px;"><i class="fas fa-th-large"></i></div>
                    <span style="font-weight: 700; color:#0f172a; font-size:14px;">Informasi RW</span>
                </div>
                <div style="font-size: 32px; font-weight: 800; color: #16a34a;">{{ $totalRw ?? 0 }}</div>
            </div>
            <div class="kpi-sub" style="margin-bottom: 4px;">Total {{ $totalRw ?? 0 }} RW</div>
            <div class="list-wrap">
                <ul class="mini-list">
                    @forelse($wilayahStructureRows ?? [] as $row)
                        @foreach($row['rw_detail'] ?? [] as $rwDet)
                            <li>
                                <span>Dusun {{ $row['dusun'] ?? '-' }} <strong>RW {{ $rwDet['nomor_rw'] }}</strong></span>
                                <small style="color: #94a3b8;">{{ $rwDet['jumlah_rt'] }} RT Terikat</small>
                            </li>
                        @endforeach
                    @empty
                        <li>Belum ada data RW.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="kpi-card" style="flex-direction: column; align-items: stretch; justify-content: flex-start;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:32px; height:32px; background:#eff6ff; color:#2563eb; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:14px;"><i class="fas fa-home"></i></div>
                    <span style="font-weight: 700; color:#0f172a; font-size:14px;">Informasi RT</span>
                </div>
                <div style="font-size: 32px; font-weight: 800; color: #2563eb;">{{ $totalRt ?? 0 }}</div>
            </div>
            <div class="kpi-sub" style="margin-bottom: 4px;">Total {{ $totalRt ?? 0 }} RT</div>
            <div class="list-wrap">
                <ul class="mini-list" style="font-size: 12px;">
                    @forelse($wilayahStructureRows ?? [] as $row)
                        <li>
                            <span>Dusun {{ $row['dusun'] ?? '-' }}</span>
                            <span class="badge-count" style="background:#eff6ff; color:#2563eb;">{{ count($row['rw_detail'] ?? []) }} Blok RW</span>
                        </li>
                    @empty
                        <li>Belum ada rincian data RT.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div style="max-width: 1280px; margin: 32px auto 20px; display: flex; align-items: center; gap: 16px;">
        <div style="background: #f0fdf4; color: #16a34a; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
            <i class="fas fa-user-friends"></i>
        </div>
        <div style="flex-grow: 1;">
            <h2 style="font-size: 20px; color: #0f172a; font-weight: 800; margin: 0;">Komposisi Penduduk</h2>
            <span style="font-size: 13px; color: #64748b;">Rasio gender dan status kependudukan</span>
        </div>
        <div style="flex-grow: 8; border-bottom: 1px solid #e2e8f0;"></div>
    </div>

    <div class="chart-grid">
        <div class="panel">
            <h3>Rasio Jenis Kelamin</h3>
            <div class="panel-sub">Komposisi gender penduduk aktif</div>
            <div style="position: relative; height: 180px; display:flex; justify-content:center;">
                <canvas id="genderChart"></canvas>
            </div>
            <div class="custom-gender-legend">
                <div class="custom-gender-legend-item">
                    <span class="legend-dot" style="background: #3b82f6;"></span>
                    <span>Laki-laki</span>
                </div>
                <div class="legend-val">{{ number_format($genderValues[0] ?? 0) }} jiwa <span style="color:#3b82f6; font-weight:500; margin-left:4px;">{{ $pctLaki }}%</span></div>
            </div>
            <div class="custom-gender-legend" style="margin-top:4px; border-top:none; padding-top:0;">
                <div class="custom-gender-legend-item">
                    <span class="legend-dot" style="background: #ec4899;"></span>
                    <span>Perempuan</span>
                </div>
                <div class="legend-val">{{ number_format($genderValues[1] ?? 0) }} jiwa <span style="color:#ec4899; font-weight:500; margin-left:4px;">{{ $pctPerempuan }}%</span></div>
            </div>
            <div class="gender-progress-bar">
                <div class="gender-progress-fill" style="width: {{ $pctLaki }}%; background: #3b82f6;"></div>
                <div class="gender-progress-fill" style="width: {{ $pctPerempuan }}%; background: #ec4899;"></div>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:10px; color:#94a3b8; margin-top:4px;">
                <div>Laki-laki {{ $pctLaki }}%</div>
                <div>Perempuan {{ $pctPerempuan }}%</div>
            </div>
        </div>

        <div class="panel">
            <h3>Status Kependudukan</h3>
            <div class="panel-sub">Aktif, keluar & meninggal</div>
            <div style="position: relative; height: 180px; display:flex; justify-content:center;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <div class="panel">
            <h3 style="margin-bottom: 2px;">Status Kependudukan</h3>
            <div class="panel-sub" style="margin-bottom: 16px;">Aktif, keluar & meninggal</div>
            
            <div class="status-summary-box">
                <div class="status-card-item aktif">
                    <div class="label-left">
                        <div class="icon-circle"><i class="fas fa-user-check"></i></div>
                        <span>Aktif</span>
                    </div>
                    <div class="value-right">{{ number_format($totalAktif) }} <span>jiwa</span></div>
                </div>
                
                <div class="status-card-item keluar">
                    <div class="label-left">
                        <div class="icon-circle"><i class="fas fa-sign-out-alt"></i></div>
                        <span>Keluar</span>
                    </div>
                    <div class="value-right">{{ number_format($totalKeluar) }} <span>jiwa</span></div>
                </div>
                
                <div class="status-card-item meninggal">
                    <div class="label-left">
                        <div class="icon-circle"><i class="fas fa-heart-broken"></i></div>
                        <span>Meninggal</span>
                    </div>
                    <div class="value-right">{{ number_format($totalMeninggal) }} <span>jiwa</span></div>
                </div>
            </div>
        </div>
    </div>

    <div style="max-width: 1280px; margin: 32px auto 20px; display: flex; align-items: center; gap: 16px;">
        <div style="background: #eff6ff; color: #2563eb; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
            <i class="fas fa-chart-line"></i>
        </div>
        <div style="flex-grow: 1;">
            <h2 style="font-size: 20px; color: #0f172a; font-weight: 800; margin: 0;">Sosial Ekonomi & Dinamika</h2>
            <span style="font-size: 13px; color: #64748b;">Pendidikan, pekerjaan, and pergerakan penduduk aktif</span>
        </div>
        <div style="flex-grow: 8; border-bottom: 1px solid #e2e8f0;"></div>
    </div>

    <div class="chart-grid">
        <div class="panel">
            <h3>Pendidikan Terakhir</h3>
            <div class="panel-sub">Jenjang pendidikan warga aktif (Agregat)</div>
            <canvas id="educationChart"></canvas>
        </div>
        <div class="panel">
            <h3>Pekerjaan Utama</h3>
            <div class="panel-sub">Jenis pekerjaan warga aktif (Agregat)</div>
            <canvas id="occupationChart"></canvas>
        </div>
        <div class="panel">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:nowrap; margin-bottom: 4px;">
                <div>
                    <h3>Analisis Dinamika Penduduk</h3>
                    <div class="panel-sub" style="margin-bottom:0;">Kelahiran, kematian & migrasi</div>
                </div>
                <form method="GET" action="{{ route('public.statistik') }}" style="display:flex; align-items:center; gap:6px;">
                    <select id="tahun_analisis" name="tahun_analisis" onchange="this.form.submit()" style="padding:4px 8px; border:1px solid #e2e8f0; border-radius:6px; font-size:12px; font-weight:600; background:#f8fafc; color:#334155;">
                        @foreach($tahunOptions ?? [date('Y') => date('Y')] as $value => $label)
                            <option value="{{ $value }}" {{ (int) $value === (int) ($analysisYear ?? date('Y')) ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <canvas id="dynamicsChart" style="margin-top:16px;"></canvas>
        </div>
    </div>

    <div style="max-width: 1280px; margin: 32px auto 20px; display: flex; align-items: center; gap: 16px;">
        <div style="background: #fefde8; color: #eab308; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
            <i class="fas fa-map-marked-alt"></i>
        </div>
        <div style="flex-grow: 1;">
            <h2 style="font-size: 20px; color: #0f172a; font-weight: 800; margin: 0;">Sebaran Wilayah</h2>
            <span style="font-size: 13px; color: #64748b;">Peta persebaran dusun, RW, dan RT</span>
        </div>
        <div style="flex-grow: 8; border-bottom: 1px solid #e2e8f0;"></div>
    </div>

    <div class="panel wide-panel">
        <h3>Sebaran Wilayah (Agregat)</h3>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Dusun</th>
                        <th class="text-right">Total Penduduk Aktif</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dusunPopulationRows ?? [] as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $row['nama'] }}</strong></td>
                            <td class="text-right" style="font-weight:700; color:#076653;">{{ number_format($row['total_penduduk']) }} jiwa</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">Belum ada data dusun.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="publicStatMap"></div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const c = {
        blueGender: '#3b82f6',
        pinkGender: '#ec4899',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444',
        primaryBar: '#6366f1',
        orangeBar: '#f97316',
        purpleBar: '#a855f7'
    };

    // 1. CHART GENDER (Hanya Penduduk Aktif)
    new Chart(document.getElementById('genderChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Laki-laki', 'Perempuan'],
            datasets: [{
                data: @json($genderValues ?? [0, 0]),
                backgroundColor: [c.blueGender, c.pinkGender],
                borderWidth: 4,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: { 
                legend: { display: false } 
            } 
        },
        plugins: [{
            id: 'centerText',
            beforeDraw(chart) {
                const { width, height, ctx } = chart;
                ctx.restore();
                ctx.font = "extrabold 28px sans-serif";
                ctx.textBaseline = "middle";
                ctx.fillStyle = "#0f172a";
                const text = "{{ $totalAktif ?? 0 }}",
                      textX = Math.round((width - ctx.measureText(text).width) / 2),
                      textY = height / 2 - 10;
                ctx.fillText(text, textX, textY);

                ctx.font = "500 12px sans-serif";
                ctx.fillStyle = "#94a3b8";
                const textSub = "Total",
                      textSubX = Math.round((width - ctx.measureText(textSub).width) / 2),
                      textSubY = height / 2 + 14;
                ctx.fillText(textSub, textSubX, textSubY);
                ctx.save();
            }
        }]
    });

    // 2. CHART STATUS KEPENDUDUKAN (Agregat Seluruh Status)
    new Chart(document.getElementById('statusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Aktif', 'Keluar', 'Meninggal'],
            datasets: [{
                data: [{{ $totalAktif ?? 0 }}, {{ $totalKeluar ?? 0 }}, {{ $totalMeninggal ?? 0 }}],
                backgroundColor: [c.success, c.warning, c.danger],
                borderWidth: 4,
                borderColor: '#ffffff'
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: { 
                legend: { display: false }
            } 
        },
        plugins: [{
            id: 'centerTextStatus',
            beforeDraw(chart) {
                const { width, height, ctx } = chart;
                ctx.restore();
                ctx.font = "extrabold 28px sans-serif";
                ctx.textBaseline = "middle";
                ctx.fillStyle = "#0f172a";
                const text = "{{ $totalSemuaStatus ?? 0 }}",
                      textX = Math.round((width - ctx.measureText(text).width) / 2),
                      textY = height / 2 - 10;
                ctx.fillText(text, textX, textY);

                ctx.font = "500 12px sans-serif";
                ctx.fillStyle = "#94a3b8";
                const textSub = "Total",
                      textSubX = Math.round((width - ctx.measureText(textSub).width) / 2),
                      textSubY = height / 2 + 14;
                ctx.fillText(textSub, textSubX, textSubY);
                ctx.save();
            }
        }]
    });

    // 3. CHART PENDIDIKAN (Dinamis - Hanya Penduduk Aktif)
    new Chart(document.getElementById('educationChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($educationLabels ?? []),
            datasets: [{
                label: 'Jumlah',
                data: @json($educationValues ?? []),
                backgroundColor: '#6366f1',
                borderRadius: 6,
                barThickness: 10
            }]
        },
        options: { 
            responsive: true, 
            indexAxis: 'y', 
            plugins: { legend: { display: false } }, 
            scales: { 
                x: { display: false, grid: { display: false } },
                y: { grid: { display: false }, ticks: { font: { weight: 'bold', size: 11 }, color: '#475569' } }
            } 
        },
        plugins: [{
            id: 'valueLabels',
            afterDatasetsDraw(chart) {
                const { ctx, data } = chart;
                ctx.save();
                ctx.font = "bold 12px sans-serif";
                ctx.fillStyle = "#0f172a";
                const meta = chart.getDatasetMeta(0);
                if (meta && meta.data) {
                    meta.data.forEach((bar, index) => {
                        const value = data.datasets[0].data[index];
                        if (value > 0) { 
                            ctx.fillText(value, bar.x + 8, bar.y + 4);
                        }
                    });
                }
            }
        }]
    });

    // 4. CHART PEKERJAAN (Dinamis - Hanya Penduduk Aktif)
    new Chart(document.getElementById('occupationChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($occupationLabels ?? []),
            datasets: [{
                label: 'Jumlah',
                data: @json($occupationValues ?? []),
                backgroundColor: [
                    '#eab308', '#14b8a6', '#3b82f6', '#f97316', '#a855f7', 
                    '#ec4899', '#22c55e', '#f43f5e', '#64748b', '#06b6d4'
                ],
                borderRadius: 6,
                barThickness: 10
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { display: false, grid: { display: false } },
                y: { grid: { display: false }, ticks: { font: { weight: 'bold', size: 11 }, color: '#475569' } }
            }
        },
        plugins: [{
            id: 'valueLabelsOcc',
            afterDatasetsDraw(chart) {
                const { ctx, data } = chart;
                ctx.save();
                ctx.font = "bold 12px sans-serif";
                ctx.fillStyle = "#0f172a";
                const meta = chart.getDatasetMeta(0);
                if (meta && meta.data) {
                    meta.data.forEach((bar, index) => {
                        const value = data.datasets[0].data[index];
                        if (value > 0) {
                            ctx.fillText(value, bar.x + 8, bar.y + 4);
                        }
                    });
                }
            }
        }]
    });

    // 5. CHART DINAMIKA PENDUDUK (Line Chart)
    new Chart(document.getElementById('dynamicsChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [
                {
                    label: 'Kelahiran',
                    data: @json($monthlyBirths ?? array_fill(0, 12, 0)),
                    borderColor: c.success,
                    backgroundColor: 'transparent',
                    tension: 0.3
                },
                {
                    label: 'Kematian',
                    data: @json($monthlyDeaths ?? array_fill(0, 12, 0)),
                    borderColor: c.danger,
                    backgroundColor: 'transparent',
                    tension: 0.3
                },
                {
                    label: 'Migrasi Masuk',
                    data: @json($monthlyMigrateIn ?? array_fill(0, 12, 0)),
                    borderColor: c.blueGender,
                    backgroundColor: 'transparent',
                    tension: 0.3
                },
                {
                    label: 'Migrasi Keluar',
                    data: @json($monthlyMigrateOut ?? array_fill(0, 12, 0)),
                    borderColor: c.warning,
                    backgroundColor: 'transparent',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 11 } }
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 6. INITIALIZATION LEAFLET MAP
    const mapCenter = @json($mapCenterCoordinates ?? [-7.9123, 111.9032]); // Default koordinat Sebalor/Tulungagung
    const map = L.map('publicStatMap').setView(mapCenter, 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Tambahkan markers/polygon dusun jika datanya dikirim dari Controller
    const geojsonData = @json($dusunGeoJson ?? null);
    if(geojsonData) {
        L.geoJSON(geojsonData, {
            style: function(feature) {
                return { color: "#076653", weight: 2, fillOpacity: 0.1 };
            },
            onEachFeature: function(feature, layer) {
                if (feature.properties && feature.properties.name) {
                    layer.bindPopup("<strong>Dusun " + feature.properties.name + "</strong>");
                }
            }
        }).addTo(map);
    }
});
</script>
@endpush