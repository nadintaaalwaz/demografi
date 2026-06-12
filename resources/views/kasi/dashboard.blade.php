@extends('kasi.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Desa Sebalor')
@php
$dusunLabels = $dusunLabels ?? [];
$dusunValues = $dusunValues ?? [];

$rwLabels = $rwLabels ?? [];
$rwValues = $rwValues ?? [];

$rtLabels = $rtLabels ?? [];
$rtValues = $rtValues ?? [];
@endphp
@push('styles')
<style>

.stats-grid{
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:20px;
    margin-bottom:25px;
}

.age-grid{
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:20px;
    margin-bottom:30px;
}

.stat-card{
    background:linear-gradient(135deg,#0b6b57,#075a49);
    border-radius:24px;
    padding:22px;
    color:white;
    min-height:160px;
    position:relative;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.15);
    transition:.3s;
}

.stat-card:hover{
    transform:translateY(-6px);
}

.stat-card::before{
    content:'';
    position:absolute;
    width:100px;
    height:100px;
    border-radius:50%;
    background:rgba(255,255,255,.07);
    right:-30px;
    top:-30px;
}

.stat-label{
    font-size:14px;
    color:#d8f7ed;
    font-weight:600;
    margin-bottom:15px;
}

.stat-value{
    font-size:38px;
    font-weight:800;
    color:white;
}

.stat-sub{
    margin-top:10px;
    color:#d5ece6;
    font-size:13px;
    line-height:1.5;
}

.age-card{
    min-height:120px;
}

.charts-grid{
    display:grid;
    grid-template-columns:1fr 1.5fr 1fr;
    gap:25px;
    margin-bottom:35px;
}

