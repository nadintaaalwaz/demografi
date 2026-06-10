@extends('kasi.layout')

@section('title', 'Tambah Penduduk')
@section('page-title', 'Tambah Data Penduduk Baru')

@push('styles')
<style>
    .form-wrapper {
        max-width: 800px;
        margin: 0 auto;
    }

    .card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 32px;
        margin-bottom: 24px;
    }

    .form-intro {
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f3f4f6;
        color: #6b7280;
        font-size: 14px;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #111827;
        font-size: 14px;
    }

    .form-label .required {
        color: #ef4444;
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 12px 14px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.3s ease;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: #076653;
        box-shadow: 0 0 0 3px rgba(7, 102, 83, 0.1);
    }

    .form-input::placeholder {
        color: #9ca3af;
    }

    .form-error {
        color: #ef4444;
        font-size: 13px;
        margin-top: 6px;
    }

    .alert {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 24px;
        display: flex;
        align-items: start;
        gap: 12px;
    }

    .alert-success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .alert-danger {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .alert ul {
        margin: 0;
        padding-left: 20px;
    }

    .alert li {
        margin-bottom: 6px;
    }

    .button-group {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid #f3f4f6;
    }

    .btn {
        border: none;
        border-radius: 10px;
        padding: 12px 24px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
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

    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .form-two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    @media (max-width: 768px) {
        .card {
            padding: 20px;
        }

        .button-group {
            flex-direction: column;
        }

        .btn {
            justify-content: center;
            width: 100%;
        }

        .form-two-col {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="form-wrapper">
    <div class="card">
        <div class="form-intro">
            <i class="fas fa-plus-circle" style="color: #076653; margin-right: 8px;"></i>
            Masukkan data penduduk baru. Semua field bertanda <span class="required">*</span> wajib diisi.
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <div>{{ session('success') }}</div>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <strong>Validasi Gagal!</strong>
                <ul>
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <form action="{{ route('kasi.penduduk.store') }}" method="POST" novalidate>
            @csrf

            <div class="form-two-col">
                <div class="form-group">
                    <label class="form-label">NIK <span class="required">*</span></label>
                    <input type="text" name="nik" value="{{ old('nik') }}" 
                           class="form-input" placeholder="16 digit nomor identitas" required>
                    @error('nik')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor KK</label>
                    <input type="text" name="nomor_kartu_keluarga" value="{{ old('nomor_kartu_keluarga') }}" 
                           class="form-input" placeholder="16 digit nomor kartu keluarga">
                    @error('nomor_kartu_keluarga')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" 
                       class="form-input" placeholder="Nama lengkap sesuai KTP" required>
                @error('nama_lengkap')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Alamat <span class="required">*</span></label>
                <textarea name="alamat" class="form-input" rows="3" placeholder="Alamat lengkap sesuai domisili" required>{{ old('alamat') }}</textarea>
                @error('alamat')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-two-col">
                <div class="form-group">
                    <label class="form-label">Status Keluarga</label>
                    <select name="status_keluarga" class="form-select">
                        <option value="">-- Pilih Status Keluarga --</option>
                        <option value="Kepala Keluarga" {{ old('status_keluarga') == 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                        <option value="Istri" {{ old('status_keluarga') == 'Istri' ? 'selected' : '' }}>Istri</option>
                        <option value="Anak" {{ old('status_keluarga') == 'Anak' ? 'selected' : '' }}>Anak</option>
                    </select>
                    @error('status_keluarga')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Status Perkawinan</label>
                    <select name="status_perkawinan" class="form-select">
                        <option value="">-- Pilih Status Perkawinan --</option>
                        <option value="Belum Kawin" {{ old('status_perkawinan') == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                        <option value="Kawin" {{ old('status_perkawinan') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                        <option value="Cerai Hidup" {{ old('status_perkawinan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                        <option value="Cerai Mati" {{ old('status_perkawinan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                    </select>
                    @error('status_perkawinan')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-two-col">
                <div class="form-group">
                    <label class="form-label">Pendidikan</label>
                    <input type="text" name="pendidikan" value="{{ old('pendidikan') }}" class="form-input" placeholder="Contoh: SMA, S1">
                    @error('pendidikan')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Pekerjaan</label>
                    <input type="text" name="pekerjaan" value="{{ old('pekerjaan') }}" class="form-input" placeholder="Contoh: Petani, IRT">
                    @error('pekerjaan')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-two-col">
                <div class="form-group">
                    <label class="form-label">RW</label>
                    <input type="text" name="rw" value="{{ old('rw') }}" class="form-input" placeholder="RW">
                    @error('rw')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">RT</label>
                    <input type="text" name="rt" value="{{ old('rt') }}" class="form-input" placeholder="RT">
                    @error('rt')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-two-col">
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin <span class="required">*</span></label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                    </select>
                    @error('jenis_kelamin')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" 
                           class="form-input" placeholder="Kota/Kabupaten">
                    @error('tempat_lahir')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-two-col">
                <div class="form-group">
                    <label class="form-label">Tanggal Lahir <span class="required">*</span></label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" 
                           class="form-input">
                    @error('tanggal_lahir')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Dusun</label>
                    <select name="id_dusun" class="form-select">
                        <option value="">-- Pilih Dusun --</option>
                        @foreach($dusunList as $d)
                        <option value="{{ $d->id }}" {{ old('id_dusun') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                        @endforeach
                    </select>
                    @error('id_dusun')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-two-col">
                <div class="form-group">
                    <label class="form-label">Status <span class="required">*</span></label>
                    <div class="form-group">
                        <label class="form-label">Status</label>

                        <input
                            type="text"
                            class="form-input"
                            value="Aktif"
                            readonly
                        >

                        <input
                            type="hidden"
                            name="status"
                            value="Aktif"
                        >
                    </div>
                    @error('status')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Data Penduduk
                </button>
                <a href="{{ route('kasi.penduduk.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
