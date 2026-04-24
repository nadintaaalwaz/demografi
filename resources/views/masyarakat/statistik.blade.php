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
        max-width: 1280px;
        margin: 0 auto 20px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        align-items: stretch;
    }

    .kpi-card {
        background: #fff;
        border-radius: 26px;
        border: 2px solid #0f172a;
        box-shadow: none;
        padding: 20px;
        min-height: 190px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
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
        font-size: 34px;
        font-weight: 800;
        line-height: 1.2;
    }

    .kpi-sub {
        margin-top: 6px;
        font-size: 13px;
        color: #475569;
    }

    .gender-counts {
        margin-top: 10px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .gender-pill {
        border-radius: 10px;
        padding: 8px 10px;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.35;
    }

    .gender-pill small {
        display: block;
        font-size: 11px;
        opacity: 0.85;
        font-weight: 600;
    }

    .gender-pill.male {
        background: #076653;
    }

    .gender-pill.female {
        background: #f59e0b;
    }

    .list-wrap {
        margin-top: 8px;
        max-height: 102px;
        overflow: auto;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
    }

    .mini-list {
        margin: 0;
        padding: 8px 10px 8px 22px;
        font-size: 12px;
        color: #334155;
        line-height: 1.5;
    }

    .mini-list li + li {
        margin-top: 4px;
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

    .kpi-card canvas {
        width: 100% !important;
        max-height: 170px !important;
        margin-top: 6px;
    }

    .status-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(0, 1fr);
        gap: 12px;
        align-items: center;
    }

    .status-summary {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px;
        background: #f8fafc;
    }

    .status-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        color: #334155;
        padding: 6px 0;
    }

    .status-item + .status-item {
        border-top: 1px dashed #cbd5e1;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 8px;
        vertical-align: middle;
    }

    .status-dot.aktif { background: #10b981; }
    .status-dot.keluar { background: #f59e0b; }
    .status-dot.meninggal { background: #ef4444; }

    .status-summary.full {
        margin-top: 10px;
        height: 100%;
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

        .gender-counts {
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
    @php
        $rwPerDusun = collect($wilayahStructureRows)->map(function ($row) {
            $rwList = $row['rw_list'] ?? [];
            return [
                'dusun' => $row['dusun'] ?? '-',
                'jumlah_rw' => count($rwList),
                'rw_list' => $rwList,
            ];
        })->values();

        $rwRtDetails = collect($wilayahStructureRows)->flatMap(function ($row) {
            return collect($row['rw_detail'] ?? [])->map(function ($rw) use ($row) {
                return [
                    'dusun' => $row['dusun'] ?? '-',
                    'nomor_rw' => $rw['nomor_rw'] ?? '-',
                    'jumlah_rt' => $rw['jumlah_rt'] ?? 0,
                    'rt_list' => $rw['rt_list'] ?? [],
                ];
            });
        })->values();
    @endphp

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
            <div class="kpi-label">Jumlah Penduduk per Gender</div>
            <div class="kpi-value">{{ number_format(($genderValues[0] ?? 0) + ($genderValues[1] ?? 0)) }}</div>
            <div class="kpi-sub">Ditampilkan dalam jumlah jiwa (bukan persentase)</div>
            <div class="gender-counts">
                <div class="gender-pill male">
                    Laki-laki: {{ number_format($genderValues[0] ?? 0) }}
                    <small>Jiwa</small>
                </div>
                <div class="gender-pill female">
                    Perempuan: {{ number_format($genderValues[1] ?? 0) }}
                    <small>Jiwa</small>
                </div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Jumlah Dusun dan RW per Dusun</div>
            <div class="kpi-value">{{ $totalDusun }} Dusun</div>
            <div class="kpi-sub">Total RW: {{ $totalRw }}</div>
            <div class="list-wrap">
                <ul class="mini-list">
                    @forelse($rwPerDusun as $item)
                        <li>
                            {{ $item['dusun'] }}: {{ $item['jumlah_rw'] }} RW
                            @if(!empty($item['rw_list']))
                                (RW {{ implode(', ', $item['rw_list']) }})
                            @endif
                        </li>
                    @empty
                        <li>Belum ada data dusun/RW.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Jumlah RW dan RT per RW</div>
            <div class="kpi-value">{{ $totalRw }} RW</div>
            <div class="kpi-sub">Total RT: {{ $totalRt }}</div>
            <div class="list-wrap">
                <ul class="mini-list">
                    @forelse($rwRtDetails as $item)
                        <li>
                            {{ $item['dusun'] }} - RW {{ $item['nomor_rw'] }}:
                            @if(!empty($item['rt_list']))
                                RT {{ implode(', ', $item['rt_list']) }}
                            @else
                                belum ada RT
                            @endif
                            ({{ $item['jumlah_rt'] }} RT)
                        </li>
                    @empty
                        <li>Belum ada data relasi RW/RT.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Komposisi Gender</div>
            <canvas id="genderChart"></canvas>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Luas Wilayah Desa</div>
            <div class="kpi-value">{{ number_format($totalLuasDesaKm2, 2) }} km²</div>
            <div class="kpi-sub">Kepadatan: {{ number_format($kepadatan, 2) }} jiwa/km²</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Jumlah Status Kependudukan</div>
            <div class="status-summary full">
                <div class="status-item">
                    <span><span class="status-dot aktif"></span>Aktif</span>
                    <strong>{{ number_format($statusValues[0] ?? 0) }}</strong>
                </div>
                <div class="status-item">
                    <span><span class="status-dot keluar"></span>Keluar</span>
                    <strong>{{ number_format($statusValues[1] ?? 0) }}</strong>
                </div>
                <div class="status-item">
                    <span><span class="status-dot meninggal"></span>Meninggal</span>
                    <strong>{{ number_format($statusValues[2] ?? 0) }}</strong>
                </div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Chart Status Kependudukan</div>
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <div class="chart-grid">
        <div class="panel">
            <h3>Pendidikan Terakhir (Agregat)</h3>
            <canvas id="educationChart"></canvas>
        </div>

        <div class="panel">
            <h3>Pekerjaan Utama (Agregat)</h3>
            <canvas id="occupationChart"></canvas>
        </div>

        <div class="panel">
            <h3>Analisis Dinamika Penduduk</h3>
            <canvas id="dynamicsChart"></canvas>
        </div>
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