.chart-card{
    background:#fff;
    border-radius:24px;
    padding:24px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.chart-title{
    font-size:18px;
    font-weight:700;
    color:#0C342C;
}

.chart-header{
    margin-bottom:20px;
}

.gender-legend{
    display:flex;
    justify-content:center;
    gap:20px;
    margin-top:15px;
}

.gender-legend-item{
    display:flex;
    align-items:center;
    gap:8px;
}

.gender-legend-dot{
    width:12px;
    height:12px;
    border-radius:50%;
}

.chart-card canvas{
    max-height:300px!important;
}

.occupation-section{
    background:#fff;
    border-radius:24px;
    padding:28px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.occupation-table{
    width:100%;
    border-collapse:collapse;
}

.occupation-table th{
    background:#f8fafc;
    padding:15px;
}

.occupation-table td{
    padding:15px;
}

.occupation-bar{
    height:8px;
    background:#e2e8f0;
    border-radius:30px;
}

.occupation-bar-fill{
    height:100%;
    border-radius:30px;
    background:linear-gradient(90deg,#0b6b57,#0C342C);
}

@media(max-width:1200px){

    .stats-grid{
        grid-template-columns:repeat(2,1fr);
    }

    .age-grid{
        grid-template-columns:repeat(2,1fr);
    }

    .charts-grid{
        grid-template-columns:1fr;
    }
}

@media(max-width:768px){

    .stats-grid,
    .age-grid{
        grid-template-columns:1fr;
    }
}
.occupation-header{
    margin-bottom:20px;
    border-bottom:2px solid #eef2f7;
    padding-bottom:15px;
}

.occupation-title{
    font-size:20px;
    font-weight:700;
    color:#0C342C;
}

.occupation-number{
    font-size:16px;
    font-weight:700;
    color:#0C342C;
}

.occupation-percentage{
    font-size:12px;
    color:#64748b;
    margin-top:5px;
}
.region-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:25px;
    margin-bottom:35px;
}

.mini-summary{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:12px;
    margin-top:20px;
}

.mini-box{
    background:#0b6b57;
    color:white;
    border-radius:16px;
    padding:12px;
    text-align:center;
}

.mini-box strong{
    display:block;
    font-size:18px;
}

.mini-box span{
    font-size:12px;
    opacity:.9;
}

/* Match RW/RT UI from dashboard kasun */
.rw-tabs{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    margin-bottom:14px;
}

.rw-tab{
    border: 1px solid rgba(7,102,83,0.2);
    background: rgba(7,102,83,0.06);
    color:#0C342C;
    padding:8px 14px;
    border-radius:999px;
    font-weight:800;
    font-size:13px;
    cursor:pointer;
    transition:0.2s ease;
}

.rw-tab:hover{
    transform: translateY(-1px);
    background: rgba(227,239,38,0.12);
    border-color: rgba(7,102,83,0.35);
}

.rw-tab.active{
    background:#E3EF26;
    color:#0C342C;
    border-color:#E3EF26;
}

.rt-summary-list{
    display:flex;
    flex-direction:column;
    gap:10px;
    margin-top:14px;
    max-height:100%;
    overflow:auto;
    padding-right:4px;
}

.rt-summary-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:12px 14px;
    border-radius:12px;
    background:rgba(7,102,83,0.05);
    border:1px solid rgba(7,102,83,0.12);
}

.rt-summary-item .label{
    font-weight:700;
    color:#0C342C;
}

.rt-summary-item .value{
    font-weight:900;
    color:#076653;
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
            'label' => 'Jumlah Remaja (12–19 Tahun)',
            'value' => $ageValues[2] ?? 0,
            'sub' => 'Masa pubertas dan pencarian jati diri, penting untuk edukasi kesehatan reproduksi dan mental.',
        ],
        [
            'label' => 'Jumlah Dewasa (20–59 Tahun)',
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

@php
$totalAge = array_sum($ageValues ?? []);
@endphp

@foreach($ageCards as $card)

@php
$persen = $totalAge > 0
    ? round(($card['value'] / $totalAge) * 100)
    : 0;
@endphp

<div class="stat-card age-card">

    <div class="stat-label">
        {{ str_replace('Jumlah ','',$card['label']) }}
    </div>

    <div class="stat-value">
        {{ number_format($card['value']) }}
    </div>

    <div style="margin-top:15px;">
        <small>{{ $persen }}%</small>

        <div style="
            width:100%;
            height:8px;
            background:rgba(255,255,255,.2);
            border-radius:20px;
            margin-top:6px;">

            <div style="
                width:{{ $persen }}%;
                height:100%;
                background:#4ade80;
                border-radius:20px;">
            </div>

        </div>
    </div>

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
<div class="region-grid">

    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Penduduk per Dusun</h2>
        </div>
        <canvas id="dusunChart"></canvas>

        <div class="mini-summary">
            @foreach($dusunLabels as $i => $label)
                <div class="mini-box">
                    <strong>{{ number_format($dusunValues[$i] ?? 0) }}</strong>
                    <span>{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Jumlah Penduduk per RW</h2>
        </div>
        <canvas id="rwChart"></canvas>
        <div id="rwSummary" style="display:flex;gap:12px;margin-top:16px;flex-wrap:wrap"></div>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Jumlah Penduduk per RT</h2>
        </div>
        <div class="rw-rt-body" style="display:grid;grid-template-columns:minmax(0, 1.55fr) minmax(240px, 0.85fr);gap:16px;align-items:start;">
            <div class="rw-rt-chart-area" style="min-width:0;">
                <div class="chart-container" style="height:180px;display:flex;align-items:center;justify-content:center;padding:6px 0;">
                    <canvas id="rtChart"></canvas>
                </div>
                <div id="rwTabs" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:10px;margin-bottom:0;"></div>
            </div>
            <div class="rt-summary-panel" style="min-width:220px;">
                <h3 class="chart-title">Detail RT</h3>
                <div id="rtList" style="display:flex;flex-direction:column;gap:10px;margin-top:14px;max-height:100%;overflow:auto;padding-right:4px;"></div>
            </div>
        </div>
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
    type: 'doughnut',
    data: {
        labels: ['Laki-laki', 'Perempuan'],
        datasets: [{
            data: [{{ $totalLakiLaki ?? 0 }}, {{ $totalPerempuan ?? 0 }}],
            backgroundColor: [chartColors.primary, chartColors.warning],
            borderWidth: 0
        }]
    },
    cutout:'72%',
    options:{
        responsive:true,
        plugins:{
            legend:{
                display:false
            }
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
            backgroundColor:'#ec4899',
            borderRadius:12,
            barThickness:18
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
    type:'doughnut',
    data: {
        labels: educationLabels,
        datasets:[{
            data:educationValues,
            backgroundColor:[
                '#94a3b8',
                '#60a5fa',
                '#22c55e',
                '#facc15',
                '#fb923c',
                '#ec4899',
                '#8b5cf6'
            ],
            borderWidth:0
        }]
    },
    cutout:'65%',
    options:{
        responsive:true,
        plugins:{
            legend:{
                position:'bottom'
            }
        }
    }
});

// Distribution charts (using realtime JSON endpoint) - keep other charts untouched.
const dusunCtx = document.getElementById('dusunChart').getContext('2d');
const rwCtx = document.getElementById('rwChart').getContext('2d');
const rtCtx = document.getElementById('rtChart').getContext('2d');

let dusunChart = new Chart(dusunCtx, {
    type: 'bar',
    data: {
        labels: @json($dusunLabels),
        datasets: [{
            data: @json($dusunValues),
            backgroundColor: '#d9f01f',
            borderRadius: 10
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = @json(max((int)($totalPenduduk ?? 0),1));
                        const value = context.raw ?? 0;
                        const persen = total > 0 ? roundTo1((value / total) * 100) : 0;
                        return `Jumlah: ${Number(value).toLocaleString('id-ID')} (${persen}%)`;
                    }
                }
            }
        }
    }
});

let rwChart = new Chart(rwCtx, {
    type: 'bar',
    data: {
        labels: @json($rwLabels),
        datasets: [{
            data: @json($rwValues),
            backgroundColor: '#7dd3c0',
            borderRadius: 10
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = @json(max((int)($totalPenduduk ?? 0),1));
                        const value = context.raw ?? 0;
                        const persen = total > 0 ? roundTo1((value / total) * 100) : 0;
                        return `Jumlah: ${Number(value).toLocaleString('id-ID')} (${persen}%)`;
                    }
                }
            }
        }
    }
});

