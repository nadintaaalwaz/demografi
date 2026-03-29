@extends('kasun.layout')

@section('title', 'Profil Akun')
@section('page-title', 'Profil Kasun')

@push('styles')
<style>
    .profile-wrapper {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 24px;
    }

    .profile-card,
    .profile-detail-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        padding: 24px;
    }

    .avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 700;
        margin: 0 auto 16px;
        color: #fff;
        background: linear-gradient(135deg, #076653, #0C342C);
    }

    .profile-name {
        text-align: center;
        font-size: 20px;
        font-weight: 700;
        color: #0C342C;
        margin-bottom: 6px;
    }

    .profile-subtitle {
        text-align: center;
        color: #6b7280;
        font-size: 14px;
        margin-bottom: 18px;
    }

    .badge-role {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        border-radius: 999px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 700;
    }

    .text-center {
        text-align: center;
    }

    .meta-list {
        margin-top: 18px;
        border-top: 1px solid #f1f5f9;
        padding-top: 14px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        padding: 8px 0;
        color: #374151;
    }

    .meta-item span:first-child {
        color: #6b7280;
    }

    .profile-detail-title {
        margin: 0 0 16px;
        color: #0C342C;
        font-size: 18px;
        font-weight: 700;
        padding-bottom: 12px;
        border-bottom: 2px solid #f3f4f6;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(220px, 1fr));
        gap: 14px 18px;
    }

    .detail-item {
        background: #f9fafb;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 14px;
    }

    .detail-label {
        display: block;
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        font-weight: 700;
    }

    .detail-value {
        display: block;
        font-size: 15px;
        color: #111827;
        font-weight: 600;
        word-break: break-word;
    }

    @media (max-width: 992px) {
        .profile-wrapper {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="profile-wrapper">
    <div class="profile-card">
        <div class="avatar">{{ $user->initials }}</div>
        <h2 class="profile-name">{{ $user->nama }}</h2>
        <p class="profile-subtitle">{{ '@' . $user->username }}</p>

        <div class="text-center">
            <span class="badge-role">
                <i class="fas fa-user-shield"></i>
                {{ strtoupper($user->role) }}
            </span>
        </div>

        <div class="meta-list">
            <div class="meta-item">
                <span>ID User</span>
                <strong>#{{ $user->id }}</strong>
            </div>
            <div class="meta-item">
                <span>Terakhir Diperbarui</span>
                <strong>{{ optional($user->updated_at)->format('d M Y H:i') ?? '-' }}</strong>
            </div>
        </div>
    </div>

    <div class="profile-detail-card">
        <h3 class="profile-detail-title">Detail Akun</h3>

        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Nama Lengkap</span>
                <span class="detail-value">{{ $user->nama }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Username</span>
                <span class="detail-value">{{ $user->username }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Role</span>
                <span class="detail-value">{{ strtoupper($user->role) }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Dusun</span>
                <span class="detail-value">{{ $user->dusun_name }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Dibuat Pada</span>
                <span class="detail-value">{{ optional($user->created_at)->format('d M Y H:i') ?? '-' }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Data Sumber</span>
                <span class="detail-value">Tabel users</span>
            </div>
        </div>
    </div>
</div>
@endsection
