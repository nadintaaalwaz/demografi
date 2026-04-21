@extends('kasi.layout')

@section('title', 'Detail Penduduk')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-id-card"></i> Detail Penduduk</h1>
    <p>Informasi lengkap data penduduk</p>
</div>

<div class="card detail-card">
    <div class="card-header">
        <h2>{{ $penduduk->nama_lengkap }}</h2>
        <span class="badge badge-{{ $penduduk->status === 'Aktif' ? 'success' : 'danger' }}">{{ $penduduk->status }}</span>
    </div>

    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item"><label>NIK</label><div>{{ $penduduk->nik }}</div></div>
            <div class="detail-item"><label>Nomor Kartu Keluarga</label><div>{{ $penduduk->nomor_kartu_keluarga ?? '-' }}</div></div>
            <div class="detail-item"><label>Nama Lengkap</label><div>{{ $penduduk->nama_lengkap }}</div></div>
            <div class="detail-item"><label>Jenis Kelamin</label><div>{{ $penduduk->jenis_kelamin }}</div></div>
            <div class="detail-item"><label>Tempat Lahir</label><div>{{ $penduduk->tempat_lahir ?? '-' }}</div></div>
            <div class="detail-item"><label>Tanggal Lahir</label><div>{{ optional($penduduk->tanggal_lahir)->format('d-m-Y') ?? '-' }}</div></div>
            <div class="detail-item"><label>Status Keluarga</label><div>{{ $penduduk->status_keluarga ?? '-' }}</div></div>
            <div class="detail-item"><label>Status Perkawinan</label><div>{{ $penduduk->status_perkawinan ?? '-' }}</div></div>
            <div class="detail-item"><label>Pendidikan</label><div>{{ $penduduk->pendidikan ?? '-' }}</div></div>
            <div class="detail-item"><label>Pekerjaan</label><div>{{ $penduduk->pekerjaan ?? '-' }}</div></div>
            <div class="detail-item"><label>Dusun</label><div>{{ $penduduk->dusun->nama ?? '-' }}</div></div>
            <div class="detail-item"><label>RW</label><div>{{ $penduduk->rw ?? '-' }}</div></div>
            <div class="detail-item"><label>RT</label><div>{{ $penduduk->rt ?? '-' }}</div></div>
            <div class="detail-item"><label>Status</label><div>{{ $penduduk->status }}</div></div>
            <div class="detail-item"><label>Tanggal Status</label><div>{{ optional($penduduk->tanggal_status)->format('d-m-Y') ?? '-' }}</div></div>
            <div class="detail-item detail-item-full"><label>Alamat</label><div>{{ $penduduk->alamat ?? '-' }}</div></div>
        </div>

        <div class="detail-actions">
            <a href="{{ route('kasi.penduduk.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Data Penduduk
            </a>
        </div>
    </div>
</div>

<style>
    .page-header { margin-bottom: 24px; }
    .page-header h1 { font-size: 1.8rem; color: #0C342C; margin-bottom: 8px; }
    .page-header p { color: #666; }

    .detail-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .card-header h2 { margin: 0; color: #0C342C; font-size: 1.2rem; }

    .card-body { padding: 24px; }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 14px;
    }

    .detail-item {
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 12px 14px;
        background: #fafafa;
    }

    .detail-item label {
        display: block;
        font-size: .8rem;
        color: #666;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .detail-item div {
        color: #1f2937;
        font-weight: 500;
        word-break: break-word;
    }

    .detail-item-full { grid-column: 1 / -1; }

    .detail-actions { margin-top: 18px; }

    .badge {
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .badge-success { background: #dcfce7; color: #166534; }
    .badge-danger { background: #fee2e2; color: #991b1b; }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        border: none;
        border-radius: 8px;
        padding: 10px 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-secondary { background: #f3f4f6; color: #374151; }
    .btn-secondary:hover { background: #e5e7eb; }
</style>
@endsection
