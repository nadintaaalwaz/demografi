@extends('kasi.layout')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User Kasun')

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

    .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        padding: 8px 16px;
        font-size: 13px;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
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
        justify-content: flex-end;
        margin-bottom: 25px;
    }

    .users-table-container {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
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

    .users-table {
        width: 100%;
        border-collapse: collapse;
    }

    .users-table thead {
        background: #f9fafb;
    }

    .users-table th {
        padding: 16px 25px;
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e5e7eb;
    }

    .users-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.2s ease;
    }

    .users-table tbody tr:hover {
        background: #f9fafb;
    }

    .users-table td {
        padding: 18px 25px;
        font-size: 14px;
        color: #374151;
    }

    .table-user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .table-user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #076653, #0C342C);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #E3EF26;
        font-weight: 700;
        font-size: 16px;
    }

    .user-details h4 {
        font-size: 15px;
        font-weight: 600;
        color: #0C342C;
        margin-bottom: 3px;
    }

    .user-details p {
        font-size: 13px;
        color: #6b7280;
    }

    .role-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: #dbeafe;
        color: #1e40af;
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
    <a href="{{ route('kasi.users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Kasun Baru
    </a>
</div>

<div class="users-table-container">
        <div class="table-header">
            <h3>
                <i class="fas fa-list"></i>
                Daftar Akun Kasun ({{ $users->count() }})
            </h3>
        </div>

        @if($users->count() > 0)
            <table class="users-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kasun</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Dusun</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="table-user-info">
                                    <div class="table-user-avatar">
                                        {{ strtoupper(substr($user->nama, 0, 1)) }}
                                    </div>
                                    <div class="user-details">
                                        <h4>{{ $user->nama }}</h4>
                                        <p>Kasun Desa Sebalor</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ $user->dusun->nama ?? '-' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('kasi.users.edit', $user->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $user->id }}, '{{ $user->nama }}')">
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
                <i class="fas fa-users-slash"></i>
                <h3>Belum Ada Data Kasun</h3>
                <p>Klik tombol "Tambah Kasun Baru" untuk menambahkan akun kasun</p>
            </div>
        @endif
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
            <p>Apakah Anda yakin ingin menghapus akun kasun <strong id="deleteUserName"></strong>?</p>
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
    function confirmDelete(userId, userName) {
        const modal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const deleteUserName = document.getElementById('deleteUserName');
        
        deleteForm.action = `/kasi/users/${userId}`;
        deleteUserName.textContent = userName;
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
