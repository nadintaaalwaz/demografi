@extends('kasi.layout')

@section('title', 'Daftar Pengajuan')
@section('page-title', 'Daftar Pengajuan')

@push('styles')
<style>
    .page-wrap { display:grid; gap:16px; }
    .card {
        background: rgba(255,255,255,0.9);
        border: 1px solid rgba(96,225,194,0.25);
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 12px 30px rgba(5, 74, 63, 0.08);
    }
    .card-header {
        display:flex; justify-content:space-between; align-items:center; gap:12px;
        margin-bottom: 12px;
    }
    .card-title {
        font-size: 18px; font-weight: 900; color:#0C342C;
        margin:0;
    }

    .table-wrap { overflow-x:auto; }

    table { width:100%; border-collapse: collapse; font-size: 13px; }
    th, td {
        padding: 12px 10px;
        border-bottom: 1px solid rgba(96,225,194,0.18);
        text-align:left;
        vertical-align: top;
    }
    th {
        background: rgba(12,52,44,0.06);
        color:#0C342C;
        font-weight: 900;
        white-space: nowrap;
    }

    .badge {
        display:inline-flex;
        align-items:center;
        padding: 4px 10px;
        border-radius: 999px;
        font-weight: 900;
        font-size: 12px;
        border: 1px solid transparent;
    }
    .badge-menunggu { background: rgba(245, 158, 11, 0.15); border-color: rgba(245,158,11,0.35); color:#92400e; }
    .badge-disetujui { background: rgba(16, 185, 129, 0.15); border-color: rgba(16,185,129,0.35); color:#065f46; }
    .badge-ditolak { background: rgba(239, 68, 68, 0.15); border-color: rgba(239,68,68,0.35); color:#991b1b; }

    .btn {
        border: 1px solid rgba(96,225,194,0.35);
        background: rgba(39,225,176,0.12);
        color:#075b4f;
        border-radius: 12px;
        padding: 10px 14px;
        font-weight: 900;
        cursor: pointer;
        display:inline-flex;
        align-items:center;
        gap:8px;
        text-decoration:none;
    }
    .btn-danger {
        border-color: rgba(239,68,68,0.35);
        background: rgba(239,68,68,0.12);
        color:#991b1b;
    }
    .btn-primary {
        border-color: rgba(16,185,129,0.35);
        background: rgba(16,185,129,0.12);
        color:#065f46;
    }

    .actions { display:flex; gap:8px; flex-wrap:wrap; }

    .detail-row {
        color:#374151;
        font-size: 13px;
    }

    .form-inline {
        display:flex; flex-direction:column; gap:10px;
        margin-top: 8px;
    }
    .form-inline textarea {
        width:100%; min-height: 90px; resize: vertical;
        border-radius: 12px;
        border:1px solid rgba(229,231,235,1);
        padding: 10px 12px;
        outline:none;
    }

    .submit-row { display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap; }

</style>
@endpush

@section('content')
<div class="page-wrap">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Pengajuan</h3>
        </div>

        @if(session('success'))
            <div style="margin-bottom:12px; padding:12px 14px; border-radius:12px; background: rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.35); color:#065f46; font-weight:900;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="margin-bottom:12px; padding:12px 14px; border-radius:12px; background: rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.35); color:#991b1b; font-weight:900;">
                {{ session('error') }}
            </div>
        @endif

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Jenis</th>
                        <th>Pengaju</th>
                        <th>Status</th>
                        <th>Waktu</th>
                        <th>Detail</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuan as $p)
                        @php
                            $badgeClass = 'badge-menunggu';
                            if($p->status === 'disetujui') $badgeClass = 'badge-disetujui';
                            if($p->status === 'ditolak') $badgeClass = 'badge-ditolak';

                            $jenisLabel = match($p->jenis_pengajuan) {
                                'kelahiran' => 'Kelahiran',
                                'kematian' => 'Kematian',
                                'migrasi_masuk' => 'Migrasi Masuk',
                                'migrasi_keluar' => 'Migrasi Keluar',
                                'perubahan_data' => 'Perubahan Data',
                                default => $p->jenis_pengajuan,
                            };
                        @endphp
                        <tr>
                            <td><b>{{ $p->kode_pengajuan }}</b></td>
                            <td>{{ $jenisLabel }}</td>
                            <td>{{ optional($p->pengaju)->nama ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $badgeClass }}">{{ $p->status }}</span>
                            </td>
                            <td>{{ $p->created_at ? $p->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="detail-row">
                                <div><b>NIK:</b> {{ $p->nik ?? '-' }}</div>
                                @if(!empty($p->catatan))
                                    <div style="margin-top:6px;"><b>Catatan:</b> {{ $p->catatan }}</div>
                                @endif
                            </td>
                            <td>
                                @if($p->status === 'menunggu')
                                    <div class="actions">
                                        <form method="POST" action="{{ route('pengajuan.approve', $p->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-primary" onclick="return confirm('Setujui pengajuan ini?')">
                                                <i class="fas fa-check"></i> Setujui
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('pengajuan.reject', $p->id) }}" class="form-inline">
                                            @csrf
                                            <textarea name="alasan_penolakan" placeholder="Alasan penolakan..." required></textarea>
                                            <div class="submit-row">
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Tolak pengajuan ini?')">
                                                    <i class="fas fa-times"></i> Tolak
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    <div class="detail-row">Tidak ada aksi</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="color:#6b7280; font-weight:900; padding:18px 10px;">Belum ada pengajuan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($pengajuan) && $pengajuan instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div style="margin-top:14px; display:flex; justify-content:center;">
                {{ $pengajuan->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

