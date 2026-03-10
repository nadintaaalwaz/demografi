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
                        <td>{{ $p->dusun->nama_dusun ?? '-' }}</td>
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
            {{ $penduduk->links() }}
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
        justify-content: center;
    }
</style>
@endsection
