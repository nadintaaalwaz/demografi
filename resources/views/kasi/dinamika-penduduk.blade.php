@extends('kasi.layout')

@section('title', 'Dinamika Penduduk')
@section('page-title', 'Dinamika Penduduk')

@push('styles')
<style>
    .dinamika-wrap {
        background: linear-gradient(180deg, #063b34 0%, #075b4f 100%);
        border-radius: 18px;
        padding: 20px;
        color: #e6fffb;
        box-shadow: 0 10px 28px rgba(5, 52, 45, 0.25);
    }

    .dinamika-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .dinamika-title {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .dinamika-title h2 {
        font-size: 28px;
        color: #e8ff3f;
        margin: 0;
    }

    .dinamika-title p {
        margin: 0;
        color: rgba(224, 255, 246, 0.85);
        font-size: 13px;
    }

    .year-select {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(227, 239, 38, 0.15);
        color: #f5ff96;
        border: 1px solid rgba(227, 239, 38, 0.35);
        border-radius: 10px;
        padding: 0 12px;
        height: 38px;
    }

    .year-select select {
        border: none;
        background: transparent;
        color: #f5ff96;
        font-weight: 600;
        outline: none;
        cursor: pointer;
    }

    .dinamika-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-manual-input {
        height: 38px;
        border: 1px solid rgba(227, 239, 38, 0.45);
        background: rgba(227, 239, 38, 0.18);
        color: #f7ffb2;
        border-radius: 10px;
        padding: 0 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .manual-form-card {
        display: none;
        background: rgba(9, 100, 85, 0.72);
        border: 1px solid rgba(96, 225, 194, 0.25);
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 12px;
    }

    .manual-form-card.active {
        display: block;
    }

    .manual-form-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        margin-top: 10px;
    }

    .manual-form-field label {
        display: block;
        font-size: 12px;
        margin-bottom: 6px;
        color: rgba(224, 255, 246, 0.9);
    }

    .manual-form-field input,
    .manual-form-field select {
        width: 100%;
        height: 38px;
        border-radius: 8px;
        border: 1px solid rgba(96, 225, 194, 0.35);
        background: rgba(5, 74, 63, 0.75);
        color: #e6fffb;
        padding: 0 10px;
        outline: none;
    }

    .manual-form-footer {
        margin-top: 12px;
        display: flex;
        justify-content: flex-end;
    }

    .btn-save-dinamika {
        height: 38px;
        border: 1px solid rgba(96, 225, 194, 0.45);
        background: rgba(39, 225, 176, 0.2);
        color: #b7ffe6;
        border-radius: 10px;
        padding: 0 14px;
        font-weight: 700;
        cursor: pointer;
    }

    .manual-note {
        margin-top: 8px;
        font-size: 12px;
        color: rgba(224, 255, 246, 0.85);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 12px;
    }

    .stat-card {
        background: rgba(9, 100, 85, 0.75);
        border: 1px solid rgba(96, 225, 194, 0.25);
        border-radius: 14px;
        padding: 14px;
        min-height: 116px;
    }

    .stat-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .stat-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(39, 225, 176, 0.15);
        color: #98ffd8;
        font-size: 13px;
    }

    .trend-badge {
        font-size: 11px;
        font-weight: 700;
        color: #9efccf;
        background: rgba(65, 224, 153, 0.2);
        border: 1px solid rgba(97, 245, 183, 0.35);
        border-radius: 999px;
        padding: 2px 8px;
    }

    .trend-badge.down {
        color: #ffb2b2;
        background: rgba(255, 126, 126, 0.15);
        border-color: rgba(255, 165, 165, 0.3);
    }

    .stat-label {
        font-size: 13px;
        color: rgba(224, 255, 246, 0.88);
        margin-bottom: 2px;
    }

    .stat-value {
        font-size: 34px;
        line-height: 1;
        font-weight: 800;
        color: #f7fff0;
    }

    .stat-sub {
        font-size: 11px;
        color: rgba(217, 254, 246, 0.75);
    }

    .charts-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 12px;
        margin-bottom: 12px;
    }

    .charts-grid-bottom {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .chart-card {
        background: rgba(9, 100, 85, 0.72);
        border: 1px solid rgba(96, 225, 194, 0.25);
        border-radius: 14px;
        padding: 14px;
    }

    .chart-title {
        margin: 0 0 2px;
        font-size: 18px;
        color: #e8ff3f;
        font-weight: 700;
    }

    .chart-subtitle {
        margin: 0 0 10px;
        color: rgba(224, 255, 246, 0.8);
        font-size: 12px;
    }

    .chart-canvas {
        width: 100%;
        height: 210px !important;
    }

    .chart-canvas.chart-tall {
        height: 230px !important;
    }

    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .charts-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .stats-grid,
        .charts-grid-bottom {
            grid-template-columns: 1fr;
        }

        .manual-form-grid {
            grid-template-columns: 1fr;
        }

        .dinamika-title h2 {
            font-size: 23px;
        }
    }
