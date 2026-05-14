@extends('kasun.layout')

@section('title', 'Dashboard Dusun')

@section('page-title')
Dashboard {{ Auth::user()->dusun_name ?? "Dusun" }}
@endsection

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        padding: 18px 20px;
        width: 100%;
        min-height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        border-radius: 14px;
        box-shadow: 0 8px 18px rgba(12, 52, 44, 0.06);
        text-align: center;
        transition: transform 0.22s ease, box-shadow 0.22s ease;
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
        font-size: 28px;
        font-weight: 800;
        margin: 6px 0 4px;
        color: #0C342C;
    }

    .stat-card p {
        font-size: 13px;
        color: #6b7280;
        font-weight: 600;
        margin: 0;
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
        grid-template-columns: repeat(1, 1fr);
        gap: 16px;
        margin-bottom: 24px;
        align-items: start;
    }

    .chart-card {
        background: #fff;
        padding: 16px;
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        border: none;
        display: flex;
        flex-direction: column;
        min-height: 320px;
    }

    .chart-container {
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px 0;
    }

    .chart-card canvas {
        width: 100% !important;
        height: 100% !important;
        max-height: 220px !important;
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
        justify-content: center;
        align-items: center;
        font-size: 13px;
        font-weight: 700;
    }

    .gender-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-width: auto;
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
        height: 300px;
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

    .table-responsive {
        overflow-x: auto;
    }

    .dusun-total-table th,
    .dusun-total-table td {
        text-align: left;
        padding: 10px 12px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 13px;
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

    .text-center {
        text-align: center;
    }

    .age-note-list {
        margin-top: 14px;
        padding-left: 18px;
        color: #4b5563;
        font-size: 13px;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .charts-row,
        .dinamika-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(1, 1fr);
        }

        .stat-card {
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

    @media (min-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .charts-row { grid-template-columns: repeat(2, 1fr); }
    }

    @media (min-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(4, 1fr); }
        .charts-row { grid-template-columns: repeat(3, 1fr); }
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

</div>

<!-- Charts -->
<div class="charts-row">
    <!-- Gender Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Distribusi Gender</h2>
        </div>
        <div class="chart-container">
            <canvas id="genderChart"></canvas>
        </div>
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
        <div class="chart-container">
            <canvas id="ageChart"></canvas>
        </div>
    </div>

    <!-- Education -->
    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Tingkat Pendidikan</h2>
        </div>
        <div class="chart-container">
            <canvas id="educationChart"></canvas>
        </div>
    </div>

    <!-- Occupation removed per request -->
</div>

<!-- Map -->
<div class="map-container">
    <div class="chart-header">
        <h2 class="chart-title">Peta Lokasi Dusun</h2>
    </div>
    <div id="dusunMap"></div>
</div>

<!-- Kategori Usia per Dusun -->
<div class="dusun-total-section">
    <div class="chart-header">
        <h2 class="chart-title">Kategori Usia per Dusun</h2>
    </div>

    <div class="table-responsive">
        <table class="dusun-total-table">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Dusun</th>
                    <th class="text-center">Bayi & Balita (0-5)</th>
                    <th class="text-center">Anak-anak (6-11)</th>
                    <th class="text-center">Remaja (10-19)</th>
                    <th class="text-center">Dewasa (19-59)</th>
                    <th class="text-center">Lansia (60+)</th>
                    <th class="text-right">Total Penduduk Aktif</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategoriUsiaPerDusun as $index => $item)
                    <tr class="{{ (int) $item->id === (int) (Auth::user()->id_dusun ?? 0) ? 'highlight-row' : '' }}">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->nama }}</td>
                        <td class="text-center">{{ number_format($item->bayi_balita) }}</td>
                        <td class="text-center">{{ number_format($item->anak_anak) }}</td>
                        <td class="text-center">{{ number_format($item->remaja) }}</td>
                        <td class="text-center">{{ number_format($item->dewasa) }}</td>
                        <td class="text-center">{{ number_format($item->lansia) }}</td>
                        <td class="text-right">{{ number_format($item->total_penduduk) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Belum ada data dusun.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <ul class="age-note-list">
        <li>Bayi & Balita (0-5 Tahun): Masa krusial untuk pertumbuhan fisik, perkembangan kognitif, dan pencegahan stunting.</li>
        <li>Anak-anak (6-11 Tahun): Masa usia sekolah dasar, fokus pada pengembangan kemampuan sosial, kognitif, dan perilaku dasar.</li>
        <li>Remaja (12-18 Tahun): Masa pubertas dan pencarian jati diri, penting untuk edukasi kesehatan reproduksi dan mental.</li>
        <li>Dewasa (19-59 Tahun): Usia produktif yang fokus pada produktivitas kerja, kesehatan fisik, dan pencegahan penyakit tidak menular.</li>
        <li>Lansia (60+ Tahun): Fokus pada pemeliharaan kesehatan di usia tua agar tetap mandiri dan memiliki kualitas hidup yang baik.</li>
    </ul>
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

    <div class="table-responsive" style="margin-top:18px;">
        <table class="dusun-total-table" style="width:100%;">
            <thead>
                <tr>
                    <th>Jenis</th>
                    <th class="text-right">Jumlah</th>
                    <th class="text-right">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $dinTotal = max(1, $totalPenduduk);
                    $net = ($kelahiran + $migrasiMasuk) - ($kematian + $migrasiKeluar);
                @endphp
                <tr>
                    <td>Kelahiran</td>
                    <td class="text-right">{{ number_format($kelahiran) }}</td>
                    <td class="text-right">{{ $totalPenduduk > 0 ? round(($kelahiran / $dinTotal) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td>Kematian</td>
                    <td class="text-right">{{ number_format($kematian) }}</td>
                    <td class="text-right">{{ $totalPenduduk > 0 ? round(($kematian / $dinTotal) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td>Migrasi Masuk</td>
                    <td class="text-right">{{ number_format($migrasiMasuk) }}</td>
                    <td class="text-right">{{ $totalPenduduk > 0 ? round(($migrasiMasuk / $dinTotal) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td>Migrasi Keluar</td>
                    <td class="text-right">{{ number_format($migrasiKeluar) }}</td>
                    <td class="text-right">{{ $totalPenduduk > 0 ? round(($migrasiKeluar / $dinTotal) * 100, 1) : 0 }}%</td>
                </tr>
                <tr class="highlight-row">
                    <td><strong>Perubahan Bersih</strong></td>
                    <td class="text-right"><strong>{{ number_format($net) }}</strong></td>
                    <td class="text-right"><strong>{{ $totalPenduduk > 0 ? round(($net / $dinTotal) * 100, 1) : 0 }}%</strong></td>
                </tr>
            </tbody>
        </table>
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
        maintainAspectRatio: false,
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
        maintainAspectRatio: false,
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
        maintainAspectRatio: false,
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

// Occupation chart removed (per request)

// Leaflet Map
const mapLat = {{ (float) $mapLat }};
const mapLng = {{ (float) $mapLng }};
const rwMapData = @json($rwMapData ?? []);

const dusunMap = L.map('dusunMap').setView([mapLat, mapLng], 15);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(dusunMap);

const bounds = [[mapLat, mapLng]];

const marker = L.circleMarker([mapLat, mapLng], {
    radius: 11,
    color: '#ca8a04',
    weight: 2,
    fillColor: '#facc15',
    fillOpacity: 0.95,
}).addTo(dusunMap);

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
`);

rwMapData.forEach((rw) => {
    const lat = Number(rw.lat);
    const lng = Number(rw.lng);

    if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
        return;
    }

    bounds.push([lat, lng]);

    const rwLabel = rw.nomor_rw ? `RW ${rw.nomor_rw}` : (rw.name || 'RW');
    const rwMarker = L.circleMarker([lat, lng], {
        radius: 8,
        color: '#1d4ed8',
        weight: 2,
        fillColor: '#3b82f6',
        fillOpacity: 0.9,
    }).addTo(dusunMap);

    rwMarker.bindPopup(`
        <div style="font-family: 'Segoe UI', sans-serif; min-width: 170px;">
            <h3 style="margin: 0 0 8px 0; color: #0C342C; font-size: 15px;">${rwLabel}</h3>
            <p style="margin: 4px 0; color: #4b5563;"><strong>Dusun:</strong> {{ $dusun?->nama ?? (Auth::user()->dusun_name ?? 'Dusun') }}</p>
            <p style="margin: 4px 0; color: #4b5563;"><strong>Nama Wilayah:</strong> ${rw.name || '-'}</p>
        </div>
    `);
});

if (bounds.length > 1) {
    dusunMap.fitBounds(bounds, { padding: [30, 30] });
} else {
    marker.openPopup();
}
</script>
@endpush
