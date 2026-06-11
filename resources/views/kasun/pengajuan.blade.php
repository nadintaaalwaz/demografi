@extends('kasun.layout')

@section('title', 'Pengajuan Penduduk')
@section('page-title', 'Pengajuan Penduduk')


@push('styles')
<style>
    .page-wrap {
        display: grid;
        grid-template-columns: 1fr;
        gap: 18px;
    }

    .card {
        background: #ffffff;
        border: 1px solid #edf2f7;
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        padding: 18px;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .card-title {
        font-size: 18px;
        font-weight: 800;
        color: #0C342C;
        margin: 0;
    }

    .btn {
        border: 0;
        cursor: pointer;
        padding: 10px 14px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: transform .15s ease, opacity .15s ease;
    }

    .btn:active { transform: translateY(1px); }

    .btn-primary {
        background: linear-gradient(135deg, #076653, #0C342C);
        color: #fff;
    }

    .btn-outline {
        background: #fff;
        border: 1px solid #e5e7eb;
        color: #0C342C;
    }

    .btn-danger {
        background: #ef4444;
        color: #fff;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-weight: 900;
        font-size: 12px;
        letter-spacing: .2px;
    }
    .badge-waiting {
        background: rgba(245, 158, 11, 0.18);
        border: 1px solid rgba(245, 158, 11, 0.35);
        color: #b45309;
    }
    .badge-approved {
        background: rgba(16, 185, 129, 0.18);
        border: 1px solid rgba(16, 185, 129, 0.35);
        color: #065f46;
    }
    .badge-rejected {
        background: rgba(239, 68, 68, 0.18);
        border: 1px solid rgba(239, 68, 68, 0.35);
        color: #991b1b;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 12px;
    }

    .field label {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 800;
        color: #374151;
    }

    .field input,
    .field select,
    .field textarea {
        width: 100%;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 10px 12px;
        font-size: 14px;
        outline: none;
    }

    .field textarea { min-height: 110px; resize: vertical; }

    .table-wrap {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    th, td {
        padding: 12px 10px;
        border-bottom: 1px solid #eef2f7;
        text-align: left;
        vertical-align: top;
    }

    th {
        background: #f9fafb;
        color: #374151;
        font-weight: 900;
        font-size: 13px;
        white-space: nowrap;
    }

    .help-text {
        font-size: 12px;
        color: #6b7280;
        margin-top: 6px;
        line-height: 1.5;
    }

    @media (max-width: 920px) {
        .grid-2, .grid-3 { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="page-wrap">
    {{-- Flash --}}
    @if(session('success') || session('error') || $errors->any())
        <div class="card" style="padding: 14px 16px;">
            @if(session('success'))
                <div style="color:#065f46; font-weight:900;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div style="color:#991b1b; font-weight:900;">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <ul style="margin: 10px 0 0 18px; color:#991b1b; font-weight:700;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    {{-- Header actions --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Riwayat Pengajuan</h3>
            <a href="{{ route('kasun.pengajuan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Buat Pengajuan
            </a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode Pengajuan</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Catatan</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($pengajuan ?? collect()) as $p)
                        @php
                            $status = $p->status;
                            $badgeClass = 'badge-waiting';
                            if ($status === 'disetujui') $badgeClass = 'badge-approved';
                            if ($status === 'ditolak') $badgeClass = 'badge-rejected';

                            $jenisLabel = match($p->jenis_pengajuan) {
                                'kelahiran' => 'Kelahiran',
                                'kematian' => 'Kematian',
                                'migrasi_masuk' => 'Migrasi Masuk',
                                'migrasi_keluar' => 'Migrasi Keluar',
                                'perubahan_data' => 'Perubahan Data Penduduk',
                                default => $p->jenis_pengajuan,
                            };
                        @endphp
                        <tr>
                            <td><b>{{ $p->kode_pengajuan }}</b></td>
                            <td>{{ $jenisLabel }}</td>
                            <td>
                                <span class="badge {{ $badgeClass }}">
                                    {{ ucfirst(str_replace('_',' ',$p->status)) }}
                                </span>
                            </td>
                            <td>{{ $p->created_at ? $p->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $p->catatan ? $p->catatan : '-' }}</td>
                            <td>
                                <a href="{{ route('kasun.pengajuan.index') }}" class="btn btn-outline" style="padding: 8px 12px;">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="color:#6b7280; font-weight:800;">Belum ada pengajuan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if(isset($pengajuan) && $pengajuan instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div style="margin-top:14px; display:flex; justify-content:center;">
                    {{ $pengajuan->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Form Create (tampilan inline opsional). 
         Karena Controller saat ini memanggil view 'pengajuan.create', 
         namun requirement user meminta nama file kasun/pengajuan.blade.php,
         maka form create tidak ditampilkan inline di file ini.
         File form create tersedia di `resources/views/kasun/pengajuan_create.blade.php`.
    --}}

    <div class="help-text">
        Halaman ini didesain khusus untuk Kasun.</b>.
    </div>
</div>
@endsection

