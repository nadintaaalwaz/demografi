@extends('kasi.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Desa Sebalor')

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: #fff;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .stat-card.primary {
        border-left-color: #076653;
    }

    .stat-card.success {
        border-left-color: #10b981;
    }

    .stat-card.warning {
        border-left-color: #f59e0b;
    }

    .stat-card.info {
        border-left-color: #3b82f6;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: #fff;
    }

    .stat-icon.primary {
        background: linear-gradient(135deg, #076653, #0C342C);
    }

    .stat-icon.success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .stat-icon.warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .stat-icon.info {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .stat-content h3 {
        font-size: 32px;
        font-weight: 700;
        color: #0C342C;
        margin-bottom: 5px;
    }

    .stat-content p {
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
    }

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .chart-card {
        background: #fff;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f3f4f6;
    }

    .chart-title {
        font-size: 18px;
        font-weight: 700;
        color: #0C342C;
    }

    .chart-menu {
        color: #6b7280;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .chart-menu:hover {
        color: #076653;
    }

    .map-container {
        background: #fff;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 40px;
    }

    #map {
        height: 500px;
        border-radius: 12px;
        margin-top: 20px;
    }

    .recent-activity {
        background: #fff;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .activity-item {
        display: flex;
        align-items: start;
        gap: 15px;
        padding: 15px;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.3s ease;
    }

    .activity-item:hover {
        background: #f9fafb;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .activity-icon.upload {
        background: #dbeafe;
        color: #1e40af;
    }

    .activity-icon.edit {
        background: #fef3c7;
        color: #92400e;
    }

    .activity-icon.delete {
        background: #fee2e2;
        color: #991b1b;
    }

    .activity-content h4 {
        font-size: 14px;
        font-weight: 600;
        color: #0C342C;
        margin-bottom: 3px;
    }

    .activity-content p {
        font-size: 12px;
        color: #6b7280;
    }

    .activity-time {
        font-size: 11px;
        color: #9ca3af;
        margin-left: auto;
        flex-shrink: 0;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .charts-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-icon primary">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <h3>{{ number_format($totalPenduduk ?? 0) }}</h3>
            <p>Total Penduduk</p>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-icon success">
            <i class="fas fa-male"></i>
        </div>
        <div class="stat-content">
            <h3>{{ number_format($totalLakiLaki ?? 0) }}</h3>
            <p>Laki-laki ({{ $persenLakiLaki ?? 0 }}%)</p>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-icon warning">
            <i class="fas fa-female"></i>
        </div>
        <div class="stat-content">
            <h3>{{ number_format($totalPerempuan ?? 0) }}</h3>
            <p>Perempuan ({{ $persenPerempuan ?? 0 }}%)</p>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-icon info">
            <i class="fas fa-child"></i>
        </div>
        <div class="stat-content">
            <h3>{{ number_format($totalBalita ?? 0) }}</h3>
            <p>Balita (0-5 Tahun)</p>
        </div>
    </div>

    <div class="stat-card primary">
        <div class="stat-icon primary">
            <i class="fas fa-user-friends"></i>
        </div>
        <div class="stat-content">
            <h3>{{ number_format($totalProduktif ?? 0) }}</h3>
            <p>Usia Produktif</p>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-icon warning">
            <i class="fas fa-user-clock"></i>
        </div>
        <div class="stat-content">
            <h3>{{ number_format($totalLansia ?? 0) }}</h3>
            <p>Lansia (>60 Tahun)</p>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div class="charts-grid">
    <!-- Gender Distribution Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Distribusi Gender</h2>
            <i class="fas fa-ellipsis-v chart-menu"></i>
        </div>
        <canvas id="genderChart"></canvas>
    </div>

    <!-- Age Distribution Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Distribusi Usia</h2>
            <i class="fas fa-ellipsis-v chart-menu"></i>
        </div>
        <canvas id="ageChart"></canvas>
    </div>

    <!-- Education Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Tingkat Pendidikan</h2>
            <i class="fas fa-ellipsis-v chart-menu"></i>
        </div>
        <canvas id="educationChart"></canvas>
    </div>

    <!-- Occupation Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Jenis Pekerjaan</h2>
            <i class="fas fa-ellipsis-v chart-menu"></i>
        </div>
        <canvas id="occupationChart"></canvas>
    </div>
</div>

<!-- Map -->
<div class="map-container">
    <div class="chart-header">
        <h2 class="chart-title">Peta Sebaran Penduduk Per Dusun</h2>
        <i class="fas fa-expand chart-menu"></i>
    </div>
    <div id="map"></div>
</div>

<!-- Recent Activity -->
<div class="recent-activity">
    <div class="chart-header">
        <h2 class="chart-title">Aktivitas Terbaru</h2>
        <a href="#" style="color: #076653; font-size: 14px; text-decoration: none;">Lihat Semua</a>
    </div>
    
    <div class="activity-item">
        <div class="activity-icon upload">
            <i class="fas fa-file-upload"></i>
        </div>
        <div class="activity-content">
            <h4>Upload Data Penduduk</h4>
            <p>500 data penduduk berhasil diimport</p>
        </div>
        <span class="activity-time">2 jam lalu</span>
    </div>

    <div class="activity-item">
        <div class="activity-icon edit">
            <i class="fas fa-edit"></i>
        </div>
        <div class="activity-content">
            <h4>Edit Data Penduduk</h4>
            <p>Data penduduk "Ahmad Fauzi" telah diperbarui</p>
        </div>
        <span class="activity-time">5 jam lalu</span>
    </div>

    <div class="activity-item">
        <div class="activity-icon delete">
            <i class="fas fa-trash-alt"></i>
        </div>
        <div class="activity-content">
            <h4>Hapus Data Penduduk</h4>
            <p>1 data penduduk telah dihapus</p>
        </div>
        <span class="activity-time">1 hari lalu</span>
    </div>

    <div class="activity-item">
        <div class="activity-icon upload">
            <i class="fas fa-map-marker-alt"></i>
        </div>
        <div class="activity-content">
            <h4>Tambah Wilayah Baru</h4>
            <p>Dusun "Krajan" berhasil ditambahkan</p>
        </div>
        <span class="activity-time">2 hari lalu</span>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
// Chart.js Configuration
const chartColors = {
    primary: '#076653',
    secondary: '#0C342C',
    accent: '#E3EF26',
    success: '#10b981',
    warning: '#f59e0b',
    info: '#3b82f6',
};

// Gender Distribution Chart
const genderCtx = document.getElementById('genderChart').getContext('2d');
new Chart(genderCtx, {
    type: 'pie',
    data: {
        labels: ['Laki-laki', 'Perempuan'],
        datasets: [{
            data: [{{ $totalLakiLaki ?? 0 }}, {{ $totalPerempuan ?? 0 }}],
            backgroundColor: [chartColors.primary, chartColors.warning],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Age Distribution Chart
const ageCtx = document.getElementById('ageChart').getContext('2d');
new Chart(ageCtx, {
    type: 'bar',
    data: {
        labels: ['0-5', '6-12', '13-17', '18-60', '>60'],
        datasets: [{
            label: 'Jumlah Penduduk',
            data: [{{ $totalBalita ?? 0 }}, 150, 120, {{ $totalProduktif ?? 0 }}, {{ $totalLansia ?? 0 }}],
            backgroundColor: chartColors.primary,
            borderRadius: 8
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
        labels: ['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2'],
        datasets: [{
            label: 'Jumlah',
            data: [200, 150, 180, 50, 80, 20],
            backgroundColor: [
                chartColors.primary,
                chartColors.success,
                chartColors.info,
                chartColors.warning,
                chartColors.secondary,
                '#6366f1'
            ],
            borderRadius: 8
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
        labels: ['Petani', 'PNS', 'Wiraswasta', 'Buruh', 'Guru', 'Lainnya'],
        datasets: [{
            data: [250, 80, 120, 150, 60, 100],
            backgroundColor: [
                chartColors.primary,
                chartColors.success,
                chartColors.info,
                chartColors.warning,
                '#6366f1',
                '#8b5cf6'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Leaflet Map
const map = L.map('map').setView([-7.5, 110.5], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Sample markers for dusun
const dusunData = [
    { name: 'Dusun Krajan', lat: -7.50, lng: 110.50, penduduk: 500 },
    { name: 'Dusun Jati', lat: -7.51, lng: 110.51, penduduk: 450 },
    { name: 'Dusun Mawar', lat: -7.49, lng: 110.49, penduduk: 400 },
];

dusunData.forEach(dusun => {
    const marker = L.marker([dusun.lat, dusun.lng]).addTo(map);
    marker.bindPopup(`
        <div style="font-family: 'Segoe UI', sans-serif;">
            <h3 style="margin: 0 0 10px 0; color: #0C342C;">${dusun.name}</h3>
            <p style="margin: 5px 0;"><strong>Total Penduduk:</strong> ${dusun.penduduk}</p>
            <p style="margin: 5px 0;"><strong>Laki-laki:</strong> ${Math.floor(dusun.penduduk * 0.52)}</p>
            <p style="margin: 5px 0;"><strong>Perempuan:</strong> ${Math.floor(dusun.penduduk * 0.48)}</p>
        </div>
    `);
});
</script>
@endpush
