@extends('kasi.layout')

@section('title', 'Laporan Demografi & Dinamika Penduduk')
@section('page-title', 'Laporan Demografi & Dinamika Penduduk')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .report-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .filter-section {
        background: #fff;
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 28px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        align-items: flex-end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .form-group select,
    .form-group input {
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.2s ease;
    }

    .form-group select:focus,
    .form-group input:focus {
        outline: none;
        border-color: #076653;
        box-shadow: 0 0 0 3px rgba(7, 102, 83, 0.1);
    }

    .filter-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 100%;
    }

    .filter-actions .btn {
        min-width: 190px;
    }

    @media (min-width: 768px) {
        .filter-actions {
            margin-left: auto;
            align-items: flex-end;
            width: auto;
        }
    }

    .hidden {
        display: none !important;
    }

    .report-panel {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 3px 12px rgba(15, 23, 42, 0.08);
        padding: 20px;
    }

    .summary-stat {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        height: 100%;
    }

    .summary-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin-bottom: 10px;
        background: #ecfeff;
        color: #0f766e;
    }

    .content-section {
        background: #fff;
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .section-title {
        font-size: 16px;
        font-weight: 800;
        color: #0C342C;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e2e8f0;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .summary-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 16px;
        border-radius: 8px;
        border-left: 4px solid #076653;
    }

    .summary-label {
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 6px;
    }

    .summary-value {
        font-size: 24px;
        font-weight: 800;
        color: #0C342C;
    }

    .chart-container {
        position: relative;
        height: 320px;
        margin-bottom: 24px;
    }

    .breakdown-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .breakdown-table thead {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .breakdown-table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.04em;
    }

    .breakdown-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    .breakdown-table tbody tr:hover {
        background: #f8fafc;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .loading {
        text-align: center;
        padding: 40px;
        color: #64748b;
    }

    .loading-spinner {
        display: inline-block;
        width: 40px;
        height: 40px;
        border: 4px solid #e2e8f0;
        border-top-color: #076653;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-bottom: 16px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .error-message {
        background: #fee2e2;
        color: #991b1b;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 16px;
    }

    .export-buttons {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        flex-wrap: wrap;
    }

    .chart-card {
        background: #fff;
        padding: 22px;
        border-radius: 28px;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.08);
        border: none;
        min-height: 340px;
        transition: transform 0.28s ease, box-shadow 0.28s ease;
        margin-bottom: 22px;
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

    .chart-card canvas {
        width: 100% !important;
        max-height: 260px !important;
    }

    @media (max-width: 768px) {
        .filter-row {
            grid-template-columns: 1fr;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .chart-container {
            height: 250px;
        }

        .chart-card {
            min-height: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="report-container">
    <div class="filter-section">
        <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px; font-weight: 800; color: #0C342C;">
            Filter Laporan
        </h3>

        <form id="filterForm" class="row g-3 align-items-end">
            @csrf

            <div class="form-group col-12 col-md-3">
                <label>Jenis Laporan <span style="color: #ef4444;">*</span></label>
                <select id="laporanTipe" name="laporan_tipe" required>
                    <option value="demografi" selected>Demografi</option>
                    <option value="dinamika">Dinamika</option>
                </select>
            </div>

            <div class="form-group col-12 col-md-2 hidden" id="tahunGroup">
                <label>Tahun <span style="color: #ef4444;">*</span></label>
                <select id="tahun" name="tahun" required>
                    @foreach($yearList as $year)
                        <option value="{{ $year }}" @selected($year == $currentYear)>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-12 col-md-2 hidden" id="bulanGroup">
                <label>Bulan <span style="color: #94a3b8; font-weight: 400;">(Opsional)</span></label>
                <select id="bulan" name="bulan">
                    <option value="">Semua Bulan</option>
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
            </div>

            <div class="form-group col-12 col-md-3">
                <label>Dusun <span style="color: #94a3b8; font-weight: 400;">(Opsional)</span></label>
                <select id="dusun" name="dusun_id">
                    <option value="">Semua Dusun</option>
                    @foreach($dusunList as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-actions col-12 col-md-auto ms-md-auto">
                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-funnel-fill me-1"></i>
                    Tampilkan Laporan
                </button>
                <button type="button" onclick="exportToPdf()" class="btn btn-outline-danger w-100">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i>
                    Download PDF
                </button>
                <button type="button" onclick="exportToExcel()" class="btn btn-outline-success w-100">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i>
                    Download Excel
                </button>
            </div>
        </form>
    </div>

    <div id="reportContent">
        <div class="loading">
            <div class="loading-spinner"></div>
            <p>Memuat data laporan...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

<script>
    let charts = {};
    let currentReportType = 'demografi';
    let currentFilters = {};

    function updateFilterVisibility() {
        const tahunGroup = document.getElementById('tahunGroup');
        const bulanGroup = document.getElementById('bulanGroup');

        if (currentReportType === 'demografi') {
            tahunGroup.classList.add('hidden');
            bulanGroup.classList.add('hidden');
            document.getElementById('bulan').value = '';
        } else {
            tahunGroup.classList.remove('hidden');
            bulanGroup.classList.remove('hidden');
        }
    }

    function destroyDemografiCharts() {
        ['genderChart', 'educationChart', 'occupationChart'].forEach(chartName => {
            if (charts[chartName]) {
                charts[chartName].destroy();
                charts[chartName] = null;
            }
        });
    }

    function destroyDinamikaChart() {
        if (charts.dinamikaChart) {
            charts.dinamikaChart.destroy();
            charts.dinamikaChart = null;
        }
    }

    function syncCurrentFiltersFromForm() {
        currentFilters = {
            tahun: currentReportType === 'demografi'
                ? '{{ $currentYear }}'
                : document.getElementById('tahun').value,
            bulan: currentReportType === 'demografi'
                ? null
                : (document.getElementById('bulan').value || null),
            dusun_id: document.getElementById('dusun').value || null,
        };
    }

    document.getElementById('laporanTipe').addEventListener('change', function () {
        currentReportType = this.value;
        updateFilterVisibility();
        syncCurrentFiltersFromForm();
        loadReportData();
    });

    document.getElementById('filterForm').addEventListener('submit', function (e) {
        e.preventDefault();

        syncCurrentFiltersFromForm();

        loadReportData();
    });

    function loadReportData() {
        const filters = {
            ...currentFilters,
            laporan_tipe: currentReportType,
        };

        document.getElementById('reportContent').innerHTML = `
            <div class="loading">
                <div class="loading-spinner"></div>
                <p>Memuat data ${currentReportType}...</p>
            </div>
        `;

        fetch('{{ route("kasi.laporan.data") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify(filters),
        })
            .then(response => {
                if (!response.ok) throw new Error('Response error: ' + response.status);
                return response.json();
            })
            .then(data => {
                if (currentReportType === 'demografi') {
                    destroyDinamikaChart();
                    renderDemografiReport(data);
                } else {
                    destroyDemografiCharts();
                    renderDinamikaReport(data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('reportContent').innerHTML = `
                    <div class="error-message">
                        Gagal memuat data laporan. Silakan coba lagi.
                    </div>
                `;
            });
    }

    function renderDemografiReport(data) {
        const palette = ['#076653', '#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#14b8a6', '#ec4899', '#06b6d4', '#84cc16'];

        const html = `
            <div class="report-panel mb-4">
                <div class="section-title mb-3"><i class="bi bi-people-fill me-2"></i>Ringkasan Demografi Penduduk</div>
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <div class="summary-stat">
                            <div class="summary-icon"><i class="bi bi-people"></i></div>
                            <div class="summary-label">Total Penduduk Aktif</div>
                            <div class="summary-value">${data.summary.totalPenduduk.toLocaleString('id-ID')}</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="summary-stat">
                            <div class="summary-icon"><i class="bi bi-gender-male"></i></div>
                            <div class="summary-label">Laki-laki</div>
                            <div class="summary-value">${data.summary.totalLakiLaki.toLocaleString('id-ID')}</div>
                            <div class="text-muted small mt-1">${data.summary.persenLakiLaki}%</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="summary-stat">
                            <div class="summary-icon"><i class="bi bi-gender-female"></i></div>
                            <div class="summary-label">Perempuan</div>
                            <div class="summary-value">${data.summary.totalPerempuan.toLocaleString('id-ID')}</div>
                            <div class="text-muted small mt-1">${data.summary.persenPerempuan}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="report-panel mb-4">
                <div class="section-title"><i class="bi bi-pie-chart-fill me-2"></i>Grafik Jenis Kelamin</div>
                <div class="chart-container">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h2 class="chart-title"><i class="bi bi-mortarboard-fill me-2"></i>Tingkat Pendidikan</h2>
                </div>
                <canvas id="educationChart"></canvas>
            </div>

            <div class="report-panel mb-4">
                <div class="section-title"><i class="bi bi-briefcase-fill me-2"></i>Grafik Pekerjaan</div>
                <div class="chart-container">
                    <canvas id="occupationChart"></canvas>
                </div>
            </div>
        `;

        document.getElementById('reportContent').innerHTML = html;

        if (charts.genderChart) charts.genderChart.destroy();
        charts.genderChart = new Chart(document.getElementById('genderChart'), {
            type: 'doughnut',
            data: {
                labels: data.genderChart.labels,
                datasets: [{
                    data: data.genderChart.data,
                    backgroundColor: ['#076653', '#f59e0b'],
                    borderWidth: 2,
                    borderColor: '#fff',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        reverse: true,
                    },
                },
            },
        });

        if (charts.educationChart) charts.educationChart.destroy();
        charts.educationChart = new Chart(document.getElementById('educationChart'), {
            type: 'bar',
            data: {
                labels: data.educationChart.labels,
                datasets: [{
                    label: 'Jumlah',
                    data: data.educationChart.data,
                    backgroundColor: data.educationChart.labels.map((_, index) => palette[index % palette.length]),
                    borderRadius: 8
                }],
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
            },
        });

        if (charts.occupationChart) charts.occupationChart.destroy();
        charts.occupationChart = new Chart(document.getElementById('occupationChart'), {
            type: 'bar',
            data: {
                labels: data.occupationChart.labels,
                datasets: [{
                    label: 'Jumlah Penduduk',
                    data: data.occupationChart.data,
                    backgroundColor: palette,
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } },
            },
        });
    }

    function renderDinamikaReport(data) {
        const showPerBulanChart = !!(data.meta && data.meta.showPerBulanChart);
        const hasData = !!(data.meta && data.meta.hasData);
        const noDataMessage = !hasData
            ? `<div class="alert alert-warning py-2 px-3 mb-3">Maaf, data dinamika penduduk tidak ditemukan untuk filter yang dipilih.</div>`
            : '';
        const filterInfo = (!showPerBulanChart)
            ? `<div class="text-muted small mb-2">Grafik per bulan disembunyikan karena filter bulan/dusun sedang aktif.</div>`
            : '';

        const chartSection = showPerBulanChart
            ? `
                <div class="report-panel mb-4">
                    <div class="section-title"><i class="bi bi-bar-chart-fill me-2"></i>Grafik Dinamika per Bulan</div>
                    <div class="chart-container">
                        <canvas id="dinamikaChart"></canvas>
                    </div>
                </div>
            `
            : '';

        const breakdownRows = (data.dusunBreakdown && data.dusunBreakdown.length)
            ? data.dusunBreakdown.map(row => `
                <tr>
                    <td>${row.dusun}</td>
                    <td class="text-right"><strong>${row.lahir.toLocaleString('id-ID')}</strong></td>
                    <td class="text-right">${row.meninggal.toLocaleString('id-ID')}</td>
                    <td class="text-right">${row.masuk.toLocaleString('id-ID')}</td>
                    <td class="text-right">${row.keluar.toLocaleString('id-ID')}</td>
                </tr>
            `).join('')
            : `
                <tr>
                    <td colspan="5" class="text-center text-muted">Tidak ada data dinamika pada filter yang dipilih.</td>
                </tr>
            `;

        const html = `
            <div class="report-panel mb-4">
                <div class="section-title mb-3"><i class="bi bi-activity me-2"></i>Ringkasan Dinamika Penduduk</div>
                ${noDataMessage}
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <div class="summary-stat">
                            <div class="summary-icon"><i class="bi bi-heart-fill"></i></div>
                            <div class="summary-label">Total Kelahiran</div>
                            <div class="summary-value">${data.summary.totalLahir.toLocaleString('id-ID')}</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="summary-stat">
                            <div class="summary-icon"><i class="bi bi-emoji-frown-fill"></i></div>
                            <div class="summary-label">Total Kematian</div>
                            <div class="summary-value">${data.summary.totalMeninggal.toLocaleString('id-ID')}</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="summary-stat">
                            <div class="summary-icon"><i class="bi bi-box-arrow-in-down"></i></div>
                            <div class="summary-label">Total Masuk</div>
                            <div class="summary-value">${data.summary.totalMasuk.toLocaleString('id-ID')}</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="summary-stat">
                            <div class="summary-icon"><i class="bi bi-box-arrow-up-right"></i></div>
                            <div class="summary-label">Total Keluar</div>
                            <div class="summary-value">${data.summary.totalKeluar.toLocaleString('id-ID')}</div>
                        </div>
                    </div>
                </div>
            </div>

            ${chartSection}

            <div class="report-panel mb-4">
                <div class="section-title"><i class="bi bi-table me-2"></i>Breakdown per Dusun</div>
                ${filterInfo}
                <table class="breakdown-table">
                    <thead>
                        <tr>
                            <th>Dusun</th>
                            <th class="text-right">Lahir</th>
                            <th class="text-right">Meninggal</th>
                            <th class="text-right">Masuk</th>
                            <th class="text-right">Keluar</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${breakdownRows}
                    </tbody>
                </table>
            </div>

        `;

        document.getElementById('reportContent').innerHTML = html;

        if (charts.dinamikaChart) {
            charts.dinamikaChart.destroy();
            charts.dinamikaChart = null;
        }

        if (showPerBulanChart && document.getElementById('dinamikaChart')) {
            // Render dinamika chart (line/bar dengan multiple datasets)
            charts.dinamikaChart = new Chart(document.getElementById('dinamikaChart'), {
                type: 'bar',
                data: {
                    labels: data.perBulanChart.labels,
                    datasets: [
                        {
                            label: 'Kelahiran',
                            data: data.perBulanChart.lahir,
                            backgroundColor: '#10b981',
                            borderRadius: 4,
                        },
                        {
                            label: 'Kematian',
                            data: data.perBulanChart.meninggal,
                            backgroundColor: '#ef4444',
                            borderRadius: 4,
                        },
                        {
                            label: 'Masuk',
                            data: data.perBulanChart.masuk,
                            backgroundColor: '#3b82f6',
                            borderRadius: 4,
                        },
                        {
                            label: 'Keluar',
                            data: data.perBulanChart.keluar,
                            backgroundColor: '#f59e0b',
                            borderRadius: 4,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    }

    function exportToExcel() {
        syncCurrentFiltersFromForm();

        const filters = {
            ...currentFilters,
            laporan_tipe: currentReportType,
        };

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("kasi.laporan.export-excel") }}';

        Object.keys(filters).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = filters[key] || '';
            form.appendChild(input);
        });

        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = '{{ csrf_token() }}';
        form.appendChild(token);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    function exportToPdf() {
        syncCurrentFiltersFromForm();

        const filters = {
            ...currentFilters,
            laporan_tipe: currentReportType,
        };

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("kasi.laporan.export-pdf") }}';

        Object.keys(filters).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = filters[key] || '';
            form.appendChild(input);
        });

        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = '{{ csrf_token() }}';
        form.appendChild(token);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    // Load initial report from server so the page is usable without waiting for AJAX
    currentFilters = {
        tahun: '{{ $currentYear }}',
        bulan: null,
        dusun_id: document.getElementById('dusun')?.value || null,
    };

    updateFilterVisibility();

    @if(!empty($initialDemografiData))
        renderDemografiReport(@json($initialDemografiData));
    @endif
</script>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush
