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
                    placeholder="Cari berdasarkan nama, NIK, nomor kartu keluarga, dusun..."
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
    /* Page header */
    .page-header { margin-bottom: 20px; }
    .page-header h1 { font-size: 1.6rem; color: #0C342C; margin-bottom: 6px; }
    .page-header p { color: #6b7280; margin: 0; }

    /* Card */
    .card { background: #fff; border-radius: 12px; box-shadow: 0 6px 18px rgba(10,20,15,0.04); overflow: hidden; }
    .card-header { padding: 18px 22px; border-bottom: 1px solid #f1f5f9; }
    .card-body { padding: 20px; }

    /* Toolbar + Search + Filter layout */
    .table-tools { display: grid; grid-template-columns: 1fr auto; gap: 12px; align-items: start; margin-bottom: 18px; }

    .search-group { position: relative; }
    .search-group i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 14px; }
    .search-group input { width: 100%; border: 1px solid #e6edf0; border-radius: 10px; height: 42px; padding: 0 14px 0 38px; font-size: 14px; color: #0f172a; }
    .search-group input:focus { outline: none; border-color: #076653; box-shadow: 0 0 0 4px rgba(7,102,83,0.06); }

    /* Right-side controls container */
    .table-tools > div[style] { display:flex; gap:12px; align-items:center; }

    .btn-filter-toggle { background: #fff; border: 1px solid #e6edf0; color: #0C342C; padding: 8px 12px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap:8px; }
    .btn-filter-toggle:hover { box-shadow: 0 6px 14px rgba(7,102,83,0.06); }

    .btn-primary { background: #076653; color: #fff; padding: 10px 14px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items:center; gap:8px; }

    /* Filter panel */
    .filter-panel { display: none; grid-column: 1 / -1; background: #f8fafc; border: 1px solid #edf2f7; border-radius: 8px; padding: 14px; margin-top: 12px; box-shadow: inset 0 1px 0 rgba(255,255,255,0.5); }
    .filter-panel.active { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; align-items: end; }

    .filter-field { display:flex; flex-direction:column; gap:6px; }
    .filter-field label { font-size: 12px; font-weight: 600; color: #374151; }
    .filter-field select { height:38px; border:1px solid #e6edf0; border-radius:8px; padding:0 10px; background:#fff; }
    .filter-actions { display:flex; gap:8px; align-items:center; }
    .btn-apply-filter { background:#0C342C; color:#fff; border:none; padding:8px 12px; border-radius:8px; cursor:pointer; }
    .btn-reset-filter { background:#fff; color:#374151; border:1px solid #e6edf0; padding:8px 12px; border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; }

    /* Table */
    .table-responsive { overflow-x:auto; }
    .data-table { width:100%; border-collapse:collapse; min-width: 900px; }
    .data-table th { background:#fbfdfe; padding:14px; text-align:left; font-weight:700; color:#0C342C; border-bottom:1px solid #eef2f6; }
    .data-table td { padding:14px; border-bottom:1px solid #f1f5f9; color:#0f172a; }
    .data-table tbody tr:hover { background:#fbfcfd; }

    /* Badges and buttons */
    .badge { padding:6px 10px; border-radius:12px; font-weight:600; font-size:0.85rem; }
    .badge-success { background:#dff6ea; color:#05603a; }
    .badge-danger { background:#ffe8e8; color:#8b1e1e; }

    .btn-sm { padding:8px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; }
    .btn-info { background:#17a2b8; color:#fff; border:none; }
    .btn-warning { background:#f59e0b; color:#fff; border:none; }
    .btn-danger { background:#ef4444; color:#fff; border:none; }

    /* Empty state */
    .empty-state { text-align:center; padding:40px; color:#94a3b8; }

    /* Pagination */
    .pagination-wrapper { margin-top:20px; display:flex; align-items:center; justify-content:space-between; gap:12px; }

    @media (max-width: 900px) {
        .table-tools { grid-template-columns: 1fr; }
        .filter-panel.active { grid-template-columns: 1fr; }
        .data-table { min-width: 700px; }
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
