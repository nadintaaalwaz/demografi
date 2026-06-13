@extends('masyarakat.layout')

@section('title', 'Beranda')

@push('styles')
<style>
    .main-content {
        padding: 0;
        max-width: 100%;
        background-color: #E1EFED; 
    }

    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, rgba(12, 52, 44, 0.95), rgba(7, 102, 83, 0.95)), 
                    url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 400"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(227,239,38,0.05)" stroke-width="1"/></pattern></defs><rect width="1200" height="400" fill="url(%23grid)"/></svg>');
        padding: 50px 40px 40px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .hero-badge {
        display: inline-block;
        background: rgba(227, 239, 38, 0.15);
        color: #E3EF26;
        padding: 8px 20px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 18px;
        border: 1px solid rgba(227, 239, 38, 0.3);
    }

    .hero-title {
        font-size: 42px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 12px;
        line-height: 1.2;
    }

    .hero-title .highlight {
        color: #E3EF26;
    }

    .hero-subtitle {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.85);
        max-width: 700px;
        margin: 0 auto 30px;
        line-height: 1.6;
    }

    .hero-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        padding: 15px 35px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        border: none;
    }

    .btn-primary {
        background: #E3EF26;
        color: #0C342C;
    }

    .btn-primary:hover {
        background: #d4e017;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(227, 239, 38, 0.4);
    }

    /* Stats Section */
    .stats-section {
        background: #076653;
        padding: 40px 40px;
        margin-top: 0;
        position: relative;
        z-index: 10;
    }

    .stats-container {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .stat-card {
        background: rgba(12, 52, 44, 0.6);
        backdrop-filter: blur(10px);
        padding: 30px;
        border-radius: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 12px 30px rgba(3, 63, 45, 0.06);
        border: none;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        background: rgba(227, 239, 38, 0.15);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #E3EF26;
        font-size: 24px;
        margin-bottom: 15px;
    }

    .stat-number {
        font-size: 36px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.7);
        font-weight: 500;
    }

    /* Content Section (Latar Belakang Luar) */
    .content-section {
        background: #E1EFED; 
        padding: 60px 40px;
    }

    .section-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .section-title {
        font-size: 32px;
        font-weight: 800;
        color: #0c342c; 
        margin-bottom: 10px;
    }

    .section-subtitle {
        font-size: 15px;
        color: #475569; 
        max-width: 600px;
        margin: 0 auto;
    }

    /* Charts Grid Layout */
    .charts-container {
        max-width: 1280px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    /* ==========================================================================
       UPDATE CARD: Menggunakan Hijau Emerald Medium yang Lebih Fresh & Menarik
       ========================================================================== */
    .chart-card {
        background: #0f5341; /* Hijau tidak terlalu tua, segar, kontras tinggi */
        padding: 24px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        flex-direction: column;
        box-shadow: 0 12px 30px rgba(12, 52, 44, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .chart-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 35px rgba(12, 52, 44, 0.25);
    }

    .chart-header {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
        margin-bottom: 24px;
        padding-bottom: 0;
        border-bottom: none;
    }

    .chart-title-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .chart-icon-dark {
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #E3EF26; /* Diganti warna neon agar senada dengan sistem */
        font-size: 16px;
    }

    .chart-title {
        font-size: 18px;
        font-weight: 700;
        color: #fff; 
    }

    .chart-subtitle {
        font-size: 12px;
        color: #a7f3d0; /* Diubah menjadi mint soft agar kontras dengan hijau medium */
        margin-left: 48px;
        margin-top: -12px;
        opacity: 0.8;
    }

    /* Container khusus penyeimbang canvas grafik */
    .chart-box {
        position: relative;
        width: 100%;
        max-width: 210px;
        margin: 0 auto 20px;
    }

    /* Custom Legend Ringkas untuk List Usia & Pendidikan */
    .custom-stat-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-top: auto;
    }

    .custom-stat-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .stat-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        color: #fff; 
    }

    .stat-item-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .stat-item-badge {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .stat-item-label {
        font-weight: 500;
    }

    .stat-item-sublabel {
        color: #cbd5e1; /* Menggunakan abu terang */
        font-size: 11px;
    }

    .stat-item-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stat-value-num {
        font-weight: 700;
    }

    .stat-value-pct {
        font-size: 11px;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 10px;
        min-width: 32px;
        text-align: center;
    }

    /* Progress Bar custom mini ala gambar */
    .stat-progress-bg {
        width: 100%;
        height: 6px;
        background: rgba(255, 255, 255, 0.1); /* Dipertajam sedikit */
        border-radius: 10px;
        overflow: hidden;
    }

    .stat-progress-bar {
        height: 100%;
        border-radius: 10px;
        transition: width 0.5s ease;
    }

    /* Double Bar Horisontal Terbawah khusus Gender */
    .gender-double-bar {
        width: 100%;
        height: 12px;
        display: flex;
        border-radius: 6px;
        overflow: hidden;
        margin-top: 15px;
    }
    .gender-bar-l { background: #38bdf8; } /* Menggunakan cyan yang lebih cerah ceria */
    .gender-bar-p { background: #f472b6; } /* Menggunakan pink cerah ceria */
    
    .gender-bar-label-container {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        color: #cbd5e1;
        margin-top: 6px;
    }

    /* Highlight Summary Terbanyak Pendidikan */
    .edu-summary-box {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
    }
    .edu-summary-title { color: #cbd5e1; }
    .edu-summary-value { color: #E3EF26; font-weight: 700; } /* Diselaraskan dengan warna neon */

    /* Map Section */
    .map-section {
        background: #0C342C;
        padding: 80px 40px;
    }

    .map-section .section-title,
    .map-section .section-subtitle {
        color: #fff;
    }

    .map-badge {
        display: inline-block;
        background: rgba(227, 239, 38, 0.15);
        color: #E3EF26;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .map-container-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 30px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 16px;
        overflow: hidden;
    }

    .dusun-list {
        background: rgba(12, 52, 44, 0.5);
        padding: 30px;
    }

    .dusun-item {
        padding: 18px 20px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .dusun-item:hover {
        background: rgba(227, 239, 38, 0.1);
        border-color: #E3EF26;
    }

    .dusun-name {
        color: #fff;
        font-weight: 600;
    }

    .dusun-count {
        background: #E3EF26;
        color: #0C342C;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .map-display {
        min-height: 500px;
        background: #e5e7eb;
    }

    #publicMap {
        height: 100%;
        min-height: 500px;
    }

    /* Responsif Breakpoint */
    @media (max-width: 1024px) {
        .charts-container {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .stats-container {
            grid-template-columns: repeat(2, 1fr);
        }
        .map-container-wrapper {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <section class="hero-section">
        <div class="hero-badge">Selamat Datang di Portal Kami</div>
        <h1 class="hero-title">
            Informasi Data Demografi<br>
            <span class="highlight">Kependudukan Desa Sebalor</span>
        </h1>
        <p class="hero-subtitle">
            Platform informasi profil yang menghadirkan data demografi, statistik perkembangan, dan 
            peta wilayah desa untuk keperluan dan riset bersama semua warga desa.
        </p>
        <div class="hero-buttons">
            <a href="#statistik" class="btn btn-primary">
                <i class="fas fa-chart-bar"></i>
                Pelajari Data
            </a>
        </div>
    </section>

    <section class="stats-section">
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number">{{ number_format($totalPenduduk ?? 0) }}</div>
                <div class="stat-label">Total Penduduk</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-home"></i></div>
                <div class="stat-number">{{ number_format($totalKK ?? 0) }}</div>
                <div class="stat-label">Kepala Keluarga</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-male"></i></div>
                <div class="stat-number">{{ number_format($totalLakiLaki ?? 0) }}</div>
                <div class="stat-label">Laki-laki</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-female"></i></div>
                <div class="stat-number">{{ number_format($totalPerempuan ?? 0) }}</div>
                <div class="stat-label">Perempuan</div>
            </div>
        </div>
    </section>

    <section class="content-section" id="statistik">
        <div class="section-header">
            <h2 class="section-title">Statistik Kependudukan</h2>
            <p class="section-subtitle">Gambaran visual kependudukan penduduk berdasarkan berbagai kategori</p>
        </div>

        <div class="charts-container">
            
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title-wrapper">
                        <div class="chart-icon-dark"><i class="fas fa-users"></i></div>
                        <h3 class="chart-title">Rasio Gender</h3>
                    </div>
                </div>
                <p class="chart-subtitle">Komposisi jenis kelamin</p>
                
                <div class="chart-box">
                    <canvas id="genderChart"></canvas>
                </div>
                
                <div class="custom-stat-list">
                    <div class="custom-stat-item">
                        <div class="stat-item-header">
                            <div class="stat-item-left">
                                <div class="stat-item-badge" style="background: #38bdf8;"></div>
                                <span class="stat-item-label">Laki-laki</span>
                            </div>
                            <div class="stat-item-right">
                                <span class="stat-value-num">{{ number_format($totalLakiLaki ?? 0) }}</span>
                                @php 
                                    $pctLaki = ($totalPenduduk ?? 0) > 0 ? round(($totalLakiLaki / $totalPenduduk) * 100) : 0; 
                                    $pctPerempuan = ($totalPenduduk ?? 0) > 0 ? round(($totalPerempuan / $totalPenduduk) * 100) : 100 - $pctLaki;
                                @endphp
                                <span class="stat-value-pct" style="color: #38bdf8; background: rgba(56,189,248,0.15);">{{ $pctLaki }}%</span>
                            </div>
                        </div>
                        <div class="stat-progress-bg">
                            <div class="stat-progress-bar" style="width: {{ $pctLaki }}%; background: #38bdf8;"></div>
                        </div>
                    </div>
                    <div class="custom-stat-item">
                        <div class="stat-item-header">
                            <div class="stat-item-left">
                                <div class="stat-item-badge" style="background: #f472b6;"></div>
                                <span class="stat-item-label">Perempuan</span>
                            </div>
                            <div class="stat-item-right">
                                <span class="stat-value-num">{{ number_format($totalPerempuan ?? 0) }}</span>
                                <span class="stat-value-pct" style="color: #f472b6; background: rgba(244,114,182,0.15);">{{ $pctPerempuan }}%</span>
                            </div>
                        </div>
                        <div class="stat-progress-bg">
                            <div class="stat-progress-bar" style="width: {{ $pctPerempuan }}%; background: #f472b6;"></div>
                        </div>
                    </div>
                </div>

                <div class="gender-double-bar">
                    <div class="gender-bar-l" style="width: {{ $pctLaki }}%"></div>
                    <div class="gender-bar-p" style="width: {{ $pctPerempuan }}%"></div>
                </div>
                <div class="gender-bar-label-container">
                    <span>Laki-laki {{ $pctLaki }}%</span>
                    <span>Perempuan {{ $pctPerempuan }}%</span>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title-wrapper">
                        <div class="chart-icon-dark"><i class="fas fa-briefcase"></i></div>
                        <h3 class="chart-title">Kelompok Usia</h3>
                    </div>
                </div>
                <p class="chart-subtitle">Distribusi umur penduduk</p>
                
                <div class="custom-stat-list" style="margin-top: 10px;">
                    @php
                        // Memilih warna pie/bar chart usia yang lebih cerah agar dinamis di background medium
                        $colorsUsia = ['#fb923c', '#c084fc', '#60a5fa', '#facc15', '#4ade80', '#94a3b8'];
                    @endphp
                    @forelse(($ageLabels ?? []) as $index => $label)
                        @php
                            $val = $ageValues[$index] ?? 0;
                            $pct = ($totalPenduduk ?? 0) > 0 ? round(($val / $totalPenduduk) * 100) : 0;
                            $color = $colorsUsia[$index % count($colorsUsia)];
                            
                            preg_match('/(.*?)(\(.*?\))/i', $label, $matches);
                            $titleClean = isset($matches[1]) ? trim($matches[1]) : $label;
                            $subClean = isset($matches[2]) ? trim($matches[2], '()') : '';
                        @endphp
                        <div class="custom-stat-item">
                            <div class="stat-item-header">
                                <div class="stat-item-left">
                                    <div class="stat-item-badge" style="background: {{ $color }};"></div>
                                    <span class="stat-item-label">{{ $titleClean }} <span class="stat-item-sublabel">{{ $subClean }}</span></span>
                                </div>
                                <div class="stat-item-right">
                                    <span class="stat-value-num">{{ number_format($val) }}</span>
                                    <span class="stat-value-pct" style="color: {{ $color }}; background: rgba(255,255,255,0.05);">{{ $pct }}%</span>
                                </div>
                            </div>
                            <div class="stat-progress-bg">
                                <div class="stat-progress-bar" style="width: {{ $pct }}%; background: {{ $color }};"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">Data tidak ditemukan</p>
                    @endforelse
                </div>

                <div class="edu-summary-box" style="border-top: 1px dashed rgba(255,255,255,0.1); margin-top: auto; padding-top:15px;">
                    <span class="edu-summary-title">Total Penduduk</span>
                    <span class="edu-summary-value" style="color:#fff;">{{ number_format($totalPenduduk ?? 0) }} jiwa</span>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title-wrapper">
                        <div class="chart-icon-dark"><i class="fas fa-graduation-cap"></i></div>
                        <h3 class="chart-title">Tingkat Pendidikan</h3>
                    </div>
                </div>
                <p class="chart-subtitle">Jenjang pendidikan warga</p>
                
                <div class="chart-box">
                    <canvas id="educationChart"></canvas>
                </div>
                
                <div class="custom-stat-list">
                    @php
                        // Palet pendidikan yang disesuaikan
                        $colorsEdu = ['#cbd5e1', '#94a3b8', '#60a5fa', '#2dd4bf', '#facc15', '#fb923c', '#c084fc'];
                        $highestVal = 0;
                        $highestLabel = '-';
                    @endphp
                    @foreach(($educationLabels ?? []) as $index => $label)
                        @php
                            $val = $educationValues[$index] ?? 0;
                            $pct = ($totalPenduduk ?? 0) > 0 ? round(($val / $totalPenduduk) * 100) : 0;
                            $color = $colorsEdu[$index % count($colorsEdu)];
                            
                            if($val > $highestVal) {
                                $highestVal = $val;
                                $highestLabel = $label;
                            }
                        @endphp
                        <div class="custom-stat-item">
                            <div class="stat-item-header">
                                <div class="stat-item-left">
                                    <div class="stat-item-badge" style="background: {{ $color }};"></div>
                                    <span class="stat-item-label">{{ $label }}</span>
                                </div>
                                <div class="stat-item-right">
                                    <span class="stat-value-pct" style="color: #fff; background: transparent; padding:0;">{{ $pct }}%</span>
                                </div>
                            </div>
                            <div class="stat-progress-bg" style="height:4px;">
                                <div class="stat-progress-bar" style="width: {{ $pct }}%; background: {{ $color }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="edu-summary-box">
                    <span class="edu-summary-title">Terbanyak</span>
                    <span class="edu-summary-value">{{ $highestLabel }}</span>
                </div>
            </div>

        </div>
    </section>

    <section class="map-section" id="peta-wilayah">
        <div class="section-header">
            <span class="map-badge">GEOSPASIAL</span>
            <h2 class="section-title">Peta Wilayah Desa</h2>
            <p class="section-subtitle">Peta persebaran wilayah & distribusi penduduk setiap dusun</p>
        </div>

        <div class="map-container-wrapper">
            <div class="dusun-list">
                @forelse(($dusunMapData ?? []) as $dusun)
                    <div class="dusun-item" data-lat="{{ $dusun['lat'] ?? $mapCenterLat ?? -7.50 }}" data-lng="{{ $dusun['lng'] ?? $mapCenterLng ?? 110.50 }}">
                        <span class="dusun-name"><i class="fas fa-map-marker-alt" style="margin-right: 8px; color: #E3EF26;"></i>{{ $dusun['name'] }}</span>
                        <span class="dusun-count">{{ number_format($dusun['total_penduduk']) }} jiwa</span>
                    </div>
                @empty
                    <div class="dusun-item" data-lat="{{ $mapCenterLat ?? -7.50 }}" data-lng="{{ $mapCenterLng ?? 110.50 }}">
                        <span class="dusun-name"><i class="fas fa-map-marker-alt" style="margin-right: 8px; color: #E3EF26;"></i>Belum ada data dusun</span>
                        <span class="dusun-count">0 jiwa</span>
                    </div>
                @endforelse
            </div>
            <div class="map-display">
                <div id="publicMap"></div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
const totalWarga = {{ (int)($totalPenduduk ?? 0) }};

// GENDER CHART
const genderCtx = document.getElementById('genderChart').getContext('2d');
new Chart(genderCtx, {
    type: 'doughnut',
    data: {
        labels: ['Laki-laki', 'Perempuan'],
        datasets: [{
            data: @json([(int) ($totalLakiLaki ?? 0), (int) ($totalPerempuan ?? 0)]),
            backgroundColor: ['#38bdf8', '#f472b6'], // Warna chart diperbarui
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        cutout: '78%',
        plugins: {
            legend: { display: false },
            tooltip: { enabled: true }
        },
        animation: {
            onComplete: function(chart) {
                const ctx = chart.chart.ctx;
                const width = chart.chart.width;
                const height = chart.chart.height;
                ctx.restore();
                
                ctx.font = "bold 20px sans-serif";
                ctx.textBaseline = "middle";
                ctx.fillStyle = "#ffffff"; 
                const textNum = totalWarga.toLocaleString('id-ID'),
                      textNumX = Math.round((width - ctx.measureText(textNum).width) / 2),
                      textNumY = Math.round(height / 2) - 8;
                ctx.fillText(textNum, textNumX, textNumY);

                ctx.font = "500 11px sans-serif";
                ctx.fillStyle = "#a7f3d0"; // Menggunakan warna mint agar kontras di dalam chart donut
                const textSub = "Total Jiwa",
                      textSubX = Math.round((width - ctx.measureText(textSub).width) / 2),
                      textSubY = Math.round(height / 2) + 12;
                ctx.fillText(textSub, textSubX, textSubY);
                
                ctx.save();
            }
        }
    }
});

// EDUCATION CHART
const educationCtx = document.getElementById('educationChart').getContext('2d');
const colorsEduList = ['#cbd5e1', '#94a3b8', '#60a5fa', '#2dd4bf', '#facc15', '#fb923c', '#c084fc'];

new Chart(educationCtx, {
    type: 'doughnut',
    data: {
        labels: @json($educationLabels ?? []),
        datasets: [{
            data: @json($educationValues ?? []),
            backgroundColor: colorsEduList,
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        cutout: '78%',
        plugins: {
            legend: { display: false },
            tooltip: { enabled: true }
        },
        animation: {
            onComplete: function(chart) {
                const ctx = chart.chart.ctx;
                const width = chart.chart.width;
                const height = chart.chart.height;
                ctx.restore();
                
                ctx.font = "bold 20px sans-serif";
                ctx.textBaseline = "middle";
                ctx.fillStyle = "#ffffff"; 
                const textNum = totalWarga.toLocaleString('id-ID'),
                      textNumX = Math.round((width - ctx.measureText(textNum).width) / 2),
                      textNumY = Math.round(height / 2) - 8;
                ctx.fillText(textNum, textNumX, textNumY);

                ctx.font = "500 11px sans-serif";
                ctx.fillStyle = "#a7f3d0"; 
                const textSub = "Orang",
                      textSubX = Math.round((width - ctx.measureText(textSub).width) / 2),
                      textSubY = Math.round(height / 2) + 12;
                ctx.fillText(textSub, textSubX, textSubY);
                
                ctx.save();
            }
        }
    }
});

const publicMap = L.map('publicMap').setView([{{ (float) ($mapCenterLat ?? -7.50) }}, {{ (float) ($mapCenterLng ?? 110.50) }}], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(publicMap);

function createYellowDusunPin() {
    return L.divIcon({
        html: `
            <svg width="40" height="52" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 0C5.37258 0 0 5.37258 0 12C0 20 12 32 12 32C12 32 24 20 24 12C24 5.37258 18.6274 0 12 0Z" fill="#FCD34D"/>
                <circle cx="12" cy="12" r="4" fill="#fff"/>
            </svg>
        `,
        iconSize: [40, 52],
        iconAnchor: [20, 52],
        popupAnchor: [0, -52],
        className: 'custom-dusun-pin'
    });
}

const dusunData = @json($dusunMapData ?? []);
dusunData.forEach(dusun => {
    if (dusun.lat === null || dusun.lng === null) return;

    const marker = L.marker([dusun.lat, dusun.lng], { icon: createYellowDusunPin() }).addTo(publicMap);
    marker.bindPopup(`
        <div style="font-family: 'Inter', sans-serif; min-width: 180px;">
            <h3 style="margin: 0 0 10px 0; color: #0C342C; font-size: 15px; font-weight: 700;">${dusun.name}</h3>
            <p style="margin: 5px 0; font-size: 13px;"><strong>Total Penduduk:</strong> ${dusun.total_penduduk} jiwa</p>
        </div>
    `);
});

document.querySelectorAll('.dusun-item').forEach(item => {
    item.addEventListener('click', function() {
        const lat = parseFloat(this.dataset.lat);
        const lng = parseFloat(this.dataset.lng);
        publicMap.setView([lat, lng], 15);
    });
});
</script>
@endpush