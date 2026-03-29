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
        text-align: center;
    }

    .stats-hero h1 {
        font-size: 36px;
        margin-bottom: 10px;
        font-weight: 800;
    }

    .stats-hero p {
        max-width: 860px;
        margin: 0 auto;
        color: rgba(255, 255, 255, 0.88);
        font-size: 15px;
        line-height: 1.6;
    }

    .privacy-note {
        margin-top: 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        background: rgba(227, 239, 38, 0.16);
        color: #E3EF26;
        border: 1px solid rgba(227, 239, 38, 0.35);
    }

    .section-wrap {
        background: #f8fafc;
        padding: 26px 28px;
    }

    .kpi-grid {
        max-width: 1200px;
        margin: 0 auto 20px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        align-items: stretch;
    }

    .kpi-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
        padding: 18px;
        min-height: 148px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .kpi-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 8px;
    }

    .kpi-value {
        color: #0C342C;
        font-size: 30px;
        font-weight: 800;
        line-height: 1.2;
    }

    .kpi-sub {
        margin-top: 6px;
        font-size: 13px;
        color: #475569;
    }

    .chart-grid {
        max-width: 1280px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 14px;
    }

    .panel {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
        padding: 16px;
    }

    .panel h3 {
        margin: 0 0 12px;
        font-size: 16px;
        color: #0f172a;
    }

    .panel canvas {
        width: 100% !important;
        max-height: 260px !important;
    }

    .wide-panel {
        max-width: 1280px;
        margin: 14px auto 0;
    }

    .table-wrap {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .data-table th,
    .data-table td {
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 12px;
        text-align: left;
    }

    .data-table th {
        color: #334155;
        font-weight: 700;
        background: #f8fafc;
    }

    .text-right {
        text-align: right !important;
    }

    #publicStatMap {
        height: 360px;
        border-radius: 12px;
        margin-top: 12px;
    }

    @media (max-width: 768px) {
        .kpi-grid {
            grid-template-columns: 1fr;
        }

        .stats-hero {
            padding: 36px 20px 26px;
        }

        .stats-hero h1 {
            font-size: 28px;
        }

        .section-wrap {
            padding: 18px;
        }
    }

    @media (max-width: 1100px) and (min-width: 769px) {
        .kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>
@endpush

@section('content')
<section class="stats-hero">
    <h1>Statistik Demografi Desa Sebalor</h1>
    <p>
        Data ditampilkan dalam bentuk agregat untuk menjaga privasi warga. Tidak ada data individu yang ditampilkan.
    </p>
    <div class="privacy-note">
        <i class="fas fa-shield-alt"></i>
        Kategori dengan jumlah < {{ $privacyThreshold }} digabung ke "Lainnya"
    </div>
</section>

<section class="section-wrap">
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">Total Penduduk Aktif</div>
            <div class="kpi-value">{{ number_format($totalPendudukAktif) }}</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Total Kepala Keluarga</div>
            <div class="kpi-value">{{ number_format($totalKK) }}</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Jumlah Dusun / RW / RT</div>
            <div class="kpi-value">{{ $totalDusun }} / {{ $totalRw }} / {{ $totalRt }}</div>
            <div class="kpi-sub">Struktur wilayah administratif</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Luas Wilayah Dusun</div>
            <div class="kpi-value">{{ number_format($totalLuasDusunKm2, 2) }} km²</div>
            <div class="kpi-sub">Kepadatan: {{ number_format($kepadatan, 2) }} jiwa/km²</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Persentase Gender</div>
            <div class="kpi-value">L {{ $genderPercent['L'] }}% · P {{ $genderPercent['P'] }}%</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Status Kependudukan</div>
            <div class="kpi-value">A {{ $statusPercentages[0] }}% · K {{ $statusPercentages[1] }}% · M {{ $statusPercentages[2] }}%</div>
            <div class="kpi-sub">A: Aktif, K: Keluar, M: Meninggal</div>
        </div>
    </div>

    <div class="chart-grid">
        <div class="panel">
            <h3>Komposisi Gender</h3>
            <canvas id="genderChart"></canvas>
        </div>

        <div class="panel">
            <h3>Kelompok Usia (Rentang)</h3>
            <canvas id="ageChart"></canvas>
        </div>

        <div class="panel">
            <h3>Status Kependudukan</h3>
            <canvas id="statusChart"></canvas>
        </div>

        <div class="panel">
            <h3>Pendidikan Terakhir (Agregat)</h3>
            <canvas id="educationChart"></canvas>
        </div>

        <div class="panel">
            <h3>Pekerjaan Utama (Agregat)</h3>
            <canvas id="occupationChart"></canvas>
        </div>

        <div class="panel">
            <h3>Total Penduduk per Dusun</h3>
            <canvas id="dusunChart"></canvas>
        </div>
    </div>

    <div class="panel wide-panel">
        <h3>Dinamika Bulanan (Tanpa Identitas)</h3>
        <canvas id="dynamicsChart"></canvas>
    </div>

    <div class="panel wide-panel" style="margin-top: 14px;">
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
                    @forelse($dusunPopulationRows as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['nama'] }}</td>
                            <td class="text-right">{{ number_format($row['total_penduduk']) }}</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
