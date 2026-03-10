<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Upload Data') - SIDESA Desa Sebalor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0C342C 0%, #1a5245 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            margin-bottom: 30px;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 8px;
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

        .alert-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
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

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .file-upload-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-input {
            width: 100%;
            padding: 16px;
            border: 2px dashed #0C342C;
            border-radius: 8px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-input:hover {
            border-color: #E3EF26;
            background: #fffef0;
        }

        .file-info {
            margin-top: 12px;
            padding: 12px;
            background: #e8f5e9;
            border-radius: 6px;
            display: none;
        }

        .file-info.active {
            display: block;
        }

        .btn {
            display: inline-block;
            padding: 14px 32px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-primary {
            background: #0C342C;
            color: white;
        }

        .btn-primary:hover {
            background: #1a5245;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(12, 52, 44, 0.3);
        }

        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .info-box {
            background: #fff9e6;
            border-left: 4px solid #E3EF26;
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
            padding-left: 24px;
        }

        .info-box li {
            margin-bottom: 8px;
            color: #555;
        }

        .button-group {
            display: flex;
            gap: 16px;
            margin-top: 24px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, #0C342C 0%, #1a5245 100%);
            color: white;
            padding: 24px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-card h4 {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 8px;
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .card {
                padding: 24px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-upload"></i> Upload Data Penduduk (Bank KK)</h1>
            <p>Upload file Excel (.xlsx, .xls) berisi data master penduduk desa</p>
        </div>

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
            @if(session('errors') && is_array(session('errors')))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="alert-content">
                    <strong>Data Tidak Valid!</strong>
                    <p>Ditemukan {{ count(session('errors')) }} baris dengan error:</p>
                    <ul class="error-list">
                        @foreach(array_slice(session('errors'), 0, 10) as $error)
                        <li class="error-item">
                            <strong>Baris {{ $error['row'] }}:</strong>
                            <ul>
                                @foreach($error['errors'] as $msg)
                                <li>{{ $msg }}</li>
                                @endforeach
                            </ul>
                        </li>
                        @endforeach
                        @if(count(session('errors')) > 10)
                        <li class="error-item"><em>... dan {{ count(session('errors')) - 10 }} error lainnya</em></li>
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
                    <li><strong>Header kolom wajib (baris pertama):</strong> nik, nama_lengkap, jenis_kelamin, tanggal_lahir, alamat, dusun, pendidikan, pekerjaan, nomor_kartu_keluarga</li>
                    <li>Format NIK dan Nomor KK: 16 digit angka</li>
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
                        <i class="fas fa-file-excel"></i> Pilih File Excel/CSV
                    </label>
                    <input 
                        type="file" 
                        name="file_excel" 
                        id="file_excel" 
                        class="file-input"
                        accept=".xlsx,.xls,.csv"
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

    <script>
        // Handle file selection
        document.getElementById('file_excel').addEventListener('change', function(e) {
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

        // Handle form submission
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        });
    </script>
</body>
</html>
