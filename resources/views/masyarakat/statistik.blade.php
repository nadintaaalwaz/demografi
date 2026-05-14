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
        gap: 20px;
        align-items: stretch;
    }

    .kpi-card {
        background: #fff;
        border-radius: 26px;
        padding: 20px;
        min-height: 190px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        border: none;
        cursor: pointer;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .kpi-card.kpi-card-center {
        align-items: center;
        text-align: center;
    }

    .kpi-card.kpi-card-center .kpi-sub {
        text-align: center;
    }

    .kpi-card-featured {
        min-height: 220px;
        padding: 24px 24px 22px;
        justify-content: center;
        align-items: center;
        text-align: center;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 18px 40px rgba(12, 52, 44, 0.10);
        position: relative;
        overflow: hidden;
    }

    .kpi-card-featured::before {
        content: '';
        position: absolute;
        top: -36px;
        right: -36px;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(227, 239, 38, 0.16) 0%, rgba(227, 239, 38, 0) 72%);
        pointer-events: none;
    }

    .kpi-card-featured .kpi-label {
        margin-bottom: 10px;
    }

    .kpi-card-featured .kpi-value {
        font-size: 40px;
        line-height: 1.05;
    }

    .kpi-card-featured .kpi-sub {
        font-size: 14px;
        margin-top: 8px;
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

    .gender-chart-legend {
        margin: 10px 0 2px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        font-size: 13px;
        color: #64748b;
    }

    .gender-chart-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
    }

    .gender-chart-swatch {
        width: 44px;
        height: 12px;
        border-radius: 999px;
        display: inline-block;
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
        border-radius: 10px;
        background: #f8fafc;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        border: none;
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
        padding: 16px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        border: none;
        cursor: pointer;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .kpi-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 18px 40px rgba(12, 52, 44, 0.10);
    }

    .panel:hover {
        transform: translateY(-8px);
        box-shadow: 0 18px 40px rgba(12, 52, 44, 0.10);
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
        border-radius: 10px;
        padding: 10px;
        background: #f8fafc;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
        border: none;
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
        <!-- Row 1 -->
        <div class="kpi-card kpi-card-center kpi-card-featured">
            <div class="kpi-label">Total Penduduk Aktif</div>
            <div class="kpi-value">{{ number_format($totalPendudukAktif) }}</div>
        </div>

        <div class="kpi-card kpi-card-center kpi-card-featured">
            <div class="kpi-label">Total Kepala Keluarga</div>
            <div class="kpi-value">{{ number_format($totalKK) }}</div>
        </div>

        <div class="kpi-card kpi-card-center kpi-card-featured">
            <div class="kpi-label">Luas Wilayah Desa</div>
            <div class="kpi-value">{{ number_format($totalLuasDesaKm2, 2) }} km²</div>
            <div class="kpi-sub">Kepadatan: {{ number_format($kepadatan, 2) }} jiwa/km²</div>
        </div>

        <!-- Row 2 -->
        <div class="kpi-card">
            <div class="kpi-label">Informasi Dusun</div>
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
            <div class="kpi-label">Informasi RW</div>
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
                        </li>
                    @empty
                        <li>Belum ada data relasi RW/RT.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Informasi RT</div>
            <div class="kpi-value">{{ $totalRt }} RT</div>
            <div class="kpi-sub">Dari {{ $totalRw }} RW</div>
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
                        </li>
                    @empty
                        <li>Belum ada data RT.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Row 3 -->
        <div class="kpi-card">
            <div class="kpi-label">Chart Jumlah Laki-laki dan Perempuan</div>
            <canvas id="genderChart"></canvas>
            <div class="gender-chart-legend" aria-label="Legenda chart gender">
                <span class="gender-chart-legend-item"><span class="gender-chart-swatch" style="background:#076653;"></span>Laki-laki</span>
                <span class="gender-chart-legend-item"><span class="gender-chart-swatch" style="background:#f59e0b;"></span>Perempuan</span>
            </div>
            <div class="gender-counts" style="margin-top: 16px;">
                <div class="gender-pill male">
                    Laki-laki: {{ number_format($genderValues[0] ?? 0) }}
                </div>
                <div class="gender-pill female">
                    Perempuan: {{ number_format($genderValues[1] ?? 0) }}
                </div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Chart Status Kependudukan</div>
            <canvas id="statusChart"></canvas>
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
            <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                <h3 style="margin:0;">Analisis Dinamika Penduduk</h3>
                <form method="GET" action="{{ route('public.statistik') }}" style="display:flex; align-items:center; gap:10px;">
                    <label for="tahun_analisis" style="font-size:13px; color:#475569; font-weight:600;">Tahun</label>
                    <select id="tahun_analisis" name="tahun_analisis" onchange="this.form.submit()" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px; background:#fff;">
                        @foreach($tahunOptions as $value => $label)
                            <option value="{{ $value }}" {{ (int) $value === (int) $analysisYear ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
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
        <div class="map-legend" style="margin-top: 14px; padding: 14px; background: #fff; border-radius: 12px; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);">
            <p style="margin: 0 0 10px; font-weight: 700; color: #0f172a; font-size: 14px;">Keterangan Peta:</p>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <svg width="24" height="32" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 0C5.37258 0 0 5.37258 0 12C0 20 12 32 12 32C12 32 24 20 24 12C24 5.37258 18.6274 0 12 0Z" fill="#FCD34D"/>
                        <circle cx="12" cy="12" r="4" fill="#fff"/>
                    </svg>
                    <span style="font-size: 13px; color: #475569;">Dusun</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <svg width="24" height="32" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 0C5.37258 0 0 5.37258 0 12C0 20 12 32 12 32C12 32 24 20 24 12C24 5.37258 18.6274 0 12 0Z" fill="#065F46"/>
                        <circle cx="12" cy="12" r="4" fill="#fff"/>
                    </svg>
                    <span style="font-size: 13px; color: #475569;">RW</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <svg width="24" height="32" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 0C5.37258 0 0 5.37258 0 12C0 20 12 32 12 32C12 32 24 20 24 12C24 5.37258 18.6274 0 12 0Z" fill="#1E40AF"/>
                        <circle cx="12" cy="12" r="4" fill="#fff"/>
                    </svg>
                    <span style="font-size: 13px; color: #475569;">RT</span>
                </div>
            </div>
        </div>
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
        labels: [@json($genderLabels[1]), @json($genderLabels[0])],
        datasets: [{
            data: [@json($genderValues[1]), @json($genderValues[0])],
            backgroundColor: [c.warning, c.primary],
            borderWidth: 0
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
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
    options: {
        responsive: true,
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true },
            y: {
                ticks: {
                    autoSkip: false,
                    font: { size: 11 }
                }
            }
        }
    }
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

// Fungsi untuk membuat location pin SVG dengan warna berbeda
function createLocationPinMarker(color) {
    const svg = `
        <svg width="40" height="52" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 0C5.37258 0 0 5.37258 0 12C0 20 12 32 12 32C12 32 24 20 24 12C24 5.37258 18.6274 0 12 0Z" fill="${color}"/>
            <circle cx="12" cy="12" r="4" fill="#fff"/>
        </svg>
    `;
    
    return L.divIcon({
        html: svg,
        iconSize: [40, 52],
        iconAnchor: [20, 52],
        popupAnchor: [0, -52],
        className: 'custom-location-pin'
    });
}

// Marker untuk Dusun (warna kuning)
const dusunMapData = @json($dusunPopulationRows);
let hasMarker = false;

dusunMapData.forEach((dusun) => {
    if (dusun.lat === null || dusun.lng === null) {
        return;
    }
    hasMarker = true;
    const marker = L.marker([dusun.lat, dusun.lng], { icon: createLocationPinMarker('#FCD34D') }).addTo(map);
    marker.bindPopup(`<b>${dusun.nama}</b> (Dusun)<br>Latitude: ${dusun.lat}<br>Longitude: ${dusun.lng}`);
});

// Marker untuk RW (warna biru)
const rwMapData = @json($rwMapRows);
rwMapData.forEach((rw) => {
    if (rw.latitude === null || rw.longitude === null) {
        return;
    }
    hasMarker = true;
    const marker = L.marker([rw.latitude, rw.longitude], { icon: createLocationPinMarker('#065F46') }).addTo(map);
    marker.bindPopup(`<b>${rw.nama}</b> (RW ${rw.nomor_rw})<br>Latitude: ${rw.latitude}<br>Longitude: ${rw.longitude}`);
});

// Marker untuk RT (warna biru gelap)
const rtMapData = @json($rtMapRows);
rtMapData.forEach((rt) => {
    if (rt.latitude === null || rt.longitude === null) {
        return;
    }
    hasMarker = true;
    const marker = L.marker([rt.latitude, rt.longitude], { icon: createLocationPinMarker('#1E40AF') }).addTo(map);
    marker.bindPopup(`<b>${rt.nama}</b> (RT ${rt.nomor_rt})<br>Latitude: ${rt.latitude}<br>Longitude: ${rt.longitude}`);
});

if (!hasMarker) {
    map.setView([{{ (float) $mapCenterLat }}, {{ (float) $mapCenterLng }}], 11);
}
</script>
@endpush