const c = {
    primary: '#076653',
    secondary: '#0C342C',
    success: '#10b981',
    warning: '#f59e0b',
    danger: '#ef4444',
    info: '#3b82f6',
    purple: '#8b5cf6',
};

new Chart(document.getElementById('genderChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: @json($genderLabels),
        datasets: [{
            data: @json($genderValues),
            backgroundColor: [c.primary, c.warning],
            borderWidth: 0
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('ageChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json($ageLabels),
        datasets: [{
            label: 'Jumlah',
            data: @json($ageValues),
            backgroundColor: c.info,
            borderRadius: 8
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('statusChart').getContext('2d'), {
    type: 'pie',
    data: {
        labels: @json($statusLabels),
        datasets: [{
            data: @json($statusValues),
            backgroundColor: [c.success, c.warning, c.danger],
            borderWidth: 0
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('educationChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json($educationLabels),
        datasets: [{
            label: 'Jumlah',
            data: @json($educationValues),
            backgroundColor: c.primary,
            borderRadius: 8
        }]
    },
    options: { responsive: true, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
});

new Chart(document.getElementById('occupationChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json($occupationLabels),
        datasets: [{
            label: 'Jumlah',
            data: @json($occupationValues),
            backgroundColor: c.purple,
            borderRadius: 8
        }]
    },
    options: { responsive: true, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
});

new Chart(document.getElementById('dusunChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json(collect($dusunPopulationRows)->pluck('nama')->values()->all()),
        datasets: [{
            label: 'Penduduk Aktif',
            data: @json(collect($dusunPopulationRows)->pluck('total_penduduk')->values()->all()),
            backgroundColor: c.secondary,
            borderRadius: 8
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('dynamicsChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: @json($trendLabels),
        datasets: [
            {
                label: 'Kelahiran',
                data: @json($kelahiranSeries),
                borderColor: c.success,
                backgroundColor: 'rgba(16,185,129,0.15)',
                tension: 0.35,
                fill: false
            },
            {
                label: 'Kematian',
                data: @json($kematianSeries),
                borderColor: c.danger,
                backgroundColor: 'rgba(239,68,68,0.15)',
                tension: 0.35,
                fill: false
            },
            {
                label: 'Migrasi Masuk',
                data: @json($migrasiMasukSeries),
                borderColor: c.info,
                backgroundColor: 'rgba(59,130,246,0.15)',
                tension: 0.35,
                fill: false
            },
            {
                label: 'Migrasi Keluar',
                data: @json($migrasiKeluarSeries),
                borderColor: c.warning,
                backgroundColor: 'rgba(245,158,11,0.15)',
                tension: 0.35,
                fill: false
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true } }
    }
});

const map = L.map('publicStatMap').setView([{{ (float) $mapCenterLat }}, {{ (float) $mapCenterLng }}], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

const dusunMapData = @json($dusunPopulationRows);

let hasMarker = false;
dusunMapData.forEach((dusun) => {
    if (dusun.lat === null || dusun.lng === null) {
        return;
    }

    hasMarker = true;
    const marker = L.marker([dusun.lat, dusun.lng]).addTo(map);
    marker.bindPopup(`<b>${dusun.nama}</b><br>Total Penduduk Aktif: ${dusun.total_penduduk}`);
});

if (!hasMarker) {
    map.setView([{{ (float) $mapCenterLat }}, {{ (float) $mapCenterLng }}], 11);
}
</script>
@endpush
