@extends('kasi.layout')

@section('title', 'Upload Data Penduduk')
@section('page-title', 'Upload Data Penduduk')

@push('styles')
<style>
    .upload-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .info-card {
        background: linear-gradient(135deg, #076653, #0C342C);
        color: #fff;
        padding: 30px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(7, 102, 83, 0.3);
    }

    .info-card h3 {
        font-size: 20px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-card p {
        font-size: 14px;
        line-height: 1.8;
        opacity: 0.95;
    }

    .info-list {
        list-style: none;
        margin-top: 15px;
    }

    .info-list li {
        padding: 8px 0;
        padding-left: 25px;
        position: relative;
    }

    .info-list li:before {
        content: "✓";
        position: absolute;
        left: 0;
        color: #E3EF26;
        font-weight: bold;
        font-size: 16px;
    }

    .upload-card {
        background: #fff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .upload-area {
        border: 3px dashed #d1d5db;
        border-radius: 12px;
        padding: 60px 30px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        background: #f9fafb;
    }

    .upload-area:hover,
    .upload-area.drag-over {
        border-color: #076653;
        background: #ecfdf5;
    }

    .upload-icon {
        font-size: 64px;
        color: #076653;
        margin-bottom: 20px;
    }

    .upload-area h3 {
        font-size: 20px;
        color: #0C342C;
        margin-bottom: 10px;
    }

    .upload-area p {
        color: #6b7280;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .file-input {
        display: none;
    }

    .btn-browse {
        background: #076653;
        color: #fff;
        padding: 12px 30px;
        border-radius: 10px;
        border: none;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-browse:hover {
        background: #0C342C;
        transform: translateY(-2px);
    }

    .file-info {
        background: #f3f4f6;
        padding: 15px 20px;
        border-radius: 10px;
        margin-top: 20px;
        display: none;
        align-items: center;
        gap: 15px;
    }

    .file-info.show {
        display: flex;
    }

    .file-icon {
        width: 50px;
        height: 50px;
        background: #10b981;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 24px;
    }

    .file-details {
        flex: 1;
    }

    .file-name {
        font-weight: 600;
        color: #0C342C;
        margin-bottom: 3px;
    }

    .file-size {
        font-size: 13px;
        color: #6b7280;
    }

    .btn-remove {
        color: #ef4444;
        cursor: pointer;
        font-size: 20px;
        transition: color 0.3s ease;
    }

    .btn-remove:hover {
        color: #dc2626;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 25px;
    }

    .btn {
        flex: 1;
        padding: 15px;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
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

    .btn-primary:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .template-section {
        background: #fff;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    .template-section h3 {
        font-size: 18px;
        color: #0C342C;
        margin-bottom: 15px;
    }

    .template-section p {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 20px;
    }

    .btn-download {
        background: #10b981;
        color: #fff;
        padding: 12px 30px;
        border-radius: 10px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .btn-download:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
    }

    .progress-container {
        display: none;
        margin-top: 20px;
    }

    .progress-container.show {
        display: block;
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 10px;
    }

    .progress-fill {
        height: 100%;
        background: #076653;
        width: 0%;
        transition: width 0.3s ease;
    }

    .progress-text {
        text-align: center;
        font-size: 14px;
        color: #6b7280;
    }
</style>
@endpush

@section('content')
<div class="upload-container">
    <!-- Info Card -->
    <div class="info-card">
        <h3>
            <i class="fas fa-info-circle"></i>
            Petunjuk Upload Data Penduduk
        </h3>
        <p>Untuk upload data penduduk, pastikan file Excel Anda memenuhi kriteria berikut:</p>
        <ul class="info-list">
            <li>Format file harus .xlsx (Excel 2007 atau lebih baru)</li>
            <li>Kolom wajib: nomor_kartu_keluarga, nik, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, status_keluarga, status_perkawinan, pendidikan, pekerjaan, dusun, rw, rt, alamat, status, tanggal_status</li>
            <li>NIK harus 16 digit dan unik (tidak boleh duplikat)</li>
            <li>Jenis Kelamin harus L atau P</li>
            <li>Tanggal Lahir format: DD-MM-YYYY atau DD/MM/YYYY</li>
            <li>Nama Dusun harus sesuai dengan data yang ada di database</li>
            <li>Upload akan menggantikan semua data lama dengan data baru</li>
        </ul>
    </div>

    <!-- Upload Card -->
    <div class="upload-card">
        <form action="{{ route('kasi.upload.process') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf
            
            <div class="upload-area" id="uploadArea">
                <div class="upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <h3>Upload File Excel</h3>
                <p>Drag & drop file Excel Anda di sini atau klik tombol di bawah</p>
                <button type="button" class="btn-browse" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-folder-open"></i> Pilih File
                </button>
                <input type="file" id="fileInput" name="file" class="file-input" accept=".xlsx,.xls" required>
            </div>

            <div class="file-info" id="fileInfo">
                <div class="file-icon">
                    <i class="fas fa-file-excel"></i>
                </div>
                <div class="file-details">
                    <div class="file-name" id="fileName">-</div>
                    <div class="file-size" id="fileSize">-</div>
                </div>
                <i class="fas fa-times btn-remove" id="btnRemove"></i>
            </div>

            <div class="progress-container" id="progressContainer">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <div class="progress-text" id="progressText">Mengupload... 0%</div>
            </div>

            <div class="action-buttons">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('kasi.penduduk.index') }}'">
                    <i class="fas fa-arrow-left"></i> Batal
                </button>
                <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>
                    <i class="fas fa-upload"></i> Upload & Import Data
                </button>
            </div>
        </form>
    </div>

    <!-- Template Section -->
    <div class="template-section">
        <h3>Download Template Excel</h3>
        <p>Gunakan template di bawah ini sebagai panduan format data yang benar</p>
        <a href="{{ route('kasi.upload.template') }}" class="btn-download">
            <i class="fas fa-download"></i>
            Download Template Excel
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
const fileInput = document.getElementById('fileInput');
const uploadArea = document.getElementById('uploadArea');
const fileInfo = document.getElementById('fileInfo');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');
const btnRemove = document.getElementById('btnRemove');
const btnSubmit = document.getElementById('btnSubmit');
const uploadForm = document.getElementById('uploadForm');
const progressContainer = document.getElementById('progressContainer');

// File input change
fileInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        displayFileInfo(file);
    }
});

