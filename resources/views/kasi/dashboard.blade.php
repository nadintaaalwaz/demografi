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
/* Base Theme Overrides - Light Soft Teal Concept */
body {
    background-color: #E1EFED;
    color: #333333;
}

/* Grid Atas Diselaraskan dengan Grid Bawah */
.stats-grid-custom {
    display: grid;
    grid-template-columns: 1.2fr 1fr 1.8fr 1.8fr;
    gap: 20px;
    margin-bottom: 20px;
}

.age-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

/* Card Style UI */
.stat-card {
    background: #075a49;
    border-radius: 16px;
    padding: 20px;
    color: white;
    min-height: 130px;
    position: relative;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06);
    transition: .3s;
    border: 1px solid rgba(255,255,255,0.05);
}

.stat-card:hover {
    transform: translateY(-3px);
}

.stat-label {
    font-size: 13px;
    color: #a3e635; 
    font-weight: 600;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value {
    font-size: 34px;
    font-weight: 800;
    color: white;
}

.stat-sub {
    margin-top: 6px;
    color: #9cd4c5;
    font-size: 12px;
}

/* Gender & Mutation Progress Layout */
.bar-container {
    width: 100%;
    height: 12px;
    background: rgba(255,255,255,0.15);
    border-radius: 10px;
    margin-top: 12px;
    overflow: hidden;
    display: flex;
}

.bar-fill-left {
    height: 100%;
    background: #3b82f6; 
}

.bar-fill-right {
    height: 100%;
    background: #ec4899; 
}

.bar-mutasi-left {
    height: 100%;
    background: #f87171;
}

.bar-mutasi-right {
    height: 100%;
    background: #fbbf24;
}

.charts-grid {
    display: grid;
    grid-template-columns: 1.1fr 1.2fr 1.1fr;
    gap: 20px;
    margin-bottom: 25px;
}

.region-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.chart-card {
    background: #075a49; 
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
    min-height: 440px;
    color: #ffffff;
}

.chart-header {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 16px;
}

.chart-title {
    font-size: 18px;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
}

.chart-subtitle {
    font-size: 12px;
    color: #9cd4c5;
    margin-top: 4px;
}

/* Memastikan container pembungkus chart bulat berukuran identik */
.chart-container {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    width: 100%;
    min-height: 240px;
    max-height: 240px; /* Dikunci agar proporsi terkendali */
}

.chart-container canvas {
    width: 100% !important;
    height: 100% !important;
}

.gender-custom-legend {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 15px;
    width: 100%;
}

.gender-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    padding-bottom: 6px;
}

.gender-identity {
    display: flex;
    align-items: center;
    gap: 8px;
}

.gender-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.rt-layout-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    width: 100%;
    height: 100%;
}

.rw-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 12px;
    margin-top: 10px;
}

.rw-tab {
    border: none;
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    padding: 4px 12px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 11px;
    cursor: pointer;
}

.rw-tab.active {
    background: #a855f7;
    color: white;
}

.rt-side-pane-horizontal {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 15px;
}

.rt-list-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    padding: 10px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.rt-list-name {
    font-size: 12px;
    color: #e2e8f0;
}

.rt-list-val {
    font-weight: 700;
    font-size: 13px;
    color: #ffffff;
}

.bottom-badges-container {
    display: flex;
    gap: 8px;
    margin-top: 15px;
    justify-content: space-between;
}

.badge-summary-box {
    background: rgba(0, 0, 0, 0.12);
    border-radius: 10px;
    padding: 10px;
    flex: 1;
    text-align: center;
}

.badge-summary-box strong {
    display: block;
    font-size: 15px;
    color: #ffffff;
}

.badge-summary-box span {
    font-size: 11px;
    color: #9cd4c5;
}

.occupation-section {
    background: #075a49;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 8px 24px rgba(0,0,0,.08);
    color: #fff;
}

.occupation-title {
    font-size: 18px;
    font-weight: 700;
}

.occupation-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.occupation-table th {
    background: rgba(0,0,0,0.1);
    padding: 12px;
    color: #a3e635;
    text-align: left;
}

