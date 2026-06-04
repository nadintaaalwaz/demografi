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
            <div class="toolbar-row">
                <div class="search-group">
                    <i class="fas fa-search"></i>
                    <input
                        type="text"
                        name="q"
                        id="searchInput"
                        value="{{ request('q') }}"
                        placeholder="Cari berdasarkan nama, NIK, nomor kartu keluarga, dusun..."
                        autocomplete="off"
                    >
                </div>

                <a href="{{ route('kasi.penduduk.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Data
                </a>
            </div>

            <div class="filter-panel active" id="filterPanel">
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
                        <option value="balita" {{ strtolower(request('kategori_usia', '')) === 'balita' ? 'selected' : '' }}>Bayi & Balita (0–5)</option>
                        <option value="anak" {{ strtolower(request('kategori_usia', '')) === 'anak' ? 'selected' : '' }}>Anak-anak (6–11)</option>
                        <option value="remaja" {{ strtolower(request('kategori_usia', '')) === 'remaja' ? 'selected' : '' }}>Remaja (10–19)</option>
                        <option value="dewasa" {{ strtolower(request('kategori_usia', '')) === 'dewasa' ? 'selected' : '' }}>Dewasa (19–59)</option>
                        <option value="lansia" {{ strtolower(request('kategori_usia', '')) === 'lansia' ? 'selected' : '' }}>Lansia (60+)</option>
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
                        <th>Jenis Kelamin</th>
                        <th>Tanggal Lahir</th>
                        <th>Status Keluarga</th>
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
                        <td>{{ optional($p->tanggal_lahir)->format('d-m-Y') ?? '-' }}</td>
                        <td>{{ $p->status_keluarga ?? '-' }}</td>
                        <td>{{ $p->dusun->nama ?? '-' }}</td>
                        <td>{{ $p->pekerjaan ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $p->status == 'Aktif' ? 'success' : 'danger' }}">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                <a href="{{ route('kasi.penduduk.edit', $p->nik) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('kasi.penduduk.destroy', $p->nik) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
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
    /* Overall spacing */
    .page-header { margin-bottom: 18px; }
    .page-header h1 { font-size: 1.8rem; font-weight: 800; color: #0C342C; margin-bottom: 6px; }
    .page-header p { color: #6b7280; margin: 0; }

    /* Card */
    .card { background: #fff; border-radius: 16px; box-shadow: 0 8px 24px rgba(10,20,15,0.06); overflow: hidden; }
    .card-header { padding: 18px 22px; border-bottom: 1px solid #eef2f6; }
    .card-body { padding: 20px 22px 24px; }

    /* Stats */
    .stats-grid { display: grid; grid-template-columns: 1fr; gap: 12px; }
    .stat-item { display: flex; align-items: center; gap: 14px; }
    .stat-icon {
        width: 54px; height: 54px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, #0C342C, #076653); color: #fff; font-size: 1.2rem;
        box-shadow: 0 8px 18px rgba(7, 102, 83, 0.18);
    }
    .stat-number { font-size: 1.6rem; font-weight: 800; color: #0C342C; line-height: 1.1; }
    .stat-label { color: #6b7280; font-size: 0.95rem; }

    /* Toolbar */
    .table-tools {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 16px;
    }

    .toolbar-row {
        display: flex;
        align-items: center;
        gap: 14px;
        width: 100%;
    }

    .toolbar-row .search-group {
        flex: 1;
    }

    .toolbar-row .btn-primary {
        width: 220px;
        flex: 0 0 220px;
    }

    .search-group { position: relative; }
    .search-group i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
    .search-group input {
        width: 100%; height: 48px; border: 1px solid #dbe4ea; border-radius: 12px;
        padding: 0 16px 0 42px; font-size: 14px; color: #0f172a; background: #fff;
        transition: border-color .2s ease, box-shadow .2s ease;
    }
    .search-group input::placeholder { color: #94a3b8; }
    .search-group input:focus { outline: none; border-color: #0C342C; box-shadow: 0 0 0 4px rgba(12,52,44,0.08); }

    .btn-filter-toggle,
    .btn-primary,
    .btn-apply-filter,
    .btn-reset-filter,
    .btn-sm {
        transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease, border-color .18s ease;
    }

    .btn-filter-toggle {
        background: #fff; border: 1px solid #dbe4ea; color: #0C342C; padding: 10px 14px;
        border-radius: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
        box-shadow: 0 2px 8px rgba(15,23,42,0.04);
    }
    .btn-filter-toggle:hover { transform: translateY(-1px); box-shadow: 0 8px 18px rgba(15,23,42,0.08); }

    .btn-primary {
        background: linear-gradient(135deg, #0C342C, #076653); color: #fff; padding: 12px 16px;
        border-radius: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
        box-shadow: 0 8px 18px rgba(7,102,83,0.18);
        white-space: nowrap;
        width: 100%;
        justify-content: center;
        flex: 1;
    }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 22px rgba(7,102,83,0.24); color: #fff; }

    /* Filter panel */
    .filter-panel {
        display: none; grid-column: 1 / -1; background: #f8fafc; border: 1px solid #e8eef2;
        border-radius: 14px; padding: 16px; margin-top: 12px;
    }
    .filter-panel.active { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; align-items: end; }

    .filter-panel .filter-actions {
        justify-content: flex-end;
        justify-self: end;
        margin-left: 14px;
        padding-left: 0;
        width: auto;
    }

    .filter-panel .btn-reset-filter {
        width: 180px;
        flex: 0 0 180px;
    }

    .filter-field { display: flex; flex-direction: column; gap: 6px; }
    .filter-field label { font-size: 12px; font-weight: 700; color: #334155; }
    .filter-field select {
        height: 42px; border: 1px solid #dbe4ea; border-radius: 10px; padding: 0 12px;
        background: #fff; color: #0f172a;
    }
    .filter-field select:focus { outline: none; border-color: #0C342C; box-shadow: 0 0 0 4px rgba(12,52,44,0.08); }
    .filter-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        width: 100%;
    }
    .btn-reset-filter {
        background: #fff;
        color: #334155;
        border: 1px solid #dbe4ea;
        padding: 10px 14px;
        border-radius: 10px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 200px;
        flex: 0 0 220px;
    }
    .btn-reset-filter:hover { transform: translateY(-1px); box-shadow: 0 8px 18px rgba(15,23,42,0.06); }

    /* Table */
    .table-responsive { overflow-x: auto; margin-top: 20px; border-radius: 14px; }
    .data-table { width: 100%; border-collapse: collapse; min-width: 980px; }
    .data-table thead th {
        background: #fbfdfe; padding: 16px 14px; text-align: left; font-weight: 800; color: #0C342C;
        border-bottom: 1px solid #e7edf1; white-space: nowrap;
    }
    .data-table td {
        padding: 16px 14px; border-bottom: 1px solid #eef2f6; color: #0f172a; vertical-align: middle;
    }
    .data-table tbody tr:hover { background: #fcfefe; }

    /* Badges and buttons */
    .badge { padding: 7px 12px; border-radius: 999px; font-weight: 700; font-size: 0.82rem; display: inline-block; }
    .badge-success { background: #dcfce7; color: #166534; }
    .badge-danger { background: #fee2e2; color: #991b1b; }

    .btn-sm {
        width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center;
        justify-content: center; border: none; color: #fff;
    }
    .btn-warning { background: #f59e0b; }
    .btn-danger { background: #ef4444; }
    .btn-sm:hover { transform: translateY(-1px); }

    /* Empty state */
    .empty-state { text-align: center; padding: 40px; color: #94a3b8; }

    /* Pagination */
    .pagination-wrapper {
        margin-top: 22px; display: flex; align-items: center; justify-content: space-between;
        gap: 12px; flex-wrap: wrap;
    }
    .pagination-info { color: #64748b; font-size: 13px; }
    .pagination-links { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .page-btn, .page-number {
        display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px;
        padding: 0 14px; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 700;
        border: 1px solid #dbe4ea; color: #334155; background: #fff;
    }
    .page-btn:hover, .page-number:hover { border-color: #0C342C; color: #0C342C; box-shadow: 0 6px 14px rgba(15,23,42,0.06); }
    .page-number.active { background: #0C342C; color: #fff; border-color: #0C342C; }
    .page-btn.disabled { opacity: 0.5; pointer-events: none; }

    @media (max-width: 992px) {
        .toolbar-row { flex-direction: column; align-items: stretch; }
        .filter-panel.active { grid-template-columns: 1fr; }
        .filter-actions { flex-wrap: wrap; }
        .data-table { min-width: 860px; }
    }

    @media (max-width: 768px) {
        .card-header, .card-body { padding-left: 14px; padding-right: 14px; }
        .btn-primary { width: 100%; justify-content: center; }
        .toolbar-row { gap: 10px; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('searchFilterForm');
        if (!form) return;

        const baseUrl = form.dataset.baseUrl;
        const searchInput = document.getElementById('searchInput');
        const selects = Array.from(form.querySelectorAll('select'));
        let searchDebounce = null;

        const hasAnyFilterValue = () => selects.some(select => select.value !== '');

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
