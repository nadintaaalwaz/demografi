@extends('kasi.layout')

@section('title', 'Laporan Demografi & Dinamika Penduduk')
@section('page-title', 'Laporan Demografi & Dinamika Penduduk')

@push('styles')
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
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .btn-primary {
        background: #076653;
        color: white;
    }

    .btn-primary:hover {
        background: #0C342C;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(7, 102, 83, 0.3);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
    }

    .btn-small {
        padding: 8px 14px;
        font-size: 12px;
    }

    .report-tabs {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        border-bottom: 2px solid #f1f5f9;
    }

    .tab-button {
        padding: 12px 20px;
        background: none;
        border: none;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        font-size: 14px;
        font-weight: 700;
        color: #64748b;
        transition: all 0.2s ease;
        position: relative;
        bottom: -2px;
    }

    .tab-button.active {
        color: #076653;
        border-bottom-color: #076653;
    }

    .tab-button:hover {
        color: #0C342C;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
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

    @media (max-width: 768px) {
        .filter-row {
            grid-template-columns: 1fr;
        }

        .report-tabs {
            flex-wrap: wrap;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .chart-container {
            height: 250px;
        }
    }
</style>
@endpush

@section('content')
<div class="report-container">
    <!-- Filter Section -->
    <div class="filter-section">
        <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px; font-weight: 800; color: #0C342C;">
            Filter Laporan
        </h3>

        <form id="filterForm" class="filter-row">
            @csrf

            <div class="form-group">
                <label>Tahun <span style="color: #ef4444;">*</span></label>
                <select id="tahun" name="tahun" required>
                    @foreach($yearList as $year)
                        <option value="{{ $year }}" @selected($year == $currentYear)>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
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

            <div class="form-group">
                <label>Dusun <span style="color: #94a3b8; font-weight: 400;">(Opsional)</span></label>
                <select id="dusun" name="dusun_id">
                    <option value="">Semua Dusun</option>
                    @foreach($dusunList as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    Tampilkan Laporan
                </button>
                <button type="reset" class="btn btn-secondary">
                    Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Report Tabs -->
    <div class="report-tabs">
        <button class="tab-button active" data-tab="demografi">
            📊 Demografi Penduduk
        </button>
        <button class="tab-button" data-tab="dinamika">
            📈 Dinamika Penduduk
        </button>
    </div>

    <!-- Demografi Tab -->
    <div id="demografi" class="tab-content active">
        <div id="demografiContent">
            <div class="loading">
                <div class="loading-spinner"></div>
                <p>Memuat data demografi...</p>
            </div>
        </div>
    </div>

    <!-- Dinamika Tab -->
    <div id="dinamika" class="tab-content">
        <div id="dinamikaContent">
            <div class="loading">
                <div class="loading-spinner"></div>
                <p>Memuat data dinamika...</p>
            </div>
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

    // Tab switching
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function () {
            const tabName = this.dataset.tab;

            // Update active tab
            document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');

            currentReportType = tabName;

            // Load data if filters exist
            if (Object.keys(currentFilters).length > 0) {
                loadReportData();
            }
        });
    });

    // Form submission
    document.getElementById('filterForm').addEventListener('submit', function (e) {
        e.preventDefault();

        currentFilters = {
            tahun: document.getElementById('tahun').value,
            bulan: document.getElementById('bulan').value || null,
            dusun_id: document.getElementById('dusun').value || null,
        };

        // Reset to demografi tab
        document.querySelectorAll('.tab-button')[0].click();

        loadReportData();
    });

    function loadReportData() {
        const filters = {
            ...currentFilters,
            laporan_tipe: currentReportType,
        };

        const contentDiv = currentReportType === 'demografi' ? 'demografiContent' : 'dinamikaContent';
        document.getElementById(contentDiv).innerHTML = `
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
                    renderDemografiReport(data);
                } else {
                    renderDinamikaReport(data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById(contentDiv).innerHTML = `
                    <div class="error-message">
                        Gagal memuat data laporan. Silakan coba lagi.
                    </div>
                `;
            });
    }

    function renderDemografiReport(data) {
        const html = `
            <div class="content-section">
                <div class="section-title">Ringkasan Demografi</div>
                <div class="summary-grid">
                    <div class="summary-card">
                        <div class="summary-label">Total Penduduk Aktif</div>
                        <div class="summary-value">${data.summary.totalPenduduk.toLocaleString('id-ID')}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Laki-laki</div>
                        <div class="summary-value">${data.summary.totalLakiLaki.toLocaleString('id-ID')}</div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">${data.summary.persenLakiLaki}%</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Perempuan</div>
                        <div class="summary-value">${data.summary.totalPerempuan.toLocaleString('id-ID')}</div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">${data.summary.persenPerempuan}%</div>
                    </div>
                </div>
            </div>

            <div class="content-section">
                <div class="section-title">Distribusi Jenis Kelamin</div>
                <div class="chart-container">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>

            <div class="content-section">
                <div class="section-title">Distribusi Pendidikan</div>
                <div class="chart-container">
                    <canvas id="educationChart"></canvas>
                </div>
            </div>

            <div class="content-section">
                <div class="section-title">Distribusi Pekerjaan</div>
                <div class="chart-container">
                    <canvas id="occupationChart"></canvas>
                </div>
            </div>

            <div class="content-section">
                <div class="section-title">Breakdown per Dusun</div>
                <table class="breakdown-table">
                    <thead>
                        <tr>
                            <th>Dusun</th>
                            <th class="text-right">Total</th>
                            <th class="text-right">Laki-laki</th>
                            <th class="text-right">Perempuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.dusunBreakdown.map(row => `
                            <tr>
                                <td>${row.dusun}</td>
                                <td class="text-right"><strong>${row.total.toLocaleString('id-ID')}</strong></td>
                                <td class="text-right">${row.laki_laki.toLocaleString('id-ID')}</td>
                                <td class="text-right">${row.perempuan.toLocaleString('id-ID')}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>

            <div class="export-buttons">
                <button onclick="exportToExcel('demografi')" class="btn btn-secondary">
                    📥 Download Excel
                </button>
                <button onclick="exportToPdf('demografi')" class="btn btn-secondary">
                    📥 Download PDF
                </button>
            </div>
        `;

        document.getElementById('demografiContent').innerHTML = html;

        // Render charts
        const palette = ['#076653', '#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#14b8a6', '#ec4899', '#06b6d4', '#84cc16'];

        // Gender chart
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
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Education chart
        if (charts.educationChart) charts.educationChart.destroy();
        charts.educationChart = new Chart(document.getElementById('educationChart'), {
            type: 'bar',
            data: {
                labels: data.educationChart.labels,
                datasets: [{
                    label: 'Jumlah Penduduk',
                    data: data.educationChart.data,
                    backgroundColor: palette,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Occupation chart
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
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    function renderDinamikaReport(data) {
        const html = `
            <div class="content-section">
                <div class="section-title">Ringkasan Dinamika Penduduk</div>
                <div class="summary-grid">
                    <div class="summary-card">
                        <div class="summary-label">Total Kelahiran</div>
                        <div class="summary-value">${data.summary.totalLahir.toLocaleString('id-ID')}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Total Kematian</div>
                        <div class="summary-value">${data.summary.totalMeninggal.toLocaleString('id-ID')}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Total Masuk</div>
                        <div class="summary-value">${data.summary.totalMasuk.toLocaleString('id-ID')}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Total Keluar</div>
                        <div class="summary-value">${data.summary.totalKeluar.toLocaleString('id-ID')}</div>
                    </div>
                </div>
            </div>

            <div class="content-section">
                <div class="section-title">Grafik Dinamika per Bulan</div>
                <div class="chart-container">
                    <canvas id="dinamikaChart"></canvas>
                </div>
            </div>

            <div class="content-section">
                <div class="section-title">Breakdown per Dusun</div>
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
                        ${data.dusunBreakdown.map(row => `
                            <tr>
                                <td>${row.dusun}</td>
                                <td class="text-right"><strong>${row.lahir.toLocaleString('id-ID')}</strong></td>
                                <td class="text-right">${row.meninggal.toLocaleString('id-ID')}</td>
                                <td class="text-right">${row.masuk.toLocaleString('id-ID')}</td>
                                <td class="text-right">${row.keluar.toLocaleString('id-ID')}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>

            <div class="export-buttons">
                <button onclick="exportToExcel('dinamika')" class="btn btn-secondary">
                    📥 Download Excel
                </button>
                <button onclick="exportToPdf('dinamika')" class="btn btn-secondary">
                    📥 Download PDF
                </button>
            </div>
        `;

        document.getElementById('dinamikaContent').innerHTML = html;

        // Render dinamika chart (line/bar dengan multiple datasets)
        if (charts.dinamikaChart) charts.dinamikaChart.destroy();
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

    function exportToExcel(type) {
        const filters = {
            ...currentFilters,
            laporan_tipe: type,
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

    function exportToPdf(type) {
        const filters = {
            ...currentFilters,
            laporan_tipe: type,
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
</script>
@endpush
