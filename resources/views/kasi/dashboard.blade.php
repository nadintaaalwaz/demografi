@extends('kasi.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Desa Sebalor')

@push('styles')
<style>
    .stats-grid,
    .age-grid {
        display: grid;
        gap: 18px;
        margin-bottom: 22px;
    }

    .stats-grid {
        grid-template-columns: repeat(5, minmax(0, 1fr));
    }

    .age-grid {
        grid-template-columns: repeat(5, minmax(0, 1fr));
        margin-bottom: 30px;
    }

    .stat-card {
        background: linear-gradient(180deg, #ffffff 0%, #fbfcfd 100%);
        padding: 18px 18px 16px;
        border-radius: 24px;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.08);
        border: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 142px;
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 18px 36px rgba(7, 102, 83, 0.16), 0 0 0 1px rgba(227, 239, 38, 0.18);
    }

    .stat-card.summary-card {
        min-height: 132px;
    }

    .stat-card.summary-card::after,
    .stat-card.age-card::after {
        content: '';
        position: absolute;
        inset: auto -24px -30px auto;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(227, 239, 38, 0.12) 0%, rgba(227, 239, 38, 0) 72%);
        pointer-events: none;
    }

    .stat-label {
        font-size: 15px;
        font-weight: 800;
        color: #111827;
        line-height: 1.35;
        text-transform: none;
        letter-spacing: 0;
        margin-bottom: 8px;
    }

    .stat-label.small {
        font-size: 13px;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 800;
        color: #0C342C;
        line-height: 1;
        margin-top: auto;
    }

    .stat-sub {
        margin-top: 10px;
        font-size: 13px;
        color: #475569;
        line-height: 1.55;
    }

    .stat-detail {
        margin-top: 10px;
        display: inline-flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #f8fafc;
        color: #334155;
        font-size: 12px;
        font-weight: 700;
    }

    .dashboard-section-title {
        margin: 6px 0 16px;
        font-size: 18px;
        font-weight: 800;
        color: #0C342C;
        letter-spacing: -0.01em;
    }

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 22px;
        margin-bottom: 40px;
    }

    .chart-card {
        background: #fff;
        padding: 22px;
        border-radius: 28px;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.08);
        border: none;
        min-height: 340px;
        transition: transform 0.28s ease, box-shadow 0.28s ease;
    }

    .chart-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 18px 36px rgba(7, 102, 83, 0.16), 0 0 0 1px rgba(227, 239, 38, 0.18);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 2px solid #eef2f7;
    }

    .chart-title {
        font-size: 17px;
        font-weight: 800;
        color: #111827;
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

    .chart-card canvas {
        width: 100% !important;
        max-height: 260px !important;
    }

    .occupation-section {
        background: #fff;
        padding: 28px;
        border-radius: 28px;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.08);
        border: none;
        transition: transform 0.28s ease, box-shadow 0.28s ease;
        margin-bottom: 40px;
    }

    .occupation-section:hover {
        transform: translateY(-7px);
        box-shadow: 0 18px 36px rgba(7, 102, 83, 0.16), 0 0 0 1px rgba(227, 239, 38, 0.18);
    }

    .occupation-header {
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #eef2f7;
    }

    .occupation-title {
        font-size: 18px;
        font-weight: 800;
        color: #0C342C;
        letter-spacing: -0.01em;
    }

    .occupation-table {
        width: 100%;
        border-collapse: collapse;
    }

    .occupation-table thead tr {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .occupation-table th {
        padding: 14px 16px;
        text-align: left;
        font-size: 13px;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .occupation-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.15s ease;
    }

    .occupation-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .occupation-table td {
        padding: 16px;
        font-size: 15px;
        color: #334155;
    }

    .occupation-table td:first-child {
        font-weight: 600;
        color: #076653;
    }

    .occupation-number {
        font-weight: 700;
        color: #0C342C;
        font-size: 16px;
    }

    .occupation-percentage {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
    }

    .occupation-bar {
        width: 100%;
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
        margin-top: 8px;
    }

    .occupation-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #076653 0%, #0C342C 100%);
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    @media (max-width: 768px) {
        .stats-grid,
        .age-grid,
        .charts-grid {
            grid-template-columns: 1fr;
        }

        .chart-card {
            min-height: auto;
        }

        .occupation-table {
            font-size: 13px;
        }

        .occupation-table th,
        .occupation-table td {
            padding: 10px 12px;
        }
    }

    @media (max-width: 1100px) and (min-width: 769px) {
        .stats-grid,
        .age-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .charts-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>
@endpush

@section('content')
@php
    $totalMeninggalKeluar = (int) (($totalMeninggalKeluar ?? 0) ?: ((($totalMeninggal ?? 0) + ($totalKeluar ?? 0))));

    $summaryCards = [
        [
            'label' => 'Jumlah penduduk aktif',
            'value' => $totalPenduduk ?? 0,
            'sub' => 'Penduduk yang masih terdata aktif di wilayah layanan.',
        ],
        [
            'label' => 'Jumlah kepala keluarga',
            'value' => $totalKK ?? 0,
            'sub' => 'Kepala keluarga aktif yang menjadi acuan administrasi.',
        ],
        [
            'label' => 'Jumlah laki-laki',
            'value' => $totalLakiLaki ?? 0,
            'sub' => ($persenLakiLaki ?? 0) . '% dari total penduduk aktif.',
        ],
        [
            'label' => 'Jumlah perempuan',
            'value' => $totalPerempuan ?? 0,
            'sub' => ($persenPerempuan ?? 0) . '% dari total penduduk aktif.',
        ],
        [
            'label' => 'Jumlah meninggal dan keluar',
            'value' => $totalMeninggalKeluar,
            'sub' => 'Meninggal: ' . number_format($totalMeninggal ?? 0) . ' • Keluar: ' . number_format($totalKeluar ?? 0),
        ],
    ];

    $ageCards = [
        [
            'label' => 'Jumlah Bayi & Balita (0–5 Tahun)',
            'value' => $ageValues[0] ?? 0,
            'sub' => 'Masa krusial untuk pertumbuhan fisik, perkembangan kognitif, dan pencegahan stunting.',
        ],
        [
            'label' => 'Jumlah Anak-anak (6–11 Tahun)',
            'value' => $ageValues[1] ?? 0,
            'sub' => 'Masa usia sekolah dasar, fokus pada pengembangan kemampuan sosial, kognitif, dan perilaku dasar.',
        ],
        [
            'label' => 'Jumlah Remaja (12–18 Tahun)',
            'value' => $ageValues[2] ?? 0,
            'sub' => 'Masa pubertas dan pencarian jati diri, penting untuk edukasi kesehatan reproduksi dan mental.',
        ],
        [
            'label' => 'Jumlah Dewasa (19–59 Tahun)',
            'value' => $ageValues[3] ?? 0,
            'sub' => 'Usia produktif yang fokus pada produktivitas kerja, kesehatan fisik, dan pencegahan penyakit tidak menular.',
        ],
        [
            'label' => 'Jumlah Lansia (60+ Tahun)',
            'value' => $ageValues[4] ?? 0,
            'sub' => 'Fokus pada pemeliharaan kesehatan di usia tua agar tetap mandiri dan memiliki kualitas hidup yang baik.',
        ],
    ];
@endphp

<div class="stats-grid">
    @foreach($summaryCards as $card)
        <div class="stat-card summary-card">
            <div class="stat-label">{{ $card['label'] }}</div>
            <div class="stat-value">{{ number_format($card['value'] ?? 0) }}</div>
            <div class="stat-sub">{{ $card['sub'] }}</div>
        </div>
    @endforeach
</div>

<div class="age-grid">
    @foreach($ageCards as $card)
        <div class="stat-card age-card">
            <div class="stat-label">{{ $card['label'] }}</div>
            <div class="stat-value">{{ number_format($card['value'] ?? 0) }}</div>
            <div class="stat-sub">{{ $card['sub'] }}</div>
        </div>
    @endforeach
</div>

<div class="charts-grid">
    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Chart jumlah laki-laki dan perempuan</h2>
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

    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Kategori Usia</h2>
        </div>
        <canvas id="ageChart"></canvas>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Tingkat pendidikan</h2>
        </div>
        <canvas id="educationChart"></canvas>
    </div>
</div>

<!-- Occupation Section -->
<div class="occupation-section">
    <div class="occupation-header">
        <h2 class="occupation-title">Informasi Pekerjaan Penduduk Aktif</h2>
    </div>
    <table class="occupation-table">
        <thead>
            <tr>
                <th style="width: 25%;">Jenis Pekerjaan</th>
                <th style="width: 15%; text-align: center;">Jumlah</th>
                <th style="width: 60%;">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalOccupation = array_sum($occupationValues ?? []);
            @endphp
            @forelse($occupationLabels as $index => $label)
                @php
                    $count = $occupationValues[$index] ?? 0;
                    $percentage = $totalOccupation > 0 ? round(($count / $totalOccupation) * 100, 1) : 0;
                @endphp
                <tr>
                    <td>{{ $label }}</td>
                    <td style="text-align: center;">
                        <div class="occupation-number">{{ number_format($count) }}</div>
                    </td>
                    <td>
                        <div class="occupation-bar">
                            <div class="occupation-bar-fill" style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="occupation-percentage">{{ $percentage }}% dari {{ number_format($totalOccupation) }} penduduk aktif</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #94a3b8; padding: 24px;">
                        Data pekerjaan tidak tersedia
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

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
</script>
@endpush
