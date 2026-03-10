@extends('kasi.layout')

@section('title', 'Data Penduduk')
@section('page-title', 'Data Penduduk Desa Sebalor')

@push('styles')
<style>
    .toolbar {
        background: #fff;
        padding: 25px;
        border-radius: 16px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 300px;
    }

    .search-box input {
        width: 100%;
        padding: 12px 40px 12px 20px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .search-box input:focus {
        outline: none;
        border-color: #076653;
    }

    .search-box i {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
    }

    .filter-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background: #076653;
        color: #fff;
    }

    .btn-primary:hover {
        background: #0C342C;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(7, 102, 83, 0.3);
    }

    .btn-success {
        background: #10b981;
        color: #fff;
    }

    .btn-success:hover {
        background: #059669;
    }

    .btn-warning {
        background: #f59e0b;
        color: #fff;
    }

    .btn-warning:hover {
        background: #d97706;
    }

    .btn-danger {
        background: #ef4444;
        color: #fff;
    }

    .btn-danger:hover {
        background: #dc2626;
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .table-container {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .table-responsive {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }

    th {
        padding: 15px 20px;
        text-align: left;
        font-size: 13px;
        font-weight: 700;
        color: #0C342C;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.3s ease;
    }

    tbody tr:hover {
        background: #f9fafb;
    }

    td {
        padding: 18px 20px;
        font-size: 14px;
        color: #374151;
    }

    .badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-male {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-female {
        background: #fce7f3;
        color: #9f1239;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-sm {
        padding: 8px 12px;
        font-size: 12px;
    }

    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 25px;
        border-top: 2px solid #f3f4f6;
    }

    .pagination-info {
        font-size: 14px;
        color: #6b7280;
    }

    .pagination-links {
        display: flex;
        gap: 8px;
    }

    .page-link {
        padding: 8px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        color: #374151;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .page-link:hover {
        background: #076653;
        color: #fff;
        border-color: #076653;
    }

    .page-link.active {
        background: #076653;
        color: #fff;
        border-color: #076653;
    }

    @media (max-width: 768px) {
        .toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .search-box {
            min-width: 100%;
        }

        .filter-group {
            justify-content: stretch;
        }

        .btn {
            flex: 1;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<!-- Toolbar -->
<div class="toolbar">
    <div class="search-box">
        <input type="text" placeholder="Cari berdasarkan NIK atau Nama..." id="searchInput">
        <i class="fas fa-search"></i>
    </div>
    
    <div class="filter-group">
        <button class="btn btn-secondary">
            <i class="fas fa-filter"></i> Filter
        </button>
        <a href="{{ route('kasi.penduduk.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Data
        </a>
        <a href="{{ route('kasi.upload.form') }}" class="btn btn-success">
            <i class="fas fa-file-upload"></i> Upload Excel
        </a>
    </div>
</div>

<!-- Table -->
<div class="table-container">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIK</th>
                    <th>Nama Lengkap</th>
                    <th>JK</th>
                    <th>Tgl Lahir</th>
                    <th>Usia</th>
                    <th>Dusun</th>
                    <th>Pendidikan</th>
                    <th>Pekerjaan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penduduk ?? [] as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $p->nik }}</td>
                    <td><strong>{{ $p->nama }}</strong></td>
                    <td>
                        <span class="badge {{ $p->jenis_kelamin == 'L' ? 'badge-male' : 'badge-female' }}">
                            {{ $p->jenis_kelamin == 'L' ? 'L' : 'P' }}
                        </span>
                    </td>
                    <td>{{ $p->tanggal_lahir }}</td>
                    <td>{{ $p->usia }} th</td>
                    <td>{{ $p->dusun->nama ?? '-' }}</td>
                    <td>{{ $p->pendidikan ?? '-' }}</td>
                    <td>{{ $p->pekerjaan ?? '-' }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('kasi.penduduk.edit', $p->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('kasi.penduduk.destroy', $p->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <!-- Sample Data -->
                <tr>
                    <td>1</td>
                    <td>3301012301850001</td>
                    <td><strong>Ahmad Fauzi</strong></td>
                    <td><span class="badge badge-male">L</span></td>
                    <td>23-01-1985</td>
                    <td>39 th</td>
                    <td>Krajan</td>
                    <td>S1</td>
                    <td>PNS</td>
                    <td>
                        <div class="action-buttons">
                            <a href="#" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>3301015405920002</td>
                    <td><strong>Siti Nurjanah</strong></td>
                    <td><span class="badge badge-female">P</span></td>
                    <td>14-05-1992</td>
                    <td>32 th</td>
                    <td>Jati</td>
                    <td>SMA</td>
                    <td>Wiraswasta</td>
                    <td>
                        <div class="action-buttons">
                            <a href="#" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>3301011207880003</td>
                    <td><strong>Budi Santoso</strong></td>
                    <td><span class="badge badge-male">L</span></td>
                    <td>12-07-1988</td>
                    <td>36 th</td>
                    <td>Mawar</td>
                    <td>D3</td>
                    <td>Guru</td>
                    <td>
                        <div class="action-buttons">
                            <a href="#" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>3301012008950004</td>
                    <td><strong>Dewi Kusuma</strong></td>
                    <td><span class="badge badge-female">P</span></td>
                    <td>20-08-1995</td>
                    <td>29 th</td>
                    <td>Melati</td>
                    <td>SMP</td>
                    <td>Petani</td>
                    <td>
                        <div class="action-buttons">
                            <a href="#" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>3301013009000005</td>
                    <td><strong>Eko Prasetyo</strong></td>
                    <td><span class="badge badge-male">L</span></td>
                    <td>30-09-2000</td>
                    <td>24 th</td>
                    <td>Anggrek</td>
                    <td>S1</td>
                    <td>Programmer</td>
                    <td>
                        <div class="action-buttons">
                            <a href="#" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <div class="pagination-info">
            Menampilkan 1 - 5 dari 500 data
        </div>
        <div class="pagination-links">
            <a href="#" class="page-link">Prev</a>
            <a href="#" class="page-link active">1</a>
            <a href="#" class="page-link">2</a>
            <a href="#" class="page-link">3</a>
            <a href="#" class="page-link">...</a>
            <a href="#" class="page-link">100</a>
            <a href="#" class="page-link">Next</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('tbody tr');
    
    tableRows.forEach(row => {
        const nik = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const nama = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        
        if (nik.includes(searchValue) || nama.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endpush
