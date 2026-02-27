@extends('kasi.layout')

@section('title', 'Manajemen Wilayah')

@section('page-title')
Manajemen Wilayah
@endsection

@push('styles')
<style>
    .btn {
        padding: 12px 24px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #076653, #0C342C);
        color: #fff;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(7, 102, 83, 0.3);
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
        padding: 8px 16px;
        font-size: 13px;
    }

    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        padding: 8px 16px;
        font-size: 13px;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .action-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .wilayah-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 25px;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 0;
    }

    .tab-btn {
        padding: 12px 24px;
        background: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        color: #6b7280;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .tab-btn:hover {
        color: #076653;
    }

    .tab-btn.active {
        color: #076653;
        border-bottom-color: #076653;
    }

    .wilayah-table-container {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .table-header {
        padding: 20px 25px;
        background: linear-gradient(135deg, #076653, #0C342C);
        color: #fff;
    }

    .table-header h3 {
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .wilayah-table {
        width: 100%;
        border-collapse: collapse;
    }

    .wilayah-table thead {
        background: #f9fafb;
    }

    .wilayah-table th {
        padding: 16px 25px;
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e5e7eb;
    }

    .wilayah-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.2s ease;
    }

    .wilayah-table tbody tr:hover {
        background: #f9fafb;
    }

    .wilayah-table td {
        padding: 18px 25px;
        font-size: 14px;
        color: #374151;
    }

    .wilayah-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .wilayah-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #076653, #0C342C);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #E3EF26;
        font-size: 18px;
    }

    .wilayah-details h4 {
        font-size: 15px;
        font-weight: 600;
        color: #0C342C;
        margin-bottom: 3px;
    }

    .wilayah-details p {
        font-size: 13px;
        color: #6b7280;
    }

    .badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-dusun {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-rt {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-rw {
        background: #fef3c7;
        color: #92400e;
    }

    .stat-value {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #0C342C;
    }

    .stat-value i {
        color: #076653;
        font-size: 16px;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .empty-state {
        padding: 60px 25px;
        text-align: center;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 18px;
        color: #6b7280;
        margin-bottom: 10px;
    }

    .empty-state p {
        font-size: 14px;
    }

    /* Delete Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }

    .modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        max-width: 450px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .modal-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #fee2e2;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #dc2626;
        font-size: 24px;
    }

    .modal-header h3 {
        font-size: 20px;
        color: #0C342C;
        font-weight: 700;
    }

    .modal-body {
        margin-bottom: 25px;
        color: #6b7280;
        font-size: 14px;
        line-height: 1.6;
    }

    .modal-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn-cancel {
        background: #e5e7eb;
        color: #374151;
        padding: 10px 20px;
        font-size: 14px;
    }

    .btn-cancel:hover {
        background: #d1d5db;
    }

    .tabcontent {
        display: none;
    }

    .tabcontent.active {
        display: block;
    }
</style>
@endpush

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<div class="action-header">
    <div></div>
    <a href="{{ route('kasi.wilayah.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Wilayah Baru
    </a>
</div>

<!-- Tabs -->
<div class="wilayah-tabs">
    <button class="tab-btn active" onclick="openTab(event, 'semua')">
        <i class="fas fa-list"></i> Semua Wilayah
    </button>
    <button class="tab-btn" onclick="openTab(event, 'dusun')">
        <i class="fas fa-home"></i> Dusun
    </button>
    <button class="tab-btn" onclick="openTab(event, 'rt')">
        <i class="fas fa-users"></i> RT
    </button>
    <button class="tab-btn" onclick="openTab(event, 'rw')">
        <i class="fas fa-user-friends"></i> RW
    </button>
</div>

<!-- Semua Wilayah -->
<div id="semua" class="tabcontent active">
    <div class="wilayah-table-container">
        <div class="table-header">
            <h3>
                <i class="fas fa-map-marked-alt"></i>
                Semua Wilayah ({{ $wilayah->count() }})
            </h3>
        </div>

        @if($wilayah->count() > 0)
            <table class="wilayah-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Wilayah</th>
                        <th>Tipe</th>
                        <th>Luas</th>
                        <th>Jumlah Penduduk</th>
                        <th>Kepadatan</th>
                        <th>Koordinat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($wilayah as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="wilayah-info">
                                    <div class="wilayah-icon">
                                        <i class="fas fa-{{ $item->tipe === 'dusun' ? 'home' : ($item->tipe === 'rt' ? 'users' : 'user-friends') }}"></i>
                                    </div>
                                    <div class="wilayah-details">
                                        <h4>{{ $item->nama }}</h4>
                                        @if($item->nomor_rt)
                                            <p>RT {{ $item->nomor_rt }}</p>
                                        @elseif($item->nomor_rw)
                                            <p>RW {{ $item->nomor_rw }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $item->tipe }}">
                                    {{ ucfirst($item->tipe) }}
                                </span>
                            </td>
                            <td>{{ $item->luas_formatted }}</td>
                            <td>
                                <div class="stat-value">
                                    <i class="fas fa-users"></i>
                                    {{ number_format($item->jumlah_penduduk) }}
                                </div>
                            </td>
                            <td>
                                @if($item->kepadatan > 0)
                                    {{ number_format($item->kepadatan, 2) }} jiwa/km²
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <small>{{ $item->latitude }}, {{ $item->longitude }}</small>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('kasi.wilayah.edit', $item->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $item->id }}, '{{ $item->nama }}')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <i class="fas fa-map"></i>
                <h3>Belum Ada Data Wilayah</h3>
                <p>Klik tombol "Tambah Wilayah Baru" untuk menambahkan wilayah</p>
            </div>
        @endif
    </div>
</div>

<!-- Dusun Only -->
<div id="dusun" class="tabcontent">
    <div class="wilayah-table-container">
        <div class="table-header">
            <h3>
                <i class="fas fa-home"></i>
                Daftar Dusun ({{ $wilayah->where('tipe', 'dusun')->count() }})
            </h3>
        </div>

        @if($wilayah->where('tipe', 'dusun')->count() > 0)
            <table class="wilayah-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Dusun</th>
                        <th>Luas</th>
                        <th>Jumlah Penduduk</th>
                        <th>Kepadatan</th>
                        <th>Koordinat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($wilayah->where('tipe', 'dusun') as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="wilayah-info">
                                    <div class="wilayah-icon">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <div class="wilayah-details">
                                        <h4>{{ $item->nama }}</h4>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item->luas_formatted }}</td>
                            <td>
                                <div class="stat-value">
                                    <i class="fas fa-users"></i>
                                    {{ number_format($item->jumlah_penduduk) }}
                                </div>
                            </td>
                            <td>
                                @if($item->kepadatan > 0)
                                    {{ number_format($item->kepadatan, 2) }} jiwa/km²
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <small>{{ $item->latitude }}, {{ $item->longitude }}</small>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('kasi.wilayah.edit', $item->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $item->id }}, '{{ $item->nama }}')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <i class="fas fa-home"></i>
                <h3>Belum Ada Data Dusun</h3>
                <p>Klik tombol "Tambah Wilayah Baru" untuk menambahkan dusun</p>
            </div>
        @endif
    </div>
</div>

<!-- RT Only -->
<div id="rt" class="tabcontent">
    <div class="wilayah-table-container">
        <div class="table-header">
            <h3>
                <i class="fas fa-users"></i>
                Daftar RT ({{ $wilayah->where('tipe', 'rt')->count() }})
            </h3>
        </div>

        @if($wilayah->where('tipe', 'rt')->count() > 0)
            <table class="wilayah-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama RT</th>
                        <th>Nomor</th>
                        <th>Luas</th>
                        <th>Koordinat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($wilayah->where('tipe', 'rt') as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="wilayah-info">
                                    <div class="wilayah-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="wilayah-details">
                                        <h4>{{ $item->nama }}</h4>
                                        <p>RT {{ $item->nomor_rt }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item->nomor_rt }}</td>
                            <td>{{ $item->luas_formatted }}</td>
                            <td>
                                <small>{{ $item->latitude }}, {{ $item->longitude }}</small>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('kasi.wilayah.edit', $item->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $item->id }}, '{{ $item->nama }}')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>Belum Ada Data RT</h3>
                <p>Klik tombol "Tambah Wilayah Baru" untuk menambahkan RT</p>
            </div>
        @endif
    </div>
</div>

<!-- RW Only -->
<div id="rw" class="tabcontent">
    <div class="wilayah-table-container">
        <div class="table-header">
            <h3>
                <i class="fas fa-user-friends"></i>
                Daftar RW ({{ $wilayah->where('tipe', 'rw')->count() }})
            </h3>
        </div>

        @if($wilayah->where('tipe', 'rw')->count() > 0)
            <table class="wilayah-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama RW</th>
                        <th>Nomor</th>
                        <th>Luas</th>
                        <th>Koordinat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($wilayah->where('tipe', 'rw') as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="wilayah-info">
                                    <div class="wilayah-icon">
                                        <i class="fas fa-user-friends"></i>
                                    </div>
                                    <div class="wilayah-details">
                                        <h4>{{ $item->nama }}</h4>
                                        <p>RW {{ $item->nomor_rw }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item->nomor_rw }}</td>
                            <td>{{ $item->luas_formatted }}</td>
                            <td>
                                <small>{{ $item->latitude }}, {{ $item->longitude }}</small>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('kasi.wilayah.edit', $item->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $item->id }}, '{{ $item->nama }}')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <i class="fas fa-user-friends"></i>
                <h3>Belum Ada Data RW</h3>
                <p>Klik tombol "Tambah Wilayah Baru" untuk menambahkan RW</p>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>Konfirmasi Hapus</h3>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus wilayah <strong id="deleteWilayahName"></strong>?</p>
            <p>Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-cancel" onclick="closeDeleteModal()">
                Batal
            </button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        
        // Hide all tabcontent
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
        }
        
        // Remove active class from all tabs
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }
        
        // Show current tab and mark button as active
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("active");
    }

    function confirmDelete(wilayahId, wilayahName) {
        const modal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const deleteWilayahName = document.getElementById('deleteWilayahName');
        
        deleteForm.action = `/kasi/wilayah/${wilayahId}`;
        deleteWilayahName.textContent = wilayahName;
        modal.classList.add('active');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('active');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target === modal) {
            closeDeleteModal();
        }
    }
</script>
@endsection
