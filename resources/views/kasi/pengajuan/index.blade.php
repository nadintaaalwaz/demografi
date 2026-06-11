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
        white-space: nowrap;
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

    .btn-approve{
        background:#dcfce7;
        color:#166534;
        border:1px solid #86efac;
        padding:8px 12px;
        border-radius:10px;
        cursor:pointer;
        font-weight:600;
    }

    .btn-approve:hover{ background:#bbf7d0; }

    .btn-reject{
        background:#fee2e2;
        color:#991b1b;
        border:1px solid #fca5a5;
        padding:8px 12px;
        border-radius:10px;
        cursor:pointer;
        font-weight:600;
    }

    .btn-reject:hover{ background:#fecaca; }

    .detail-row { color:#374151; font-size: 13px; }

    .lampiran-links a.btn { padding: 6px 10px; }

    .action-group{
        display:flex;
        gap:8px;
        flex-wrap:wrap;
    }

    /* Modal reject */
    .modal-overlay{
        position:fixed;
        inset:0;
        display:none;
        align-items:center;
        justify-content:center;
        background:rgba(0,0,0,.55);
        z-index:9999;
        padding: 16px;
    }
    .modal-overlay.is-open{ display:flex; }

    .modal{
        width: 520px;
        max-width: 100%;
        background: rgba(255,255,255,0.98);
        border: 1px solid rgba(96,225,194,0.25);
        border-radius: 16px;
        box-shadow: 0 24px 60px rgba(0,0,0,.25);
        overflow:hidden;
    }
    .modal-header{
        padding: 16px 16px 12px 16px;
        background: linear-gradient(135deg, rgba(16,185,129,0.14), rgba(39,225,176,0.06));
        border-bottom: 1px solid rgba(96,225,194,0.2);
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
    }
    .modal-title{
        margin:0;
        font-weight: 900;
        color:#0C342C;
        font-size: 16px;
    }
    .modal-subtitle{
        margin-top:6px;
        color:#475569;
        font-size: 13px;
        font-weight: 700;
    }
    .modal-body{ padding: 14px 16px 10px 16px; }
    .modal-body textarea{
        width:100%;
        min-height: 120px;
        resize: vertical;
        border-radius: 12px;
        border:1px solid rgba(229,231,235,1);
        padding: 10px 12px;
        outline:none;
        font-size: 13px;
    }
    .modal-footer{
        padding: 12px 16px 16px 16px;
        display:flex;
        justify-content:flex-end;
        gap:10px;
        border-top: 1px solid rgba(96,225,194,0.15);
    }
    .modal-close{
        background: transparent;
        border: 1px solid rgba(148,163,184,.35);
        color:#334155;
        border-radius: 12px;
        padding: 10px 12px;
        font-weight: 800;
        cursor:pointer;
    }
    .modal-close:hover{ background: rgba(148,163,184,.08); }
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
                        <th>Dusun</th>
                        <th>Status</th>
                        <th>Waktu</th>
                        <th>Detail</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuan as $p)
                        @php
                            $badgeClass = match($p->status) {
                                'disetujui' => 'badge-disetujui',
                                'ditolak' => 'badge-ditolak',
                                default => 'badge-menunggu',
                            };

                            $statusLabel = match($p->status) {
                                'menunggu' => 'Menunggu',
                                'disetujui' => 'Disetujui',
                                'ditolak' => 'Ditolak',
                                default => $p->status,
                            };

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
                            <td>{{ $p->pengaju?->dusun?->nama ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td>{{ $p->created_at ? $p->created_at->format('d/m/Y H:i') : '-' }}</td>

                            <td class="detail-row">
                                <div><b>NIK:</b> {{ $p->nik ?? '-' }}</div>

                                @if(!empty($p->catatan))
                                    <div style="margin-top:6px;">
                                        <b>Catatan:</b> {{ $p->catatan }}
                                    </div>
                                @endif

                                @php
                                    $data = is_array($p->data_pengajuan)
                                        ? $p->data_pengajuan
                                        : json_decode($p->data_pengajuan, true);

                                    $lampiran = $data['lampiran'] ?? [];
                                @endphp

                                @if(count($lampiran))
                                    <div style="margin-top:10px;" class="lampiran-links">
                                        <b>Lampiran:</b>
                                        @foreach($lampiran as $file)
                                            <div style="margin-top:6px;">
                                                <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn" style="padding:6px 10px;">
                                                    <i class="fas fa-paperclip"></i>
                                                    Lihat Lampiran {{ $loop->iteration }}
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if($p->status === 'ditolak' && !empty($p->alasan_penolakan))
                                    <div style="margin-top:10px;">
                                        <b>Alasan penolakan:</b>
                                        <div style="margin-top:4px; white-space:pre-wrap;">{{ $p->alasan_penolakan }}</div>
                                    </div>
                                @endif
                            </td>

                            <td>
                                @if($p->status === 'menunggu')
                                    <div class="action-group">
                                        <form method="POST" action="{{ route('kasi.pengajuan.approve', $p->id) }}">
                                            @csrf
                                            <button type="submit" class="btn-approve" onclick="return confirm('Apakah Anda yakin ingin menyetujui pengajuan ini?')">
                                                Setujui
                                            </button>
                                        </form>

                                        <button type="button" class="btn-reject" onclick="openRejectModal({{ $p->id }})">
                                            Tolak
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="color:#6b7280; font-weight:900; padding:18px 10px;">Belum ada pengajuan.</td>
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

    {{-- Reject Modal --}}
    <div id="rejectModal" class="modal-overlay" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="rejectModalTitle">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title" id="rejectModalTitle">Tolak Pengajuan</h3>
                    <div class="modal-subtitle">Masukkan alasan penolakan untuk diproses.</div>
                </div>
                <button type="button" class="modal-close" onclick="closeRejectModal()" aria-label="Tutup">✕</button>
            </div>

            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <textarea
                        name="alasan_penolakan"
                        required
                        placeholder="Masukkan alasan penolakan..."
                        id="rejectReason"
                    ></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="modal-close" onclick="closeRejectModal()">Batal</button>
                    <button type="submit" class="btn-reject" style="border-radius:12px; padding:10px 14px;">
                        Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function openRejectModal(id) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        const reason = document.getElementById('rejectReason');

        form.action = "{{ url('kasi/pengajuan') }}/" + id + "/reject";

        reason.value = '';
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');

        setTimeout(() => reason.focus(), 50);
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
    }

    (function() {
        const modal = document.getElementById('rejectModal');
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeRejectModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeRejectModal();
        });
    })();
</script>
@endsection


