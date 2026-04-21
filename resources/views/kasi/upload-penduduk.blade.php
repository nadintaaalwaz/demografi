@extends('kasi.layout')

@section('title', 'Upload Data Penduduk')
@section('page-title', 'Upload Data Penduduk')

@push('styles')
<style>
    .upload-wrapper {
        max-width: 920px;
        margin: 0 auto;
    }

    .card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 32px;
        margin-bottom: 24px;
    }

    .page-intro {
        margin-bottom: 20px;
        color: #4b5563;
        font-size: 14px;
    }

    .alert {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
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

    .alert i {
        margin-top: 2px;
    }

    .alert-content {
        flex: 1;
    }

    .error-list {
        margin-top: 12px;
        padding-left: 20px;
    }

    .error-item {
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .info-box {
        background: #fefce8;
        border-left: 4px solid #eab308;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 24px;
    }

    .info-box h3 {
        color: #0C342C;
        margin-bottom: 12px;
        font-size: 1.1rem;
    }

    .info-box ul {
        padding-left: 20px;
    }

    .info-box li {
        margin-bottom: 8px;
        color: #4b5563;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #111827;
    }

    .file-input {
        width: 100%;
        padding: 14px;
        border: 2px dashed #0C342C;
        border-radius: 10px;
        background: #f9fafb;
        cursor: pointer;
        transition: all 0.2s;
    }

    .file-input:hover {
        border-color: #076653;
        background: #ecfdf5;
    }

    .file-info {
        margin-top: 10px;
        padding: 12px;
        background: #ecfdf5;
        border-radius: 8px;
        display: none;
        color: #065f46;
    }

    .file-info.active {
        display: block;
    }

    .button-group {
        display: flex;
        gap: 12px;
        margin-top: 20px;
    }

    .btn {
        border: none;
        border-radius: 10px;
        padding: 12px 18px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: #076653;
        color: #fff;
    }

    .btn-primary:hover {
        background: #0C342C;
    }

    .btn-primary:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
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
    }
</style>
@endpush

@section('content')
    <div class="upload-wrapper">
        <p class="page-intro">Upload file Excel Bank KK untuk memperbarui data penduduk dan dinamika secara otomatis.</p>

        <div class="card">
            {{-- Alert Success --}}
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <div class="alert-content">
                    <strong>Berhasil!</strong><br>
                    {{ session('success') }}
                </div>
            </div>
            @endif

            {{-- Alert Error --}}
            @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div class="alert-content">
                    <strong>Terjadi Kesalahan!</strong><br>
                    {{ session('error') }}
                </div>
            </div>
            @endif

            {{-- Validation Errors --}}
            @if(session('import_errors') && is_array(session('import_errors')))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="alert-content">
                    <strong>Data Tidak Valid!</strong>
                    <p>Ditemukan {{ count(session('import_errors')) }} error:</p>
                    <ul class="error-list">
                        @foreach(array_slice(session('import_errors'), 0, 10) as $error)
                        <li class="error-item">
                            @if(isset($error['row']) && isset($error['errors']) && is_array($error['errors']))
                                <strong>Baris {{ $error['row'] }}:</strong>
                                <ul>
                                    @foreach($error['errors'] as $msg)
                                    <li>{{ $msg }}</li>
                                    @endforeach
                                </ul>
                            @elseif(isset($error['message']))
                                <strong>Error:</strong> {{ $error['message'] }}
                            @else
                                <strong>Error:</strong> Format pesan error tidak dikenali.
                            @endif
                        </li>
                        @endforeach
                        @if(count(session('import_errors')) > 10)
                        <li class="error-item"><em>... dan {{ count(session('import_errors')) - 10 }} error lainnya</em></li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            {{-- Laravel Validation Errors --}}
            @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div class="alert-content">
                    <strong>Validasi Gagal!</strong>
                    <ul class="error-list">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- Info Box --}}
            <div class="info-box">
                <h3><i class="fas fa-info-circle"></i> Ketentuan Upload Data</h3>
                <ul>
                    <li>File harus berformat Excel (.xlsx atau .xls)</li>
                    <li>Ukuran file maksimal 10 MB</li>
                    <li><strong>Header kolom wajib (baris pertama):</strong> nomor_kartu_keluarga, nik, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, status_keluarga, status_perkawinan, pendidikan, pekerjaan, dusun, rw, rt, alamat, status, tanggal_status</li>
                    <li>Format NIK dan nomor_kartu_keluarga: 16 digit angka</li>
                    <li>Jenis Kelamin: L atau P</li>
                    <li>Format Tanggal Lahir: DD-MM-YYYY, DD/MM/YYYY, atau format Excel date</li>
                    <li>Nama Dusun harus sesuai dengan data di database wilayah</li>
                    <li>Data lama akan digantikan dengan data baru</li>
                    <li>Sistem otomatis mendeteksi kelahiran, kematian, dan migrasi penduduk</li>
                </ul>
            </div>

            {{-- Form Upload --}}
            <form action="{{ route('kasi.upload.process') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                
                <div class="form-group">
                    <label class="form-label" for="file_excel">
                        <i class="fas fa-file-excel"></i> Pilih File Excel
                    </label>
                    <input 
                        type="file" 
                        name="file_excel" 
                        id="file_excel" 
                        class="file-input"
                        accept=".xlsx,.xls"
                        required
                    >
                    <div class="file-info" id="fileInfo">
                        <i class="fas fa-check-circle" style="color: #28a745;"></i>
                        <strong>File dipilih:</strong> <span id="fileName"></span>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-upload"></i> Upload & Proses Data
                    </button>
                    <a href="{{ route('kasi.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('file_excel').addEventListener('change', function() {
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const submitBtn = document.getElementById('submitBtn');

        if (this.files.length > 0) {
            fileName.textContent = this.files[0].name;
            fileInfo.classList.add('active');
            submitBtn.disabled = false;
        } else {
            fileInfo.classList.remove('active');
            submitBtn.disabled = true;
        }
    });

    document.getElementById('uploadForm').addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    });
</script>
@endpush
