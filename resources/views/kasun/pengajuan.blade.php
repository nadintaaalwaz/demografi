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

    @if(isset($pengajuan) && $pengajuan instanceof \App\Models\PengajuanPenduduk)
        {{-- Detail Pengajuan (Kasun) --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Pengajuan</h3>
                <a href="{{ route('kasun.pengajuan.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            @php
                $p = $pengajuan;
                $data = is_array($p->data_pengajuan) ? $p->data_pengajuan : json_decode($p->data_pengajuan, true);
                $lampiran = $data['lampiran'] ?? [];
                $jenisLabel = match($p->jenis_pengajuan) {
                    'kelahiran' => 'Kelahiran',
                    'kematian' => 'Kematian',
                    'migrasi_masuk' => 'Migrasi Masuk',
                    'migrasi_keluar' => 'Migrasi Keluar',
                    'perubahan_data' => 'Perubahan Data Penduduk',
                    default => $p->jenis_pengajuan,
                };

                $badgeClass = 'badge-waiting';
                if ($p->status === 'disetujui') $badgeClass = 'badge-approved';
                if ($p->status === 'ditolak') $badgeClass = 'badge-rejected';
            @endphp

            <div class="grid-2">
                <div class="detail-item" style="padding:12px; border:1px solid #e5e7eb; border-radius:14px; background:#f9fafb;">
                    <span class="detail-label" style="display:block; font-weight:900; color:#374151; font-size:12px;">Kode Pengajuan</span>
                    <span class="detail-value" style="display:block; margin-top:6px; font-weight:800; color:#0C342C;">{{ $p->kode_pengajuan }}</span>
                </div>
                <div class="detail-item" style="padding:12px; border:1px solid #e5e7eb; border-radius:14px; background:#f9fafb;">
                    <span class="detail-label" style="display:block; font-weight:900; color:#374151; font-size:12px;">Jenis Pengajuan</span>
                    <span class="detail-value" style="display:block; margin-top:6px; font-weight:800; color:#0C342C;">{{ $jenisLabel }}</span>
                </div>
            </div>

            <div style="margin-top:12px;">
                <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_',' ',$p->status)) }}</span>
                <span style="margin-left:10px; color:#6b7280; font-weight:800; font-size:12px;">{{ $p->created_at ? $p->created_at->format('d/m/Y H:i') : '-' }}</span>
            </div>

            @if(!empty($p->catatan))
                <div style="margin-top:12px;">
                    <b>Catatan:</b>
                    <div style="margin-top:6px; white-space:pre-wrap;">{{ $p->catatan }}</div>
                </div>
            @endif

            @if(!empty($p->alasan_penolakan))
                <div style="margin-top:12px; border:1px solid rgba(239,68,68,0.25); border-radius:14px; padding:12px; background:rgba(239,68,68,0.06);">
                    <b style="color:#991b1b;">Alasan Penolakan:</b>
                    <div style="margin-top:6px; white-space:pre-wrap;">{{ $p->alasan_penolakan }}</div>
                </div>
            @endif

            <div style="margin-top:14px;">
                <h3 class="card-title">Detail yang diajukan</h3>
                <div style="margin-top:10px;">
                    @php
                        $detailKeys = array_keys($data ?? []);
                    @endphp

                    @forelse(($data ?? []) as $key => $val)
                        @continue($key === 'lampiran')
                        @if(is_array($val))
                            <div style="margin-top:10px;">
                                <b>{{ $key }}:</b>
                                <ul style="margin:6px 0 0 18px;">
                                    @foreach($val as $item)
                                        <li>{{ is_scalar($item) ? $item : json_encode($item) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div style="margin-top:10px;">
                                <b>{{ $key }}:</b>
                                <div style="margin-top:4px; font-weight:700; color:#111827;">{{ $val !== null && $val !== '' ? $val : '-' }}</div>
                            </div>
                        @endif
                    @empty
                        <div style="color:#6b7280; font-weight:800;">Tidak ada detail tambahan.</div>
                    @endforelse
                </div>
            </div>

            <div style="margin-top:16px;">
                <h3 class="card-title">Lampiran</h3>
                @if(!empty($lampiran) && is_array($lampiran) && count($lampiran))
                    <div style="margin-top:10px; display:grid; gap:10px;">
                        @foreach($lampiran as $file)
                            <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-outline" style="justify-content:center;">
                                <i class="fas fa-paperclip"></i> Lihat Lampiran
                            </a>
                        @endforeach
                    </div>
                @else
                    <div style="margin-top:10px; color:#6b7280; font-weight:800;">Belum ada lampiran.</div>
                @endif
            </div>
        </div>
    @else
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
                                    <a href="{{ route('kasi.pengajuan.show', $p->id) }}" class="btn btn-outline" style="padding: 8px 12px;">
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

        <div class="help-text">
            Halaman ini didesain khusus untuk Kasun.</b>.
        </div>
    @endif
</div>
@endsection


