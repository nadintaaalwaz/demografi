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

    .gender-legend {
        margin-top: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        font-weight: 700;
    }

    .gender-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-width: 120px;
    }

    .gender-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }

    .male-label { color: #076653; }
    .female-label { color: #f59e0b; }

    .gender-legend .female-label {
        justify-content: flex-start;
    }

    .gender-legend .male-label {
        justify-content: flex-end;
        margin-left: auto;
        text-align: right;
    }

    .chart-menu {
        color: #6b7280;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .chart-menu:hover {
        color: #076653;
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
        <div class="gender-legend">
            <span class="gender-legend-item female-label">
                <span class="gender-legend-dot" style="background:#f59e0b;"></span>
                Perempuan
            </span>
            <span class="gender-legend-item male-label">
                <span class="gender-legend-dot" style="background:#076653;"></span>
                Laki-laki
            </span>
        </div>
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

const ageLabels = @json($ageLabels);
const ageValues = @json($ageValues);
const educationLabels = @json($educationLabels);
const educationValues = @json($educationValues);
const occupationLabels = @json($occupationLabels);
const occupationValues = @json($occupationValues);
const dusunData = @json($dusunMapData);

const palette = [
    chartColors.primary,
    chartColors.success,
    chartColors.info,
    chartColors.warning,
    '#6366f1',
    '#8b5cf6',
    '#14b8a6',
    '#ec4899',
];

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
            legend: { display: false }
        }
    }
});

// Age Distribution Chart
const ageCtx = document.getElementById('ageChart').getContext('2d');
new Chart(ageCtx, {
    type: 'bar',
    data: {
        labels: ageLabels,
        datasets: [{
            label: 'Jumlah Penduduk',
            data: ageValues,
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
        labels: educationLabels,
        datasets: [{
            label: 'Jumlah',
            data: educationValues,
            backgroundColor: educationLabels.map((_, index) => palette[index % palette.length]),
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
        labels: occupationLabels,
        datasets: [{
            data: occupationValues,
            backgroundColor: occupationLabels.map((_, index) => palette[index % palette.length]),
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

const bounds = [];
const baseMarkerStyle = {
    color: '#0284c7',
    weight: 2,
    fillColor: '#38bdf8',
    fillOpacity: 0.35,
};

dusunData.forEach(dusun => {
    const lat = Number(dusun.lat);
    const lng = Number(dusun.lng);

    if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
        return;
    }

    bounds.push([lat, lng]);

    const radius = Math.max(8, Math.min(26, 8 + Math.sqrt(Number(dusun.total_penduduk || 0))));
    const marker = L.circleMarker([lat, lng], {
        ...baseMarkerStyle,
        radius,
    }).addTo(map);
    marker.bindPopup(`
        <div style="font-family: 'Segoe UI', sans-serif; min-width: 200px;">
            <h3 style="margin: 0 0 10px 0; color: #0C342C; font-size: 16px;">${dusun.name}</h3>
            <p style="margin: 5px 0;"><strong>Total Penduduk:</strong> ${dusun.total_penduduk}</p>
            <p style="margin: 5px 0;"><strong>Laki-laki:</strong> ${dusun.total_laki_laki}</p>
            <p style="margin: 5px 0;"><strong>Perempuan:</strong> ${dusun.total_perempuan}</p>
        </div>
    `);
});

if (bounds.length === 1) {
    map.setView(bounds[0], 15);
} else if (bounds.length > 1) {
    map.fitBounds(bounds, { padding: [30, 30] });
}
</script>
@endpush