.occupation-table td {
    padding: 12px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.occupation-bar {
    height: 8px;
    background: rgba(255,255,255,0.1);
    border-radius: 30px;
}

.occupation-bar-fill {
    height: 100%;
    border-radius: 30px;
    background: linear-gradient(90deg, #a3e635, #22c55e);
}

@media(max-width: 1200px) {
    .stats-grid-custom, .age-grid { grid-template-columns: repeat(2, 1fr); }
    .charts-grid, .region-grid { grid-template-columns: 1fr; }
}
@media(max-width: 768px) {
    .stats-grid-custom, .age-grid, .charts-grid, .region-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
@php
    $totalMeninggalKeluar = (int) (($totalMeninggalKeluar ?? 0) ?: ((($totalMeninggal ?? 0) + ($totalKeluar ?? 0))));
    
    // Perhitungan Persentase Presisi untuk Bar Atas
    $totalG = ($totalLakiLaki ?? 0) + ($totalPerempuan ?? 0);
    $pctLki = $totalG > 0 ? round((($totalLakiLaki ?? 0) / $totalG) * 100) : 50;
    $pctPrm = $totalG > 0 ? 100 - $pctLki : 50;

    $totalMut = ($totalMeninggal ?? 0) + ($totalKeluar ?? 0);
    $pctMng = $totalMut > 0 ? round((($totalMeninggal ?? 0) / $totalMut) * 100) : 50;
    $pctPnd = $totalMut > 0 ? 100 - $pctMng : 50;

    $ageCards = [
        ['label' => 'Bayi & Balita', 'range' => '0-5 thn', 'value' => $ageValues[0] ?? 0, 'color' => '#f97316'],
        ['label' => 'Anak-anak', 'range' => '6-11 thn', 'value' => $ageValues[1] ?? 0, 'color' => '#a855f7'],
        ['label' => 'Remaja', 'range' => '12-19 thn', 'value' => $ageValues[2] ?? 0, 'color' => '#3b82f6'],
        ['label' => 'Dewasa', 'range' => '20-59 thn', 'value' => $ageValues[3] ?? 0, 'color' => '#22c55e'],
        ['label' => 'Lansia', 'range' => '60+ thn', 'value' => $ageValues[4] ?? 0, 'color' => '#14b8a6'],
    ];
@endphp

<div class="stats-grid-custom">
    <div class="stat-card">
        <div class="stat-label">Penduduk Aktif</div>
        <div class="stat-value">{{ number_format($totalPenduduk ?? 0) }}</div>
        <div class="stat-sub">jiwa terdaftar aktif</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Kepala Keluarga</div>
        <div class="stat-value">{{ number_format($totalKK ?? 0) }}</div>
        <div class="stat-sub">kartu keluarga aktif</div>
    </div>
    <div class="stat-card">
        <div class="stat-label" style="display:flex; justify-content:space-between;">
            <span>Jenis Kelamin</span>
            <span style="color:#ffffff; font-weight:normal;">{{$pctLki}}% / {{$pctPrm}}%</span>
        </div>
        <div style="display:flex; justify-content: space-between; align-items: center; margin-top:2px;">
            <div>
                <span style="font-size:22px; font-weight:800;">{{ number_format($totalLakiLaki ?? 0) }}</span>
                <span style="font-size:11px; display:block; color:#9cd4c5;">Laki-laki</span>
            </div>
            <div style="text-align: right;">
                <span style="font-size:22px; font-weight:800;">{{ number_format($totalPerempuan ?? 0) }}</span>
                <span style="font-size:11px; display:block; color:#9cd4c5;">Perempuan</span>
            </div>
        </div>
        <div class="bar-container">
            <div class="bar-fill-left" style="width: {{$pctLki}}%;"></div>
            <div class="bar-fill-right" style="width: {{$pctPrm}}%;"></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label" style="display:flex; justify-content:space-between;">
            <span>Mutasi Keluar</span>
            <span style="color:#ffffff; font-weight:normal;">{{$pctMng}}% / {{$pctPnd}}%</span>
        </div>
        <div style="display:flex; justify-content: space-between; align-items: center; margin-top:2px;">
            <div>
                <span style="font-size:22px; font-weight:800; color:#f87171;">{{ number_format($totalMeninggal ?? 0) }}</span>
                <span style="font-size:11px; display:block; color:#9cd4c5;">Meninggal</span>
            </div>
            <div style="text-align: right;">
                <span style="font-size:22px; font-weight:800; color:#fbbf24;">{{ number_format($totalKeluar ?? 0) }}</span>
                <span style="font-size:11px; display:block; color:#9cd4c5;">Pindah</span>
            </div>
        </div>
        <div class="bar-container">
            <div class="bar-mutasi-left" style="width: {{$pctMng}}%;"></div>
            <div class="bar-mutasi-right" style="width: {{$pctPnd}}%;"></div>
        </div>
    </div>
</div>

<div class="age-grid">
    @php $totalAge = array_sum($ageValues ?? []); @endphp
    @foreach($ageCards as $card)
        @php $persen = $totalAge > 0 ? round(($card['value'] / $totalAge) * 100) : 0; @endphp
        <div class="stat-card" style="min-height: 110px; padding: 16px;">
            <div style="display:flex; justify-content:space-between; font-size:12px; color:#9cd4c5;">
                <span>{{ $card['label'] }}</span>
                <span style="color:white; font-weight:700;">{{ $persen }}%</span>
            </div>
            <div class="stat-value" style="font-size: 26px; margin: 4px 0;">{{ number_format($card['value']) }}</div>
            <div style="font-size:11px; color:#cbd5e1; margin-bottom:6px;">{{ $card['range'] }}</div>
            <div style="width:100%; height:6px; background:rgba(255,255,255,.1); border-radius:10px;">
                <div style="width:{{ $persen }}%; height:100%; background:{{ $card['color'] }}; border-radius:10px;"></div>
            </div>
        </div>
    @endforeach
</div>

<div class="charts-grid">
    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Jenis Kelamin</h2>
            <span class="chart-subtitle">Distribusi gender penduduk aktif</span>
        </div>
        <div class="chart-container">
            <canvas id="genderChart"></canvas>
        </div>
        <div class="gender-custom-legend">
            <div class="gender-row">
                <div class="gender-identity">
                    <span class="gender-dot" style="background:#3b82f6;"></span>
                    <span>Laki-laki</span>
                </div>
                <span class="gender-value">{{ number_format($totalLakiLaki ?? 0) }} jiwa (51%)</span>
            </div>
            <div class="gender-row">
                <div class="gender-identity">
                    <span class="gender-dot" style="background:#ec4899;"></span>
                    <span>Perempuan</span>
                </div>
                <span class="gender-value">{{ number_format($totalPerempuan ?? 0) }} jiwa (49%)</span>
            </div>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Distribusi Usia</h2>
            <span class="chart-subtitle">Breakdown per kelompok umur</span>
        </div>
        <div class="chart-container">
            <canvas id="ageChart"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Tingkat Pendidikan</h2>
            <span class="chart-subtitle">Distribusi jenjang pendidikan warga</span>
        </div>
        <div class="chart-container">
            <canvas id="educationChart"></canvas>
        </div>
    </div>
</div>

<div class="region-grid">
    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Penduduk per Dusun</h2>
            <span class="chart-subtitle">Sebaran jiwa & KK tiap dusun</span>
        </div>
        <div class="chart-container">
            <canvas id="dusunChart"></canvas>
        </div>
        <div class="bottom-badges-container">
            @foreach($dusunLabels as $i => $label)
                <div class="badge-summary-box">
                    <strong>{{ number_format($dusunValues[$i] ?? 0) }}</strong>
                    <span>{{ str_replace('Dusun ', '', $label) }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Penduduk per RW</h2>
            <span class="chart-subtitle">Sebaran jiwa & KK tiap RW</span>
        </div>
        <div class="chart-container">
            <canvas id="rwChart"></canvas>
        </div>
        <div id="rwSummary" class="bottom-badges-container"></div>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Penduduk per RT</h2>
            <span class="chart-subtitle">Detail tiap RT dalam RW terpilih</span>
        </div>
        <div class="rt-layout-wrapper">
            <div id="rwTabs" class="rw-tabs"></div>
            <div class="chart-container">
                <canvas id="rtChart"></canvas>
            </div>
            <div class="rt-side-pane-horizontal" id="rtList"></div>
        </div>
    </div>
</div>

<div class="occupation-section">
    <div class="occupation-header">
        <h2 class="occupation-title">Informasi Pekerjaan Penduduk Aktif</h2>
    </div>
    <table class="occupation-table">
        <thead>
            <tr>
                <th style="width: 30%;">Jenis Pekerjaan</th>
                <th style="width: 20%; text-align: center;">Jumlah</th>
                <th style="width: 50%;">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @php $totalOccupation = array_sum($occupationValues ?? []); @endphp
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
                    <td colspan="3" style="text-align: center; color: #9cd4c5; padding: 24px;">Data pekerjaan tidak tersedia</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

<script>
Chart.defaults.color = '#9cd4c5';
Chart.defaults.plugins.legend.labels.color = '#ffffff';

const ageLabels = @json($ageLabels);
const ageValues = @json($ageValues);
const educationLabels = @json($educationLabels);
const educationValues = @json($educationValues);

// 1. Gender Chart 
const genderCtx = document.getElementById('genderChart').getContext('2d');
new Chart(genderCtx, {
    type: 'doughnut',
    data: {
        labels: ['Laki-laki', 'Perempuan'],
        datasets: [{
            data: [{{ $totalLakiLaki ?? 0 }}, {{ $totalPerempuan ?? 0 }}],
            backgroundColor: ['#3b82f6', '#ec4899'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%', 
        plugins: { legend: { display: false } }
    }
});

// 2. Age Distribution Chart
const ageCtx = document.getElementById('ageChart').getContext('2d');
new Chart(ageCtx, {
    type: 'bar',
    data: {
        labels: ageLabels,
        datasets: [{
            data: ageValues,
            backgroundColor: '#ec4899',
            borderRadius: 4,
            barThickness: 22
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#ffffff' } },
            y: { grid: { color: 'rgba(255,255,255,0.05)' }, beginAtZero: true }
        }
    }
});

// 3. Education Chart
const educationCtx = document.getElementById('educationChart').getContext('2d');
new Chart(educationCtx, {
    type: 'doughnut',
    data: {
        labels: educationLabels,
        datasets: [{
            data: educationValues,
            backgroundColor: ['#7dd3c0', '#64748b', '#3b82f6', '#eab308', '#f97316', '#ec4899', '#a855f7'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: {
                position: 'right',
                labels: { boxWidth: 10, font: { size: 10 } }
            }
        }
    }
});

// 4. Dusun Horizontal Bar Chart
const dusunCtx = document.getElementById('dusunChart').getContext('2d');
let dusunChart = new Chart(dusunCtx, {
    type: 'bar',
    data: {
        labels: @json($dusunLabels).map(l => l.replace('Dusun ', '')),
        datasets: [{
            data: @json($dusunValues),
            backgroundColor: '#d9f01f', 
            borderRadius: 4,
            barThickness: 12
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.05)' }, beginAtZero: true },
            y: { grid: { display: false }, ticks: { color: '#ffffff', font: { size: 10 } } }
        }
    }
});

// 5. RW Vertical Chart
const rwCtx = document.getElementById('rwChart').getContext('2d');
let rwChart = new Chart(rwCtx, {
    type: 'bar',
    data: {
        labels: @json($rwLabels).map(l => l.replace(/.*(RW\s*[-_:\s]*)/i, 'RW ').replace(/RW\s*0+(\d+)/i, 'RW $1')),
        datasets: [{
            data: @json($rwValues),
            backgroundColor: '#7dd3c0', 
            borderRadius: 4,
            barThickness: 24
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#ffffff' } },
            y: { grid: { color: 'rgba(255,255,255,0.05)' }, beginAtZero: true }
        }
    }
});

// 6. RT Curved Spline Chart
const rtCtx = document.getElementById('rtChart').getContext('2d');
let rtChart = new Chart(rtCtx, {
    type: 'line',
    data: {
        labels: @json($rtLabels),
        datasets: [{
            data: @json($rtValues),
            borderColor: '#c084fc',
            backgroundColor: 'rgba(192,132,252,0.15)',
            fill: true,
            tension: 0.4, 
            pointBackgroundColor: '#a855f7',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#ffffff' } },
            y: { grid: { color: 'rgba(255,255,255,0.05)' }, beginAtZero: true }
        }
    }
});

// Fetch Realtime Data Terstruktur & Stabil
document.addEventListener('DOMContentLoaded', () => {
    fetch('{{ route('kasi.dashboard.chart-data') }}', {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        // Update Dusun Chart
        if (data.dusun && data.dusun.labels) {
            dusunChart.data.labels = data.dusun.labels.map(l => l.replace('Dusun ', ''));
            dusunChart.data.datasets[0].data = data.dusun.data;
            dusunChart.update({ duration: 0 });
        }

        // Update RW & RT Chart
        if (data.rw && data.rw.labels) {
            const rawRwLabels = data.rw.labels; 
            
            rwChart.data.labels = rawRwLabels.map(l => {
                return l.replace(/.*(RW\s*[-_:\s]*)/i, 'RW ').replace(/RW\s*0+(\d+)/i, 'RW $1');
            });
            rwChart.data.datasets[0].data = data.rw.data;
            rwChart.update({ duration: 0 });

            // Render RW Summary Badges
            try {
                const rwSummary = document.getElementById('rwSummary');
                if (rwSummary) {
                    rwSummary.innerHTML = '';
                    rawRwLabels.forEach((label, idx) => {
                        const count = Number((data.rw.data || [])[idx] || 0);
                        const kkVal = Math.round(count / 3.7); 
                        const cleanLabel = label.replace(/.*(RW\s*[-_:\s]*)/i, 'RW ').replace(/RW\s*0+(\d+)/i, 'RW $1');

                        const card = document.createElement('div');
                        card.className = 'badge-summary-box';
                        card.innerHTML = `<strong>${count}</strong><div style="font-size:11px; color:#a3e635; font-weight:700;">${cleanLabel}</div><div style="font-size:10px; color:#9cd4c5;">${kkVal} KK</div>`;
                        rwSummary.appendChild(card);
                    });
                }
            } catch (e) { console.error(e); }

            // Grouping RT Data berdasarkan label RW asli (mencegah data merosot/hilang)
            try {
                const rwTabs = document.getElementById('rwTabs');
                const rtList = document.getElementById('rtList');
                
                if (rwTabs && rtList && data.rt && data.rt.details) {
                    const rtGroups = {};
                    
                    data.rt.details.forEach((d) => {
                        const rwNo = parseInt(d.rw ?? 0, 10);
                        
                        const candidateKey = rawRwLabels.find(l => {
                            const match = l.match(/RW\s*0*(\d+)/i);
                            return match && parseInt(match[1], 10) === rwNo;
                        }) || rawRwLabels[rwNo - 1] || `RW ${rwNo}`;

                        const rtLabel = `RT ${String(d.rt).padStart(2, '0')}`;
                        if (!rtGroups[candidateKey]) rtGroups[candidateKey] = [];
                        
                        rtGroups[candidateKey].push({ label: rtLabel, count: Number(d.count || 0) });
                    });

                    // Fungsi Render RT
                    const renderRTForRW = (targetRwLabel) => {
                        const rows = (rtGroups[targetRwLabel] || []).sort((a, b) => a.label.localeCompare(b.label));
                        
                        rtList.innerHTML = '';
                        if (!rows.length) {
                            rtList.innerHTML = '<div style="color:#9cd4c5; font-size:12px; padding: 10px;">Tidak ada data RT.</div>';
                            rtChart.data.labels = [];
                            rtChart.data.datasets[0].data = [];
                            rtChart.update({ duration: 0 });
                            return;
                        }

                        rows.forEach((row) => {
                            const item = document.createElement('div');
                            item.className = 'rt-list-card';
                            item.innerHTML = `<div class="rt-list-name">${row.label}</div><div class="rt-list-val">${row.count} jiwa</div>`;
                            rtList.appendChild(item);
                        });

                        rtChart.data.labels = rows.map(r => r.label);
                        rtChart.data.datasets[0].data = rows.map(r => r.count);
                        rtChart.update({ duration: 0 });
                    };

                    // Render Tombol Tab RW secara dinamis
                    rwTabs.innerHTML = '';
                    rawRwLabels.forEach((rwLabel, index) => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'rw-tab' + (index === 0 ? ' active' : '');
                        
                        let cleanBtnLabel = rwLabel.replace(/.*(RW\s*[-_:\s]*)/i, 'RW ').replace(/RW\s*0+(\d+)/i, 'RW $1');  
                        btn.textContent = cleanBtnLabel;
                        
                        btn.addEventListener('click', () => {
                            rwTabs.querySelectorAll('.rw-tab').forEach(el => el.classList.remove('active'));
                            btn.classList.add('active');
                            renderRTForRW(rwLabel);
                        });
                        rwTabs.appendChild(btn);
                    });

                    // Tampilkan data RT untuk RW pertama secara default
                    if (rawRwLabels.length > 0) {
                        renderRTForRW(rawRwLabels[0]);
                    }
                }
            } catch (e) {
                console.error("Error rendering RT Chart: ", e);
            }
        }
    })
    .catch((err) => {
        console.error("Fetch error: ", err);
    });
});
</script>
@endpush