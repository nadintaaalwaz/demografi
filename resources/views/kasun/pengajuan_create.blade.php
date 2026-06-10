@extends('kasun.layout')

@section('title', 'Buat Pengajuan')
@section('page-title', 'Buat Pengajuan')

@push('styles')
<style>
    .page-wrap { display:grid; grid-template-columns:1fr; gap:18px; }
    .card { background:#fff; border:1px solid #edf2f7; border-radius:16px; box-shadow:0 12px 30px rgba(15,23,42,0.06); padding:18px; }
    .card-header { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:12px; }
    .card-title { font-size:18px; font-weight:900; color:#0C342C; margin:0; }
    .btn { border:0; cursor:pointer; padding:10px 14px; border-radius:12px; font-weight:900; font-size:14px; display:inline-flex; align-items:center; gap:8px; }
    .btn-primary { background:linear-gradient(135deg,#076653,#0C342C); color:#fff; }
    .btn-outline { background:#fff; border:1px solid #e5e7eb; color:#0C342C; }

    .grid-2 {
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:20px;
        align-items:start;
    }

    .field {
        display:flex;
        flex-direction:column;
    }

    .field textarea {
        min-height:120px;
        resize:vertical;
    }
    .full-width {
        grid-column: 1 / -1;
    }
    .field label { display:block; margin-bottom:6px; font-size:12px; font-weight:900; color:#374151; }
    .field input, .field select, .field textarea { width:100%; border-radius:12px; border:1px solid #e5e7eb; padding:10px 12px; font-size:14px; outline:none; }
    .field textarea { min-height:110px; resize:vertical; }
    .help { font-size:12px; color:#6b7280; margin-top:6px; line-height:1.5; }

    @media(max-width:920px){ .grid-2{ grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="page-wrap">
    @if(session('success') || session('error'))
        <div class="card" style="padding:14px 16px;">
            @if(session('success'))
                <div style="color:#065f46; font-weight:900;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div style="color:#991b1b; font-weight:900;">{{ session('error') }}</div>
            @endif
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Pengajuan</h3>
            <a href="{{ route('pengajuan.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('pengajuan.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid-2">
                <div class="field" id="fieldJenis">
                    <label for="jenis_pengajuan">Jenis Pengajuan</label>
                    <select name="jenis_pengajuan" id="jenis_pengajuan" required>
                        <option value="" disabled selected>-- Pilih Jenis --</option>
                        <option value="kelahiran">Kelahiran</option>
                        <option value="migrasi_masuk">Migrasi Masuk</option>
                        <option value="kematian">Kematian</option>
                        <option value="migrasi_keluar">Migrasi Keluar</option>
                    </select>
                    @error('jenis_pengajuan')<div style="color:#991b1b; font-weight:800; font-size:12px; margin-top:6px;">{{ $message }}</div>@enderror
                </div>

                {{-- Khusus kematian & migrasi_keluar butuh pencarian NIK --}}
                <div class="field" id="fieldNik" style="display:none;">
                    <label for="nik">Pilih Penduduk (Khusus Kematian & Migrasi Keluar)</label>
                    <select name="nik" id="nik">
                        <option value="">-- Pilih NIK --</option>
                        @foreach($penduduk as $p)
                            <option value="{{ $p->nik }}">{{ $p->nik }} - {{ $p->nama_lengkap }}</option>
                        @endforeach
                    </select>
                    <div class="help">NIK diperlukan untuk pencatatan Meninggal/Keluar.</div>
                </div>
            </div>
            <div class="field" style="margin-top:12px;" id="fieldLampiran">
                <label for="lampiran">Upload Lampiran</label>
                <input
                    type="file"
                    name="lampiran[]"
                    id="lampiran"
                    multiple
                    accept="image/*,application/pdf"
                >
                <div class="help">Lampiran opsional. Bisa lebih dari 1 file (pdf/jpg/png/webp).</div>
                @error('lampiran')
                    <div style="color:#991b1b;font-weight:800;font-size:12px;margin-top:6px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="field" style="margin-top:12px;" id="fieldCatatan">
                <label for="catatan">Catatan</label>
                <textarea
                    name="catatan"
                    id="catatan"
                    placeholder="Catatan tambahan dari Kasun..."
                >{{ old('catatan') }}</textarea>
                <div class="help">Catatan opsional untuk Kasi Pemerintahan.</div>
                @error('catatan')
                    <div style="color:#991b1b;font-weight:800;font-size:12px;margin-top:6px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- Kematian & Migrasi Keluar: tampilkan tambahan --}}
            <div class="field" style="margin-top:12px; display:none;" id="fieldKeteranganMeninggalKeluar">
                <label>Data tambahan untuk Kasi</label>

                <div class="grid-2" style="margin-top:10px;" id="fieldTanggalKeterangan">
                    <input name="tanggal" placeholder="Tanggal kejadian/surat" value="{{ old('tanggal') }}" />
                    <input name="keterangan" placeholder="Keterangan singkat" value="{{ old('keterangan') }}" />
                </div>

                <div class="grid-2" style="margin-top:10px;" id="fieldTanggalMeninggal">
                    <input name="tanggal_meninggal" placeholder="Tanggal meninggal" value="{{ old('tanggal_meninggal') }}" />
                </div>

                <div class="grid-2" style="margin-top:10px;" id="fieldTujuanPindah">
                    <input name="tujuan_pindah" placeholder="Tujuan Pindah" value="{{ old('tujuan_pindah') }}" />
                </div>

                <div class="help" style="margin-top:8px;">Catatan: data ini disimpan ke <b>data_pengajuan</b> (JSON) dan diproses oleh Kasi via menu resmi.</div>
            </div>

            <script>
                (function () {
                    const jenisSelect = document.getElementById('jenis_pengajuan');
                    const fieldNik = document.getElementById('fieldNik');
                    const fieldTambah = document.getElementById('fieldKeteranganMeninggalKeluar');
                    const fieldTanggalMeninggal = document.getElementById('fieldTanggalMeninggal');
                    const fieldTujuanPindah = document.getElementById('fieldTujuanPindah');
                    const fieldJenis = document.getElementById('fieldJenis');

                    function updateVisibility() {
                        const jenis = jenisSelect?.value;
                        const isIndividu = jenis === 'kematian' || jenis === 'migrasi_keluar';
                        const isKematian = jenis === 'kematian';
                        const isMigrasiKeluar = jenis === 'migrasi_keluar';

                        if (fieldNik) fieldNik.style.display = isIndividu ? 'block' : 'none';
                        if (fieldTambah) fieldTambah.style.display = isIndividu ? 'block' : 'none';
                        if (fieldTanggalMeninggal) fieldTanggalMeninggal.style.display = isKematian ? 'grid' : 'none';
                        if (fieldTujuanPindah) fieldTujuanPindah.style.display = isMigrasiKeluar ? 'grid' : 'none';

                        if (!isIndividu) {
                            fieldJenis?.classList.add('full-width');
                        } else {
                            fieldJenis?.classList.remove('full-width');
                        }
                    }

                    if (jenisSelect) {
                        jenisSelect.addEventListener('change', updateVisibility);
                        updateVisibility();
                    }
                })();
            </script>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:16px; flex-wrap:wrap;">
                <button type="reset" class="btn btn-outline">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Kirim Pengajuan
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

