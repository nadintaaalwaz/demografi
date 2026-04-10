@extends('masyarakat.layout')

@section('title', 'Beranda')

@push('styles')
<style>
    .main-content {
        padding: 0;
        max-width: 100%;
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

    .btn-outline {
        background: transparent;
        color: #fff;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .btn-outline:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: #E3EF26;
        color: #E3EF26;
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
        border: 1px solid rgba(227, 239, 38, 0.2);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        background: rgba(12, 52, 44, 0.8);
        border-color: #E3EF26;
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

    /* Main Content Area */
    .content-section {
        background: #f8f9fa;
        padding: 80px 40px;
    }

    .section-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .section-title {
        font-size: 36px;
        font-weight: 800;
        color: #0C342C;
        margin-bottom: 15px;
    }

    .section-subtitle {
        font-size: 16px;
        color: #6b7280;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Charts Grid */
    .charts-container {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
    }

    .chart-card {
        background: #fff;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .chart-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .chart-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f3f4f6;
    }

    .chart-icon {
        width: 40px;
        height: 40px;
        background: #076653;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 18px;
    }

    .chart-title {
        font-size: 18px;
        font-weight: 700;
        color: #0C342C;
    }

    /* Map Section */
    .map-section {
        background: #0C342C;
        padding: 80px 40px;
        scroll-margin-top: 110px;
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
        text-transform: uppercase;
        letter-spacing: 1px;
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
        border: 1px solid rgba(227, 239, 38, 0.1);
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
        border: 1px solid transparent;
    }

    .dusun-item:hover {
        background: rgba(227, 239, 38, 0.1);
        border-color: #E3EF26;
        transform: translateX(5px);
    }

    .dusun-name {
        color: #fff;
        font-weight: 600;
        font-size: 14px;
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
        border-radius: 0 16px 16px 0;
    }

    #publicMap {
        height: 100%;
        min-height: 500px;
    }

    @media (max-width: 968px) {
        .hero-title {
            font-size: 36px;
        }

        .hero-subtitle {
            font-size: 16px;
        }

        .stats-container {
            grid-template-columns: repeat(2, 1fr);
        }

        .charts-container {
            grid-template-columns: 1fr;
        }

        .map-container-wrapper {
            grid-template-columns: 1fr;
        }

        .map-display {
            min-height: 400px;
        }

        .hero-section {
            padding: 60px 20px;
        }

        .content-section,
        .map-section {
            padding: 50px 20px;
        }

        .stats-section {
            padding: 40px 20px;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
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

<!-- Stats Cards -->
<section class="stats-section">
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">{{ number_format($totalPenduduk ?? 0) }}</div>
            <div class="stat-label">Total Penduduk</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-home"></i>
            </div>
            <div class="stat-number">{{ number_format($totalKK ?? 0) }}</div>
            <div class="stat-label">Kepala Keluarga</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-male"></i>
            </div>
            <div class="stat-number">{{ number_format($totalLakiLaki ?? 0) }}</div>
            <div class="stat-label">Laki-laki</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-female"></i>
            </div>
            <div class="stat-number">{{ number_format($totalPerempuan ?? 0) }}</div>
            <div class="stat-label">Perempuan</div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="content-section" id="statistik">
    <div class="section-header">
        <h2 class="section-title">Statistik Kependudukan</h2>
        <p class="section-subtitle">Gambaran visual kependudukan penduduk berdasarkan berbagai kategori</p>
    </div>

    <div class="charts-container">
        <!-- Gender Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-icon">
                    <i class="fas fa-venus-mars"></i>
                </div>
                <h3 class="chart-title">Rasio Gender</h3>
            </div>
            <canvas id="genderChart"></canvas>
        </div>

        <!-- Age Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="chart-title">Kelompok Usia</h3>
            </div>
            <canvas id="ageChart"></canvas>
        </div>

        <!-- Education Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3 class="chart-title">Tingkat Pendidikan</h3>
            </div>
            <canvas id="educationChart"></canvas>
        </div>
    </div>
</section>

<!-- Map Section -->
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
const chartColors = {
    primary: '#076653',
    accent: '#E3EF26',
    success: '#10b981',
    warning: '#f59e0b',
    info: '#3b82f6',
};

// Gender Chart
const genderCtx = document.getElementById('genderChart').getContext('2d');
new Chart(genderCtx, {
    type: 'pie',
    data: {
        labels: ['Laki-laki', 'Perempuan'],
        datasets: [{
            data: @json([(int) ($totalLakiLaki ?? 0), (int) ($totalPerempuan ?? 0)]),
            backgroundColor: [chartColors.primary, chartColors.warning],
            borderWidth: 4,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    font: { size: 13, weight: 'bold' }
                }
            }
        }
    }
});

// Age Chart
const ageCtx = document.getElementById('ageChart').getContext('2d');
new Chart(ageCtx, {
    type: 'bar',
    data: {
        labels: @json($ageLabels ?? []),
        datasets: [{
            label: 'Jumlah',
            data: @json($ageValues ?? []),
            backgroundColor: chartColors.accent,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

// Education Chart
const educationCtx = document.getElementById('educationChart').getContext('2d');
new Chart(educationCtx, {
    type: 'bar',
    data: {
        labels: @json($educationLabels ?? []),
        datasets: [{
            label: 'Jumlah',
            data: @json($educationValues ?? []),
            backgroundColor: '#10b981',
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

// Leaflet Map
const publicMap = L.map('publicMap').setView([{{ (float) ($mapCenterLat ?? -7.50) }}, {{ (float) ($mapCenterLng ?? 110.50) }}], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(publicMap);

// Dusun markers
const dusunData = @json($dusunMapData ?? []);

dusunData.forEach(dusun => {
    if (dusun.lat === null || dusun.lng === null) {
        return;
    }

    const marker = L.marker([dusun.lat, dusun.lng]).addTo(publicMap);
    marker.bindPopup(`
        <div style="font-family: 'Inter', sans-serif; min-width: 180px;">
            <h3 style="margin: 0 0 10px 0; color: #0C342C; font-size: 15px; font-weight: 700;">${dusun.name}</h3>
            <p style="margin: 5px 0; font-size: 13px;"><strong>Total Penduduk:</strong> ${dusun.total_penduduk} jiwa</p>
        </div>
    `);
});

// Dusun list click to zoom
document.querySelectorAll('.dusun-item').forEach(item => {
    item.addEventListener('click', function() {
        const lat = parseFloat(this.dataset.lat);
        const lng = parseFloat(this.dataset.lng);
        publicMap.setView([lat, lng], 15);
    });
});
</script>
@endpush