// Drag and drop
uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    uploadArea.classList.add('drag-over');
});

uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('drag-over');
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('drag-over');
    
    const file = e.dataTransfer.files[0];
    if (file && (file.name.endsWith('.xlsx') || file.name.endsWith('.xls'))) {
        fileInput.files = e.dataTransfer.files;
        displayFileInfo(file);
    } else {
        alert('File harus berformat .xlsx atau .xls');
    }
});

// Display file info
function displayFileInfo(file) {
    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);
    fileInfo.classList.add('show');
    btnSubmit.disabled = false;
}

// Remove file
btnRemove.addEventListener('click', function() {
    fileInput.value = '';
    fileInfo.classList.remove('show');
    btnSubmit.disabled = true;
});

// Format file size
function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' Bytes';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
}

// Form submit with progress simulation
uploadForm.addEventListener('submit', function(e) {
    // Show progress (in real implementation, use XMLHttpRequest for actual progress)
    progressContainer.classList.add('show');
    btnSubmit.disabled = true;
    
    // Simulate progress (remove this in production)
    let progress = 0;
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    
    const interval = setInterval(function() {
        progress += 10;
        progressFill.style.width = progress + '%';
        progressText.textContent = 'Mengupload... ' + progress + '%';
        
        if (progress >= 100) {
            clearInterval(interval);
            progressText.textContent = 'Memproses data...';
        }
    }, 200);
});
</script>
@endpush