</style>
@endpush

@section('content')
<div class="dinamika-wrap">
    <div class="dinamika-header">
        <div class="dinamika-title">
            <h2><i class="fas fa-wave-square"></i> Dinamika Penduduk</h2>
            <p>Monitoring data kelahiran, kematian, dan perpindahan penduduk secara real-time.</p>
        </div>

        <div class="dinamika-actions">
            <button type="button" class="btn-manual-input" id="toggleManualFormBtn">
                <i class="fas fa-plus-circle"></i> Input Manual
            </button>
            <div class="year-select">
                <i class="far fa-calendar-alt"></i>
                <select id="yearSelect">
                    @foreach(($yearOptions ?? [now()->year]) as $year)
                        <option value="{{ $year }}" {{ (int) ($selectedYear ?? now()->year) === (int) $year ? 'selected' : '' }}>
                            Tahun {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="manual-form-card" id="manualFormCard">
        <form method="POST" action="{{ route('kasi.dinamika.store') }}">
            @csrf
            <div class="manual-form-grid">
                <div class="manual-form-field">
                    <label for="tahun">Tahun</label>
                    <input type="number" id="tahun" name="tahun" min="2000" max="2100" value="{{ old('tahun', $selectedYear ?? now()->year) }}" required>
                </div>
                <div class="manual-form-field">
                    <label for="bulan">Bulan</label>
                    <select id="bulan" name="bulan" required>
                        @php
                            $namaBulanIndonesia = [
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ];
                        @endphp
                        @foreach($namaBulanIndonesia as $nomorBulan => $labelBulan)
                            <option value="{{ $nomorBulan }}" {{ (int) old('bulan', now()->month) === $nomorBulan ? 'selected' : '' }}>
                                {{ $labelBulan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="manual-form-field">
                    <label for="jumlah_lahir">Jumlah Lahir</label>
                    <input type="number" id="jumlah_lahir" name="jumlah_lahir" min="0" value="{{ old('jumlah_lahir', 0) }}" required>
                </div>
                <div class="manual-form-field">
                    <label for="jumlah_meninggal">Jumlah Meninggal</label>
                    <input type="number" id="jumlah_meninggal" name="jumlah_meninggal" min="0" value="{{ old('jumlah_meninggal', 0) }}" required>
                </div>
                <div class="manual-form-field">
                    <label for="jumlah_masuk">Jumlah Masuk</label>
                    <input type="number" id="jumlah_masuk" name="jumlah_masuk" min="0" value="{{ old('jumlah_masuk', 0) }}" required>
                </div>
                <div class="manual-form-field">
                    <label for="jumlah_keluar">Jumlah Keluar</label>
                    <input type="number" id="jumlah_keluar" name="jumlah_keluar" min="0" value="{{ old('jumlah_keluar', 0) }}" required>
                </div>
            </div>
            <p class="manual-note">Input dinamika dilakukan manual sebagai rekap bulanan (bukan dari file Excel penduduk).</p>
            <div class="manual-form-footer">
                <button type="submit" class="btn-save-dinamika">
                    <i class="fas fa-save"></i> Simpan Rekap
                </button>
            </div>
        </form>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-icon"><i class="fas fa-baby"></i></span>
                <span class="trend-badge {{ ($trendKelahiran['down'] ?? false) ? 'down' : '' }}">{{ $trendKelahiran['label'] ?? '+0%' }}</span>
            </div>
            <div class="stat-label">Kelahiran</div>
            <div class="stat-value">{{ number_format($totalKelahiran ?? 0) }}</div>
            <div class="stat-sub">Tahun {{ $selectedYear ?? now()->year }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-icon"><i class="fas fa-skull-crossbones"></i></span>
                <span class="trend-badge {{ ($trendKematian['down'] ?? false) ? 'down' : '' }}">{{ $trendKematian['label'] ?? '+0%' }}</span>
            </div>
            <div class="stat-label">Kematian</div>
            <div class="stat-value">{{ number_format($totalKematian ?? 0) }}</div>
            <div class="stat-sub">Tahun {{ $selectedYear ?? now()->year }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-icon"><i class="fas fa-sign-in-alt"></i></span>
                <span class="trend-badge {{ ($trendMigrasiMasuk['down'] ?? false) ? 'down' : '' }}">{{ $trendMigrasiMasuk['label'] ?? '+0%' }}</span>
            </div>
            <div class="stat-label">Pindah Masuk</div>
            <div class="stat-value">{{ number_format($totalMigrasiMasuk ?? 0) }}</div>
            <div class="stat-sub">Tahun {{ $selectedYear ?? now()->year }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-icon"><i class="fas fa-sign-out-alt"></i></span>
                <span class="trend-badge {{ ($trendMigrasiKeluar['down'] ?? false) ? 'down' : '' }}">{{ $trendMigrasiKeluar['label'] ?? '+0%' }}</span>
            </div>
            <div class="stat-label">Pindah Keluar</div>
            <div class="stat-value">{{ number_format($totalMigrasiKeluar ?? 0) }}</div>
            <div class="stat-sub">Tahun {{ $selectedYear ?? now()->year }}</div>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-card">
            <h3 class="chart-title">Tren Dinamika Bulanan</h3>
            <p class="chart-subtitle">Perbandingan kelahiran dan kematian per bulan</p>
            <canvas id="trendChart" class="chart-canvas chart-tall"></canvas>
        </div>

        <div class="chart-card">
            <h3 class="chart-title">Pertumbuhan Bersih</h3>
            <p class="chart-subtitle">Total penambahan penduduk per bulan</p>
            <canvas id="growthChart" class="chart-canvas chart-tall"></canvas>
        </div>
    </div>

    <div class="charts-grid-bottom">
        <div class="chart-card">
            <h3 class="chart-title">Analisis Migrasi</h3>
            <p class="chart-subtitle">Pindah masuk dan pindah keluar</p>
            <canvas id="migrationChart" class="chart-canvas"></canvas>
        </div>

        <div class="chart-card">
            <h3 class="chart-title">Perbandingan Tahunan</h3>
            <p class="chart-subtitle">Ringkasan dinamika 5 tahun terakhir</p>
            <canvas id="yearlyChart" class="chart-canvas"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const toggleManualFormBtn = document.getElementById('toggleManualFormBtn');
    const manualFormCard = document.getElementById('manualFormCard');
    const yearSelect = document.getElementById('yearSelect');

    if (toggleManualFormBtn && manualFormCard) {
        toggleManualFormBtn.addEventListener('click', function () {
            manualFormCard.classList.toggle('active');
        });
    }

    @if($errors->any())
        manualFormCard?.classList.add('active');
    @endif

    if (yearSelect) {
        yearSelect.addEventListener('change', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('tahun', this.value);
            window.location.href = url.toString();
        });
    }

    const textColor = '#d9fff3';
    const gridColor = 'rgba(190, 255, 237, 0.12)';
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const kelahiranSeries = @json($kelahiranSeries ?? array_fill(0, 12, 0));
    const kematianSeries = @json($kematianSeries ?? array_fill(0, 12, 0));
    const migrasiMasukSeries = @json($migrasiMasukSeries ?? array_fill(0, 12, 0));
    const migrasiKeluarSeries = @json($migrasiKeluarSeries ?? array_fill(0, 12, 0));
    const growthSeries = @json($growthSeries ?? array_fill(0, 12, 0));
    const yearlyLabels = @json($yearlyLabels ?? []);
    const yearlyLahir = @json($yearlyLahir ?? []);
    const yearlyMeninggal = @json($yearlyMeninggal ?? []);
    const yearlyMasuk = @json($yearlyMasuk ?? []);
    const yearlyKeluar = @json($yearlyKeluar ?? []);

    const baseScales = {
        x: {
            ticks: { color: textColor, font: { size: 11 } },
            grid: { color: gridColor }
        },
        y: {
            ticks: { color: textColor, font: { size: 11 } },
            grid: { color: gridColor },
            beginAtZero: true
        }
    };

    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Kelahiran',
                    data: kelahiranSeries,
                    borderColor: '#e7ff3a',
                    backgroundColor: 'rgba(231, 255, 58, 0.1)',
                    tension: 0.35,
                    fill: true,
                    borderWidth: 2
                },
                {
                    label: 'Kematian',
                    data: kematianSeries,
                    borderColor: '#ff6b6b',
                    backgroundColor: 'rgba(255, 107, 107, 0.08)',
                    tension: 0.35,
                    fill: true,
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: textColor } } },
            scales: baseScales
        }
    });

    new Chart(document.getElementById('growthChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Pertumbuhan Bersih',
                    data: growthSeries,
                    backgroundColor: '#4ade80',
                    borderRadius: 8,
                    maxBarThickness: 18
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: textColor } } },
            scales: baseScales
        }
    });

    new Chart(document.getElementById('migrationChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Pindah Masuk',
                    data: migrasiMasukSeries,
                    borderColor: '#60f3b8',
                    backgroundColor: 'rgba(96, 243, 184, 0.15)',
                    tension: 0.35,
                    borderWidth: 2,
                    pointRadius: 3
                },
                {
                    label: 'Pindah Keluar',
                    data: migrasiKeluarSeries,
                    borderColor: '#f472d0',
                    backgroundColor: 'rgba(244, 114, 208, 0.15)',
                    tension: 0.35,
                    borderWidth: 2,
                    pointRadius: 3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: textColor } } },
            scales: baseScales
        }
    });

    new Chart(document.getElementById('yearlyChart'), {
        type: 'bar',
        data: {
            labels: yearlyLabels,
            datasets: [
                {
                    label: 'Kelahiran',
                    data: yearlyLahir,
                    backgroundColor: '#e7ff3a',
                    borderRadius: 8,
                    maxBarThickness: 14
                },
                {
                    label: 'Kematian',
                    data: yearlyMeninggal,
                    backgroundColor: '#ff6b6b',
                    borderRadius: 8,
                    maxBarThickness: 14
                },
                {
                    label: 'Masuk',
                    data: yearlyMasuk,
                    backgroundColor: '#4ade80',
                    borderRadius: 8,
                    maxBarThickness: 14
                },
                {
                    label: 'Keluar',
                    data: yearlyKeluar,
                    backgroundColor: '#f472d0',
                    borderRadius: 8,
                    maxBarThickness: 14
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: textColor } } },
            scales: baseScales
        }
    });
</script>
@endpush
