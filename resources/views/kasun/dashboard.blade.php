@extends('kasun.layout')

@section('title', 'Dashboard Dusun')

@section('page-title')
Dashboard {{ Auth::user()->dusun_name ?? "Dusun" }}
@endsection

@push('styles')
<style>
    .stats-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: stretch;
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        padding: 20px;
        width: 100%;
        max-width: 286px;
        min-height: 220px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        border-radius: 16px;
        box-shadow: 0 8px 22px rgba(12, 52, 44, 0.08);
        text-align: center;
        transition: all 0.3s ease;
        border-bottom: 4px solid transparent;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .stat-card.primary {
        border-bottom-color: #076653;
    }

    .stat-card.success {
        border-bottom-color: #10b981;
    }

    .stat-card.warning {
        border-bottom-color: #f59e0b;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: #fff;
        margin: 0 auto 15px;
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

    .stat-card h3 {
        font-size: 36px;
        font-weight: 700;
        color: #0C342C;
        margin-bottom: 5px;
    }

    .stat-card p {
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
    }

    .stat-card .percentage {
        font-size: 18px;
        color: #076653;
        font-weight: 600;
    }

    .info-banner {
        background: linear-gradient(135deg, #076653, #0C342C);
        color: #fff;
        padding: 24px;
        border-radius: 16px;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 10px 28px rgba(7, 102, 83, 0.34);
    }

    .info-banner-content h2 {
        font-size: 24px;
        margin-bottom: 8px;
    }

    .info-banner-content p {
        font-size: 14px;
        opacity: 0.9;
    }

    .info-banner-icon {
        font-size: 60px;
        opacity: 0.3;
    }

    .charts-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .chart-card {
        background: #fff;
        padding: 16px;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        border: 1px solid #edf2f7;
    }

    .chart-card canvas {
        width: 100% !important;
        max-height: 170px !important;
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

    .map-container {
        background: #fff;
        padding: 18px;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
        border: 1px solid #edf2f7;
    }

    #dusunMap {
        height: 400px;
        border-radius: 12px;
        margin-top: 20px;
    }

    .dinamika-section {
        background: #fff;
        padding: 18px;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        border: 1px solid #edf2f7;
    }

    .dinamika-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .dinamika-card {
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        border: 2px solid #f3f4f6;
        transition: all 0.3s ease;
    }

    .dinamika-card:hover {
        border-color: #076653;
        background: #f9fafb;
    }

    .dinamika-card i {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .dinamika-card.birth i {
        color: #10b981;
    }

    .dinamika-card.death i {
        color: #ef4444;
    }

    .dinamika-card.in i {
        color: #3b82f6;
    }

    .dinamika-card.out i {
        color: #f59e0b;
    }

    .dinamika-card h4 {
        font-size: 28px;
        font-weight: 700;
        color: #0C342C;
        margin-bottom: 5px;
    }

    .dinamika-card p {
        font-size: 13px;
        color: #6b7280;
    }

    .dusun-total-section {
        background: #fff;
        padding: 18px;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
        border: 1px solid #edf2f7;
    }

    .dusun-total-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 14px;
    }

    .dusun-total-table th,
    .dusun-total-table td {
        text-align: left;
        padding: 12px 14px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 14px;
    }

    .dusun-total-table th {
        color: #374151;
        font-weight: 700;
        background: #f9fafb;
    }

    .highlight-row {
        background: #ecfdf5;
    }

    .text-right {
        text-align: right;
    }

    @media (max-width: 768px) {
        .charts-row,
        .dinamika-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            justify-content: stretch;
        }

        .stat-card {
            max-width: 100%;
            min-height: auto;
        }

        .info-banner {
            flex-direction: column;
            text-align: center;
        }

        .info-banner-icon {
            margin-top: 20px;
        }
    }
</style>
@endpush

@section('content')
<!-- Info Banner -->
<div class="info-banner">
    <div class="info-banner-content">
        <h2>Selamat Datang, {{ Auth::user()->nama ?? 'Kasun' }}</h2>
        <p>Berikut adalah data statistik dan monitoring untuk {{ Auth::user()->dusun_name ?? 'Dusun Anda' }}</p>
    </div>
    <div class="info-banner-icon">
        <i class="fas fa-home"></i>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-icon primary">
            <i class="fas fa-users"></i>
        </div>
        <h3>{{ number_format($totalPenduduk) }}</h3>
        <p>Total Penduduk</p>
    </div>

    <div class="stat-card primary">
        <div class="stat-icon primary">
            <i class="fas fa-home"></i>
        </div>
        <h3>{{ number_format($totalKK) }}</h3>
        <p>Total Kepala Keluarga</p>
    </div>

    <div class="stat-card success">
        <div class="stat-icon success">
            <i class="fas fa-male"></i>
        </div>
        <h3>{{ number_format($totalLakiLaki) }}</h3>
        <p>Laki-laki</p>
        <span class="percentage">{{ $totalPenduduk > 0 ? round(($totalLakiLaki / $totalPenduduk) * 100) : 0 }}%</span>
    </div>

    <div class="stat-card warning">
        <div class="stat-icon warning">
            <i class="fas fa-female"></i>
        </div>
        <h3>{{ number_format($totalPerempuan) }}</h3>
        <p>Perempuan</p>
        <span class="percentage">{{ $totalPenduduk > 0 ? round(($totalPerempuan / $totalPenduduk) * 100) : 0 }}%</span>
    </div>

    <div class="stat-card primary">
        <div class="stat-icon primary">
            <i class="fas fa-child"></i>
        </div>
        <h3>{{ number_format($totalBalita) }}</h3>
        <p>Balita (0-5 Tahun)</p>
    </div>

    <div class="stat-card success">
        <div class="stat-icon success">
            <i class="fas fa-user-friends"></i>
        </div>
        <h3>{{ number_format($totalProduktif) }}</h3>
        <p>Usia Produktif</p>
    </div>

    <div class="stat-card warning">
        <div class="stat-icon warning">
            <i class="fas fa-user-clock"></i>
        </div>
        <h3>{{ number_format($totalLansia) }}</h3>
        <p>Lansia (>60 Tahun)</p>
    </div>
</div>

<!-- Charts -->
<div class="charts-row">
    <!-- Gender Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Distribusi Gender</h2>
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

    <!-- Age Distribution -->
    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Distribusi Usia</h2>
        </div>
        <canvas id="ageChart"></canvas>
    </div>

    <!-- Education -->
    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Tingkat Pendidikan</h2>
        </div>
        <canvas id="educationChart"></canvas>
    </div>

    <!-- Occupation -->
    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Jenis Pekerjaan</h2>
        </div>
        <canvas id="occupationChart"></canvas>
    </div>
</div>

<!-- Map -->
<div class="map-container">
    <div class="chart-header">
        <h2 class="chart-title">Peta Lokasi Dusun</h2>
    </div>
    <div id="dusunMap"></div>
</div>

<!-- Total Penduduk per Dusun -->
<div class="dusun-total-section">
    <div class="chart-header">
        <h2 class="chart-title">Total Penduduk per Dusun</h2>
    </div>

    <table class="dusun-total-table">
        <thead>
            <tr>
                <th style="width: 60px;">No</th>
                <th>Nama Dusun</th>
                <th class="text-right">Total Penduduk Aktif</th>
            </tr>
        </thead>
        <tbody>
            @forelse($totalPerDusun as $index => $item)
                <tr class="{{ (int) $item->id === (int) (Auth::user()->id_dusun ?? 0) ? 'highlight-row' : '' }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama }}</td>
                    <td class="text-right">{{ number_format($item->total_penduduk) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Belum ada data dusun.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Dinamika Penduduk -->
<div class="dinamika-section">
    <div class="chart-header">
        <h2 class="chart-title">Dinamika Penduduk Bulan Ini</h2>
    </div>
    
    <div class="dinamika-grid">
        <div class="dinamika-card birth">
            <i class="fas fa-baby"></i>
            <h4>{{ $kelahiran }}</h4>
            <p>Kelahiran</p>
        </div>

        <div class="dinamika-card death">
            <i class="fas fa-cross"></i>
            <h4>{{ $kematian }}</h4>
            <p>Kematian</p>
        </div>

        <div class="dinamika-card in">
            <i class="fas fa-sign-in-alt"></i>
            <h4>{{ $migrasiMasuk }}</h4>
            <p>Migrasi Masuk</p>
        </div>

        <div class="dinamika-card out">
            <i class="fas fa-sign-out-alt"></i>
            <h4>{{ $migrasiKeluar }}</h4>
            <p>Migrasi Keluar</p>
        </div>
    </div>
</div>
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
};

// Gender Chart
const genderCtx = document.getElementById('genderChart').getContext('2d');
new Chart(genderCtx, {
    type: 'doughnut',
    data: {
        labels: ['Laki-laki', 'Perempuan'],
        datasets: [{
            data: @json([(int) $totalLakiLaki, (int) $totalPerempuan]),
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

// Age Chart
const ageCtx = document.getElementById('ageChart').getContext('2d');
new Chart(ageCtx, {
    type: 'bar',
    data: {
        labels: @json($ageLabels),
        datasets: [{
            label: 'Jumlah',
            data: @json($ageValues),
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
        labels: @json($educationLabels),
        datasets: [{
            label: 'Jumlah',
            data: @json($educationValues),
            backgroundColor: chartColors.primary,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        indexAxis: 'y',
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});

// Occupation Chart
const occupationCtx = document.getElementById('occupationChart').getContext('2d');
new Chart(occupationCtx, {
    type: 'pie',
    data: {
        labels: @json($occupationLabels),
        datasets: [{
            data: @json($occupationValues),
            backgroundColor: [
                chartColors.primary,
                chartColors.success,
                '#3b82f6',
                chartColors.warning,
                '#6366f1'
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
const mapLat = {{ (float) $mapLat }};
const mapLng = {{ (float) $mapLng }};

const dusunMap = L.map('dusunMap').setView([mapLat, mapLng], 15);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(dusunMap);

const marker = L.marker([mapLat, mapLng]).addTo(dusunMap);
marker.bindPopup(`
    <div style="font-family: 'Segoe UI', sans-serif;">
        <h3 style="margin: 0 0 10px 0; color: #0C342C;">{{ $dusun?->nama ?? (Auth::user()->dusun_name ?? 'Dusun') }}</h3>
        <p style="margin: 5px 0;"><strong>Total Penduduk:</strong> {{ number_format($totalPenduduk) }}</p>
        <p style="margin: 5px 0;"><strong>Laki-laki:</strong> {{ number_format($totalLakiLaki) }} ({{ $totalPenduduk > 0 ? round(($totalLakiLaki / $totalPenduduk) * 100) : 0 }}%)</p>
        <p style="margin: 5px 0;"><strong>Perempuan:</strong> {{ number_format($totalPerempuan) }} ({{ $totalPenduduk > 0 ? round(($totalPerempuan / $totalPenduduk) * 100) : 0 }}%)</p>
        <p style="margin: 5px 0;"><strong>Balita:</strong> {{ number_format($totalBalita) }}</p>
        <p style="margin: 5px 0;"><strong>Lansia:</strong> {{ number_format($totalLansia) }}</p>
        <p style="margin: 5px 0;"><strong>Kepadatan:</strong> {{ number_format($kepadatan, 2) }} jiwa/km²</p>
    </div>
`).openPopup();
</script>
@endpush