let rtChart = new Chart(rtCtx, {
    type: 'bar',
    data: {
        labels: @json($rtLabels),
        datasets: [{
            data: @json($rtValues),
            backgroundColor: '#a855f7',
            borderRadius: 10
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = @json(max((int)($totalPenduduk ?? 0),1));
                        const value = context.raw ?? 0;
                        const persen = total > 0 ? roundTo1((value / total) * 100) : 0;
                        return `Jumlah: ${Number(value).toLocaleString('id-ID')} (${persen}%)`;
                    }
                }
            }
        }
    }
});

function roundTo1(n) {
    return Math.round((n + Number.EPSILON) * 10) / 10;
}

// Fetch realtime data for distribution charts
fetch('{{ route('kasi.dashboard.chart-data') }}', {
    method: 'GET',
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
})
.then(res => {
    if (!res.ok) throw new Error('Failed to load distribution data');
    return res.json();
})
.then(data => {
    // Dusun
    if (data.dusun) {
        dusunChart.data.labels = data.dusun.labels;
        dusunChart.data.datasets[0].data = data.dusun.data;
        dusunChart.update();
    }

    // RW
    if (data.rw) {
        rwChart.data.labels = data.rw.labels;
        rwChart.data.datasets[0].data = data.rw.data;
        rwChart.update();

        // Populate RW summary cards (mimic kasun UI)
        try {
            const rwSummary = document.getElementById('rwSummary');
            if (rwSummary) {
                rwSummary.innerHTML = '';
                (data.rw.labels || []).forEach((label, idx) => {
                    const count = Number((data.rw.data || [])[idx] || 0);
                    const card = document.createElement('div');
                    card.style.cssText = 'background: rgba(227,239,38,0.08); padding:12px 14px; border-radius:12px; min-width:110px; text-align:center; border:1px solid rgba(7,102,83,0.12)';
                    card.innerHTML = `<div style="font-weight:800; font-size:18px; color:#0C342C">${new Intl.NumberFormat('id-ID').format(count)}</div><div style="font-size:12px; color:rgba(12,52,44,0.7); margin-top:4px">${label}</div>`;
                    rwSummary.appendChild(card);
                });
            }
        } catch (e) {}

        // Build RW->RT groups & tabs (use structured details, no regex parsing)
        try {
            const rwTabs = document.getElementById('rwTabs');
            const rtList = document.getElementById('rtList');
            if (rwTabs && rtList && data && data.rw && data.rt) {
                const rwLabels = data.rw.labels || [];

                // Group RT rows by RW label
                const rtGroups = {};
                const rtDetails = data.rt.details || [];
                rtDetails.forEach((d) => {
                    const idDusun = String(d.id_dusun ?? '');
                    const rwNo = String(d.rw ?? '');
                    // label convention for kasi: "{DusunName} - RW X" when scopeDusun is null, otherwise "RW X"
                    // Build grouping key by matching the RW number against existing RW labels.
                    const labelRw = `RW ${rwNo}`;
                    const candidateKey = rwLabels.find(l => String(l).trim().endsWith(labelRw)) || labelRw;

                    const rtLabel = `RT ${d.rt}`;
                    const key = candidateKey;

                    if (!rtGroups[key]) rtGroups[key] = [];
                    rtGroups[key].push({ label: rtLabel, count: Number(d.count || 0) });
                });

                const renderRTForRW = (rwLabel) => {
                    const rows = (rtGroups[rwLabel] || []).slice();
                    rtList.innerHTML = '';
                    if (!rows.length) {
                        rtList.innerHTML = '<div style="color:#64748b; opacity:0.9; padding:12px 0;">Tidak ada data RT untuk RW ini.</div>';
                        rtChart.data.labels = [];
                        rtChart.data.datasets[0].data = [];
                        rtChart.update();
                        return;
                    }

                    rows.forEach((row) => {
                        const item = document.createElement('div');
                        item.className = 'rt-summary-item';
                        item.innerHTML = `<div class="label">${row.label}</div><div class="value">${new Intl.NumberFormat('id-ID').format(row.count)} jiwa</div>`;
                        rtList.appendChild(item);
                    });

                    rtChart.data.labels = rows.map(r => r.label);
                    rtChart.data.datasets[0].data = rows.map(r => r.count);
                    rtChart.update();
                };

                // tabs
                rwTabs.innerHTML = '';
                rwLabels.forEach((rwLabel, index) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'rw-tab' + (index === 0 ? ' active' : '');
                    btn.textContent = rwLabel;
                    btn.addEventListener('click', () => {
                        rwTabs.querySelectorAll('.rw-tab').forEach(el => el.classList.remove('active'));
                        btn.classList.add('active');
                        renderRTForRW(rwLabel);
                    });
                    rwTabs.appendChild(btn);
                });

                renderRTForRW(rwLabels[0] || 'RW');
            }
        } catch (e) {}

    }

    // RT (initial - will be overridden by RW tab rendering)
    if (data.rt) {
        rtChart.data.labels = data.rt.labels || [];
        rtChart.data.datasets[0].data = data.rt.data || [];
        rtChart.update();
    }
})
.catch(() => {
    // Keep initial blade-rendered charts if API fails.
});

</script>
@endpush
