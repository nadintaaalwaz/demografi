@extends('masyarakat.layout')

@section('title', 'Beranda')

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #0C342C 0%, #076653 100%);
        color: #fff;
        padding: 80px 0;
        text-align: center;
        margin: -40px -40px 50px -40px;
    }

    .hero-content h1 {
        font-size: 42px;
        font-weight: 700;
        margin-bottom: 15px;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .hero-content p {
        font-size: 18px;
        opacity: 0.95;
        max-width: 700px;
        margin: 0 auto 30px;
        line-height: 1.6;
    }

    .hero-stats {
        display: flex;
        justify-content: center;
        gap: 50px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .hero-stat-item {
        text-align: center;
    }

    .hero-stat-item h3 {
        font-size: 48px;
        font-weight: 700;
        color: #E3EF26;
        margin-bottom: 5px;
    }

    .hero-stat-item p {
        font-size: 14px;
        opacity: 0.9;
    }

    .section-title {
        font-size: 32px;
        font-weight: 700;
        color: #0C342C;
        text-align: center;
        margin-bottom: 40px;
    }

    .section-subtitle {
        text-align: center;
        color: #6b7280;
        margin-bottom: 50px;
        font-size: 16px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 60px;
    }

    .stat-card {
        background: #fff;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        text-align: center;
        transition: all 0.3s ease;
        border-top: 4px solid #076653;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .stat-card i {
        font-size: 48px;
        color: #076653;
        margin-bottom: 20px;
    }

    .stat-card h3 {
        font-size: 40px;
        font-weight: 700;
        color: #0C342C;
        margin-bottom: 10px;
    }

    .stat-card p {
        font-size: 15px;
        color: #6b7280;
        font-weight: 500;
    }

    .charts-section {
        margin-bottom: 60px;
    }

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
        gap: 30px;
    }

    .chart-card {
        background: #fff;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .chart-header {
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f3f4f6;
    }

    .chart-title {
        font-size: 20px;
        font-weight: 700;
        color: #0C342C;
    }

    .map-section {
        margin-bottom: 60px;
    }

    .map-container {
        background: #fff;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    #publicMap {
        height: 500px;
        border-radius: 12px;
        margin-top: 25px;
    }

    .profil-section {
        background: #f9fafb;
        padding: 60px 40px;
        margin: 60px -40px -40px -40px;
        border-radius: 20px 20px 0 0;
    }

    .profil-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .profil-card {
        background: #fff;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
    }

    .profil-card i {
        font-size: 40px;
        color: #076653;
        margin-bottom: 20px;
    }

    .profil-card h3 {
        font-size: 18px;
        font-weight: 700;
        color: #0C342C;
        margin-bottom: 12px;
    }

    .profil-card p {
        font-size: 14px;
        color: #6b7280;
        line-height: 1.7;
    }

    @media (max-width: 768px) {
        .hero-section {
            padding: 50px 20px;
            margin: -20px -20px 30px -20px;
        }

        .hero-content h1 {
            font-size: 28px;
        }

        .hero-content p {
            font-size: 15px;
        }

        .hero-stats {
            gap: 30px;
        }

        .hero-stat-item h3 {
            font-size: 36px;
        }

        .section-title {
            font-size: 24px;
        }

        .stats-grid,
        .charts-grid,
        .profil-grid {
            grid-template-columns: 1fr;
        }

        .profil-section {
            padding: 40px 20px;
            margin: 40px -20px -20px -20px;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-content">
        <h1>Sistem Informasi Demografi Desa Sebalor</h1>
        <p>Menyajikan data kependudukan yang akurat, transparan, dan mudah diakses untuk masyarakat Desa Sebalor</p>
        
        <div class="hero-stats">
            <div class="hero-stat-item">
                <h3>{{ number_format($totalPenduduk ?? 1450) }}</h3>
                <p>Total Penduduk</p>
            </div>
            <div class="hero-stat-item">
                <h3>{{ $totalDusun ?? 5 }}</h3>
                <p>Dusun</p>
            </div>
            <div class="hero-stat-item">
                <h3>{{ number_format($kepadatan ?? 245) }}</h3>
                <p>Jiwa/km²</p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<section class="stats-section">
    <h2 class="section-title">Statistik Penduduk Desa Sebalor</h2>
    <p class="section-subtitle">Data demografi terkini berdasarkan hasil pendataan terbaru</p>

    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-male"></i>
            <h3>{{ number_format($totalLakiLaki ?? 754) }}</h3>
            <p>Laki-laki (52%)</p>
        </div>

        <div class="stat-card">
            <i class="fas fa-female"></i>
            <h3>{{ number_format($totalPerempuan ?? 696) }}</h3>
            <p>Perempuan (48%)</p>
        </div>

        <div class="stat-card">
            <i class="fas fa-child"></i>
            <h3>{{ number_format($totalBalita ?? 145) }}</h3>
            <p>Balita (0-5 Tahun)</p>
        </div>

        <div class="stat-card">
            <i class="fas fa-user-friends"></i>
            <h3>{{ number_format($totalProduktif ?? 980) }}</h3>
            <p>Usia Produktif</p>
        </div>

        <div class="stat-card">
            <i class="fas fa-user-clock"></i>
            <h3>{{ number_format($totalLansia ?? 325) }}</h3>
            <p>Lansia (>60 Tahun)</p>
        </div>

        <div class="stat-card">
            <i class="fas fa-home"></i>
            <h3>{{ number_format($totalKK ?? 420) }}</h3>
            <p>Kepala Keluarga</p>
        </div>
    </div>
</section>

<!-- Charts Section -->
<section class="charts-section">
    <h2 class="section-title">Visualisasi Data Demografi</h2>
    
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">Distribusi Gender</h3>
            </div>
            <canvas id="genderChart"></canvas>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">Distribusi Usia</h3>
            </div>
            <canvas id="ageChart"></canvas>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">Tingkat Pendidikan</h3>
            </div>
            <canvas id="educationChart"></canvas>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">Jenis Pekerjaan</h3>
            </div>
            <canvas id="occupationChart"></canvas>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section">
    <h2 class="section-title">Peta Persebaran Penduduk Per Dusun</h2>
    
    <div class="map-container">
        <div id="publicMap"></div>
    </div>
</section>

<!-- Profil Desa Section -->
<section class="profil-section">
    <h2 class="section-title">Profil Desa Sebalor</h2>
    
    <div class="profil-grid">
        <div class="profil-card">
            <i class="fas fa-bullseye"></i>
            <h3>Visi Desa</h3>
            <p>Mewujudkan Desa Sebalor yang maju, mandiri, sejahtera, dan berkeadilan berdasarkan nilai-nilai kearifan lokal.</p>
        </div>

        <div class="profil-card">
            <i class="fas fa-map"></i>
            <h3>Luas Wilayah</h3>
            <p>Luas wilayah Desa Sebalor adalah 5.9 km² yang terdiri dari 5 dusun dengan berbagai potensi alam dan budaya.</p>
        </div>

        <div class="profil-card">
            <i class="fas fa-landmark"></i>
            <h3>Batas Wilayah</h3>
            <p>Utara: Desa Utara, Selatan: Desa Selatan, Timur: Desa Timur, Barat: Desa Barat</p>
        </div>

        <div class="profil-card">
            <i class="fas fa-seedling"></i>
            <h3>Potensi Desa</h3>
            <p>Pertanian, perkebunan, peternakan, dan pengembangan ekonomi kreatif berbasis kearifan lokal.</p>
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
    secondary: '#0C342C',
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
            data: [754, 696],
            backgroundColor: [chartColors.primary, chartColors.warning],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    font: {
                        size: 13,
                        weight: 'bold'
                    }
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
        labels: ['0-5 Tahun', '6-12 Tahun', '13-17 Tahun', '18-60 Tahun', '>60 Tahun'],
        datasets: [{
            label: 'Jumlah Penduduk',
            data: [145, 210, 180, 590, 325],
            backgroundColor: chartColors.primary,
            borderRadius: 10
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Education Chart
const educationCtx = document.getElementById('educationChart').getContext('2d');
new Chart(educationCtx, {
    type: 'bar',
    data: {
        labels: ['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2/S3'],
        datasets: [{
            label: 'Jumlah',
            data: [430, 320, 380, 120, 170, 30],
            backgroundColor: [
                chartColors.primary,
                chartColors.success,
                chartColors.info,
                chartColors.warning,
                '#6366f1',
                '#8b5cf6'
            ],
            borderRadius: 10
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Occupation Chart
const occupationCtx = document.getElementById('occupationChart').getContext('2d');
new Chart(occupationCtx, {
    type: 'doughnut',
    data: {
        labels: ['Petani', 'PNS', 'Wiraswasta', 'Buruh', 'Guru', 'Pedagang', 'Lainnya'],
        datasets: [{
            data: [520, 150, 240, 280, 110, 90, 160],
            backgroundColor: [
                chartColors.primary,
                chartColors.success,
                chartColors.info,
                chartColors.warning,
                '#6366f1',
                '#8b5cf6',
                '#ec4899'
            ],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});

// Leaflet Map
const publicMap = L.map('publicMap').setView([-7.50, 110.50], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(publicMap);

// Dusun markers
const dusunData = [
    { name: 'Dusun Krajan', lat: -7.50, lng: 110.50, penduduk: 500, lakiLaki: 260, perempuan: 240 },
    { name: 'Dusun Jati', lat: -7.51, lng: 110.51, penduduk: 450, lakiLaki: 234, perempuan: 216 },
    { name: 'Dusun Mawar', lat: -7.49, lng: 110.49, penduduk: 200, lakiLaki: 104, perempuan: 96 },
    { name: 'Dusun Melati', lat: -7.50, lng: 110.52, penduduk: 180, lakiLaki: 94, perempuan: 86 },
    { name: 'Dusun Anggrek', lat: -7.52, lng: 110.50, penduduk: 120, lakiLaki: 62, perempuan: 58 },
];

dusunData.forEach(dusun => {
    const marker = L.marker([dusun.lat, dusun.lng]).addTo(publicMap);
    marker.bindPopup(`
        <div style="font-family: 'Segoe UI', sans-serif; min-width: 200px;">
            <h3 style="margin: 0 0 12px 0; color: #0C342C; font-size: 16px; border-bottom: 2px solid #076653; padding-bottom: 8px;">${dusun.name}</h3>
            <p style="margin: 6px 0; font-size: 13px;"><strong>Total Penduduk:</strong> ${dusun.penduduk} jiwa</p>
            <p style="margin: 6px 0; font-size: 13px;"><strong>Laki-laki:</strong> ${dusun.lakiLaki} (${Math.round(dusun.lakiLaki/dusun.penduduk*100)}%)</p>
            <p style="margin: 6px 0; font-size: 13px;"><strong>Perempuan:</strong> ${dusun.perempuan} (${Math.round(dusun.perempuan/dusun.penduduk*100)}%)</p>
        </div>
    `);
});
</script>
@endpush
