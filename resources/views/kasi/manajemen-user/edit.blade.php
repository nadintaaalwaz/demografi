@extends('kasi.layout')

@section('title', 'Edit Kasun')
@section('page-title', 'Edit Data Kasun')

@push('styles')
<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .form-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .form-header {
        padding: 25px 30px;
        background: linear-gradient(135deg, #076653, #0C342C);
        color: #fff;
    }

    .form-header h2 {
        font-size: 22px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .form-header p {
        font-size: 14px;
        opacity: 0.9;
        margin-top: 8px;
    }

    .form-body {
        padding: 35px 30px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    .form-group label .required {
        color: #ef4444;
        margin-left: 4px;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .form-control:focus {
        outline: none;
        border-color: #076653;
        box-shadow: 0 0 0 3px rgba(7, 102, 83, 0.1);
    }

    .form-control.error {
        border-color: #ef4444;
    }

    .form-text {
        font-size: 13px;
        color: #6b7280;
        margin-top: 6px;
        display: block;
    }

    .error-message {
        color: #ef4444;
        font-size: 13px;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 20px;
        border-top: 1px solid #f3f4f6;
    }

    .btn {
        padding: 12px 28px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #076653, #0C342C);
        color: #fff;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(7, 102, 83, 0.3);
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #d1d5db;
    }

    .input-group {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 16px;
    }

    .input-group .form-control {
        padding-left: 45px;
    }

    .password-toggle {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 16px;
        cursor: pointer;
        transition: color 0.3s ease;
        z-index: 10;
    }

    .password-toggle:hover {
        color: #076653;
    }

    .input-group.has-toggle .form-control {
        padding-right: 45px;
    }

    .info-box {
        background: #dbeafe;
        border-left: 4px solid #3b82f6;
        padding: 15px 18px;
        border-radius: 8px;
        margin-bottom: 25px;
        display: flex;
        align-items: start;
        gap: 12px;
    }

    .info-box i {
        color: #1e40af;
        font-size: 18px;
        margin-top: 2px;
    }

    .info-box-content {
        flex: 1;
    }

    .info-box-content p {
        font-size: 13px;
        color: #1e40af;
        margin: 0;
        line-height: 1.5;
    }
</style>
@endpush

@section('content')
<div class="form-container">
        <div class="form-card">
            <div class="form-header">
                <h2>
                    <i class="fas fa-user-edit"></i>
                    Edit Data Kasun
                </h2>
                <p>Perbarui informasi akun kasun: {{ $user->nama }}</p>
            </div>

            <form action="{{ route('kasi.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-body">
                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <div class="info-box-content">
                            <p><strong>Catatan:</strong> Kosongkan field password jika tidak ingin mengubah password.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nama">
                            Nama Lengkap
                            <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <i class="fas fa-user input-icon"></i>
                            <input 
                                type="text" 
                                id="nama" 
                                name="nama" 
                                class="form-control @error('nama') error @enderror" 
                                value="{{ old('nama', $user->nama) }}"
                                placeholder="Masukkan nama lengkap kasun"
                                required
                            >
                        </div>
                        @error('nama')
                            <span class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="username">
                            Username
                            <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <i class="fas fa-at input-icon"></i>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                class="form-control @error('username') error @enderror" 
                                value="{{ old('username', $user->username) }}"
                                placeholder="Masukkan username untuk login"
                                required
                            >
                        </div>
                        <small class="form-text">Username harus unik dan akan digunakan untuk login</small>
                        @error('username')
                            <span class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">
                            Password Baru
                        </label>
                        <div class="input-group has-toggle">
                            <i class="fas fa-lock input-icon"></i>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-control @error('password') error @enderror" 
                                placeholder="Masukkan password baru (minimal 6 karakter)"
                            >
                            <i class="fas fa-eye password-toggle" id="togglePassword" onclick="togglePasswordVisibility('password', 'togglePassword')"></i>
                        </div>
                        <small class="form-text">Kosongkan jika tidak ingin mengubah password</small>
                        @error('password')
                            <span class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="id_dusun">
                            ID Dusun
                        </label>
                        <div class="input-group">
                            <i class="fas fa-map-marker-alt input-icon"></i>
                            <input 
                                type="number" 
                                id="id_dusun" 
                                name="id_dusun" 
                                class="form-control @error('id_dusun') error @enderror" 
                                value="{{ old('id_dusun', $user->id_dusun) }}"
                                placeholder="Masukkan ID dusun (opsional)"
                            >
                        </div>
                        <small class="form-text">Opsional: ID dusun yang dikelola oleh kasun</small>
                        @error('id_dusun')
                            <span class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('kasi.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Update Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<script>
function togglePasswordVisibility(inputId, toggleId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = document.getElementById(toggleId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
@endsection
