@extends('kasi.layout')

@section('title', 'Data Penduduk')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-users"></i> Data Penduduk</h1>
    <p>Daftar seluruh penduduk yang telah diupload</p>
</div>

<div class="card">
    <div class="card-header">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($penduduk->total()) }}</div>
                    <div class="stat-label">Total Penduduk</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('kasi.penduduk.index') }}" class="table-tools" id="searchFilterForm" data-base-url="{{ route('kasi.penduduk.index') }}">
            <div class="search-group">
                <i class="fas fa-search"></i>
                <input
                    type="text"
                    name="q"
                    id="searchInput"
                    value="{{ request('q') }}"
                    placeholder="Cari berdasarkan nama, NIK, nomor KK, dusun..."
                    autocomplete="off"
                >
            </div>

            <button type="button" class="btn-filter-toggle" id="toggleFilterBtn" aria-expanded="{{ request()->filled('jenis_kelamin') || request()->filled('kategori_usia') || request()->filled('status') || request()->filled('id_dusun') || request()->filled('per_page') ? 'true' : 'false' }}">
                <i class="fas fa-filter"></i>
                Filter
            </button>

            <div class="filter-panel {{ request()->filled('jenis_kelamin') || request()->filled('kategori_usia') || request()->filled('status') || request()->filled('id_dusun') || request()->filled('per_page') ? 'active' : '' }}" id="filterPanel">
                <div class="filter-field">
                    <label for="jenisKelamin">Jenis Kelamin</label>
                    <select name="jenis_kelamin" id="jenisKelamin">
                        <option value="">Semua</option>
                        <option value="L" {{ request('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ request('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="kategoriUsia">Kategori Usia</label>
                    <select name="kategori_usia" id="kategoriUsia">
                        <option value="">Semua</option>
                        <option value="Balita" {{ request('kategori_usia') === 'Balita' ? 'selected' : '' }}>Balita</option>
                        <option value="Produktif" {{ request('kategori_usia') === 'Produktif' ? 'selected' : '' }}>Produktif</option>
                        <option value="Lansia" {{ request('kategori_usia') === 'Lansia' ? 'selected' : '' }}>Lansia</option>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="statusPenduduk">Status</label>
                    <select name="status" id="statusPenduduk">
                        <option value="">Semua</option>
                        <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Meninggal" {{ request('status') === 'Meninggal' ? 'selected' : '' }}>Meninggal</option>
                        <option value="Keluar" {{ request('status') === 'Keluar' ? 'selected' : '' }}>Keluar</option>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="dusunFilter">Dusun</label>
                    <select name="id_dusun" id="dusunFilter">
                        <option value="">Semua</option>
                        @foreach($dusunList as $dusun)
                            <option value="{{ $dusun->id }}" {{ (string) request('id_dusun') === (string) $dusun->id ? 'selected' : '' }}>
                                {{ $dusun->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field">
                    <label for="perPage">Baris per Halaman</label>
                    <select name="per_page" id="perPage">
                        <option value="25" {{ (string) request('per_page', '50') === '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ (string) request('per_page', '50') === '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ (string) request('per_page', '50') === '100' ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-apply-filter">Terapkan</button>
                    <a href="{{ route('kasi.penduduk.index') }}" class="btn-reset-filter">Reset</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama Lengkap</th>
                        <th>JK</th>
                        <th>Umur</th>
                        <th>Kategori</th>
                        <th>Dusun</th>
                        <th>Pekerjaan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penduduk as $p)
                    <tr>
                        <td>{{ $p->nik }}</td>
                        <td>{{ $p->nama_lengkap }}</td>
                        <td>{{ $p->jenis_kelamin }}</td>
                        <td>{{ $p->umur }} tahun</td>
                        <td>
                            <span class="badge badge-{{ $p->kategori_usia == 'Balita' ? 'info' : ($p->kategori_usia == 'Lansia' ? 'warning' : 'success') }}">
                                {{ $p->kategori_usia }}
                            </span>
                        </td>
                        <td>{{ $p->dusun->nama ?? '-' }}</td>
                        <td>{{ $p->pekerjaan ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $p->status == 'Aktif' ? 'success' : 'danger' }}">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('kasi.penduduk.show', $p->nik) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px;">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 16px;"></i>
                            <p>Belum ada data penduduk. Silakan upload data terlebih dahulu.</p>
                            <a href="{{ route('kasi.upload.form') }}" class="btn btn-primary" style="margin-top: 16px;">
                                <i class="fas fa-upload"></i> Upload Data
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($penduduk->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Menampilkan {{ $penduduk->firstItem() }} sampai {{ $penduduk->lastItem() }} dari {{ number_format($penduduk->total()) }} data
            </div>

            <div class="pagination-links">
                @if($penduduk->onFirstPage())
                    <span class="page-btn disabled">Sebelumnya</span>
                @else
                    <a href="{{ $penduduk->previousPageUrl() }}" class="page-btn">Sebelumnya</a>
                @endif

                @php
                    $currentPage = $penduduk->currentPage();
                    $lastPage = $penduduk->lastPage();
                    $startPage = max(1, $currentPage - 1);
                    $endPage = min($lastPage, $startPage + 2);

                    if (($endPage - $startPage) < 2) {
                        $startPage = max(1, $endPage - 2);
                    }
                @endphp

                @for($page = $startPage; $page <= $endPage; $page++)
                    @if($page == $currentPage)
                        <span class="page-number active">{{ $page }}</span>
                    @else
                        <a href="{{ $penduduk->url($page) }}" class="page-number">{{ $page }}</a>
                    @endif
                @endfor

                @if($penduduk->hasMorePages())
                    <a href="{{ $penduduk->nextPageUrl() }}" class="page-btn">Selanjutnya</a>
                @else
                    <span class="page-btn disabled">Selanjutnya</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .page-header {
        margin-bottom: 30px;
    }

    .page-header h1 {
        font-size: 1.8rem;
        color: #0C342C;
        margin-bottom: 8px;
    }

    .page-header p {
        color: #666;
    }

    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .card-header {
        padding: 24px;
        border-bottom: 1px solid #eee;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .stat-item {
        display: flex;
        gap: 16px;
        align-items: center;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #0C342C, #1a5245);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: bold;
        color: #0C342C;
    }

    .stat-label {
        color: #666;
        font-size: 0.9rem;
    }

    .card-body {
        padding: 24px;
    }

    .table-tools {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 12px;
        margin-bottom: 18px;
        align-items: center;
    }

    .search-group {
        position: relative;
    }

    .search-group i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 14px;
    }

    .search-group input {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        height: 42px;
        padding: 0 14px 0 40px;
        font-size: 14px;
        color: #1f2937;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .search-group input:focus {
        outline: none;
        border-color: #076653;
        box-shadow: 0 0 0 3px rgba(7, 102, 83, 0.15);
    }

    .btn-filter-toggle {
        border: none;
        border-radius: 10px;
        height: 42px;
        padding: 0 16px;
        background: linear-gradient(135deg, #0C342C, #076653);
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-filter-toggle:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(7, 102, 83, 0.25);
    }

    .filter-panel {
        display: none;
        grid-column: 1 / -1;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 14px;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 12px;
    }

    .filter-panel.active {
        display: grid;
    }

    .filter-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-field label {
        font-size: 12px;
        font-weight: 600;
        color: #374151;
    }

    .filter-field select {
        height: 38px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0 10px;
        background: #fff;
        color: #1f2937;
    }

    .filter-field select:focus {
        outline: none;
        border-color: #076653;
        box-shadow: 0 0 0 3px rgba(7, 102, 83, 0.15);
    }

    .filter-actions {
        display: flex;
        align-items: end;
        gap: 8px;
    }

    .btn-apply-filter,
    .btn-reset-filter {
        height: 38px;
        padding: 0 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-apply-filter {
        border: none;
        background: #0C342C;
        color: #fff;
        cursor: pointer;
    }

    .btn-reset-filter {
        border: 1px solid #d1d5db;
        color: #374151;
        background: #fff;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th {
        background: #f8f9fa;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #dee2e6;
    }

    .data-table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
    }

    .data-table tbody tr:hover {
        background: #f8f9fa;
    }

    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }

    .badge-success {
        background: #d4edda;
        color: #155724;
    }

    .badge-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .badge-info {
        background: #d1ecf1;
        color: #0c5460;
    }

    .badge-warning {
        background: #fff3cd;
        color: #856404;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.85rem;
    }

    .btn-info {
        background: #17a2b8;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .btn-info:hover {
        background: #138496;
    }

    .pagination-wrapper {
        margin-top: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 18px;
        background: linear-gradient(135deg, #0C342C, #076653);
        border-radius: 10px;
        flex-wrap: wrap;
    }

    .pagination-info {
        color: rgba(255, 255, 255, 0.9);
        font-size: 13px;
        font-weight: 500;
    }

    .pagination-links {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .page-btn,
    .page-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 12px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ecfeff;
        background: rgba(255, 255, 255, 0.08);
        transition: all 0.2s ease;
    }

    .page-btn:hover,
    .page-number:hover {
        background: rgba(227, 239, 38, 0.15);
        border-color: #E3EF26;
        color: #E3EF26;
    }

    .page-number.active {
        background: #E3EF26;
        color: #0C342C;
        border-color: #E3EF26;
    }

    .page-btn.disabled {
        opacity: 0.45;
        cursor: not-allowed;
        pointer-events: none;
    }

    @media (max-width: 768px) {
        .table-tools {
            grid-template-columns: 1fr;
        }

        .btn-filter-toggle {
            width: 100%;
            justify-content: center;
        }

        .pagination-wrapper {
            flex-direction: column;
            align-items: stretch;
        }

        .pagination-links {
            justify-content: center;
            flex-wrap: wrap;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('searchFilterForm');
        if (!form) return;

        const baseUrl = form.dataset.baseUrl;
        const searchInput = document.getElementById('searchInput');
        const filterPanel = document.getElementById('filterPanel');
        const toggleFilterBtn = document.getElementById('toggleFilterBtn');
        const selects = Array.from(form.querySelectorAll('select'));
        let searchDebounce = null;

        const hasAnyFilterValue = () => selects.some(select => select.value !== '');

        toggleFilterBtn.addEventListener('click', function () {
            const isActive = filterPanel.classList.toggle('active');
            toggleFilterBtn.setAttribute('aria-expanded', isActive ? 'true' : 'false');
        });

        searchInput.addEventListener('input', function () {
            clearTimeout(searchDebounce);

            searchDebounce = setTimeout(function () {
                const keyword = searchInput.value.trim();

                if (keyword === '' && !hasAnyFilterValue()) {
                    window.location.href = baseUrl;
                    return;
                }

                form.submit();
            }, 350);
        });

        selects.forEach(function (select) {
            select.addEventListener('change', function () {
                form.submit();
            });
        });
    });
</script>
@endsection
