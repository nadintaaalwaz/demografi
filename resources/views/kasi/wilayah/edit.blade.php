@extends('kasi.layout')

@section('title', 'Edit Wilayah')

@section('page-title')
Edit Data Wilayah
@endsection

@push('styles')
<style>
    .form-container {
        max-width: 900px;
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

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        margin-bottom: 25px;
    }

    .form-row.single {
        grid-template-columns: 1fr;
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

    .radio-group {
        display: flex;
        gap: 20px;
        margin-top: 10px;
    }

    .radio-option {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .radio-option input[type="radio"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .radio-option label {
        margin: 0;
        cursor: pointer;
        font-weight: 500;
    }

    .conditional-field {
        display: none;
    }

    .conditional-field.show {
        display: block;
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

    .info-box-content p {
        font-size: 13px;
        color: #1e40af;
        margin: 0;
        line-height: 1.5;
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

    #mapPreview {
        height: 300px;
        border-radius: 12px;
        margin-top: 10px;
        border: 2px solid #e5e7eb;
    }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="form-card">
        <div class="form-header">
            <h2>
                <i class="fas fa-edit"></i>
                Edit Data Wilayah
            </h2>
            <p>Perbarui informasi wilayah: {{ $wilayah->nama }}</p>
        </div>

        <form action="{{ route('kasi.wilayah.update', $wilayah->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-body">
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <div class="info-box-content">
                        <p><strong>Catatan:</strong> Koordinat (Latitude & Longitude) diperlukan untuk menampilkan wilayah di peta. Anda bisa klik pada peta di bawah untuk memperbarui koordinat.</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nama">
                            Nama Wilayah
                            <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <i class="fas fa-map-marker-alt input-icon"></i>
                            <input 
                                type="text" 
                                id="nama" 
                                name="nama" 
                                class="form-control @error('nama') error @enderror" 
                                value="{{ old('nama', $wilayah->nama) }}"
                                placeholder="Contoh: Dusun Krajan, RT 01, RW 02"
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
                        <label>
                            Tipe Wilayah
                            <span class="required">*</span>
                        </label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="tipe_dusun" name="tipe" value="dusun" {{ old('tipe', $wilayah->tipe) === 'dusun' ? 'checked' : '' }} required onchange="toggleConditionalFields()">
                                <label for="tipe_dusun">Dusun</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="tipe_rt" name="tipe" value="rt" {{ old('tipe', $wilayah->tipe) === 'rt' ? 'checked' : '' }} onchange="toggleConditionalFields()">
                                <label for="tipe_rt">RT</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="tipe_rw" name="tipe" value="rw" {{ old('tipe', $wilayah->tipe) === 'rw' ? 'checked' : '' }} onchange="toggleConditionalFields()">
                                <label for="tipe_rw">RW</label>
                            </div>
                        </div>
                        @error('tipe')
                            <span class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group conditional-field" id="field_nomor_rt">
                        <label for="nomor_rt">
                            Nomor RT
                        </label>
                        <div class="input-group">
                            <i class="fas fa-hashtag input-icon"></i>
                            <input 
                                type="number" 
                                id="nomor_rt" 
                                name="nomor_rt" 
                                class="form-control @error('nomor_rt') error @enderror" 
                                value="{{ old('nomor_rt', $wilayah->nomor_rt) }}"
                                placeholder="Contoh: 1, 2, 3"
                                min="1"
                            >
                        </div>
                        <small class="form-text">Hanya diisi jika tipe wilayah adalah RT</small>
                        @error('nomor_rt')
                            <span class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="form-group conditional-field" id="field_nomor_rw">
                        <label for="nomor_rw">
                            Nomor RW
                        </label>
                        <div class="input-group">
                            <i class="fas fa-hashtag input-icon"></i>
                            <input 
                                type="number" 
                                id="nomor_rw" 
                                name="nomor_rw" 
                                class="form-control @error('nomor_rw') error @enderror" 
                                value="{{ old('nomor_rw', $wilayah->nomor_rw) }}"
                                placeholder="Contoh: 1, 2, 3"
                                min="1"
                            >
                        </div>
                        <small class="form-text">Hanya diisi jika tipe wilayah adalah RW</small>
                        @error('nomor_rw')
                            <span class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-row single">
                    <div class="form-group">
                        <label for="luas_wilayah">
                            Luas Wilayah (km²)
                        </label>
                        <div class="input-group">
                            <i class="fas fa-ruler-combined input-icon"></i>
                            <input 
                                type="number" 
                                id="luas_wilayah" 
                                name="luas_wilayah" 
                                class="form-control @error('luas_wilayah') error @enderror" 
                                value="{{ old('luas_wilayah', $wilayah->luas_wilayah) }}"
                                placeholder="Contoh: 2.5"
                                step="0.01"
                                min="0.01"
                            >
                        </div>
                        <small class="form-text">Opsional: Luas wilayah akan digunakan untuk menghitung kepadatan penduduk</small>
                        @error('luas_wilayah')
                            <span class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="latitude">
                            Latitude
                            <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <i class="fas fa-map-pin input-icon"></i>
                            <input 
                                type="text" 
                                id="latitude" 
                                name="latitude" 
                                class="form-control @error('latitude') error @enderror" 
                                value="{{ old('latitude', $wilayah->latitude) }}"
                                placeholder="Contoh: -7.5012345"
                                required
                                readonly
                            >
                        </div>
                        <small class="form-text">Klik pada peta di bawah untuk memperbarui koordinat</small>
                        @error('latitude')
                            <span class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="longitude">
                            Longitude
                            <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <i class="fas fa-map-pin input-icon"></i>
                            <input 
                                type="text" 
                                id="longitude" 
                                name="longitude" 
                                class="form-control @error('longitude') error @enderror" 
                                value="{{ old('longitude', $wilayah->longitude) }}"
                                placeholder="Contoh: 110.5012345"
                                required
                                readonly
                            >
                        </div>
                        <small class="form-text">Klik pada peta di bawah untuk memperbarui koordinat</small>
                        @error('longitude')
                            <span class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Pilih Lokasi di Peta</label>
                    <div id="mapPreview"></div>
                    <small class="form-text">Klik pada peta untuk memperbarui lokasi wilayah</small>
                </div>

                <div class="form-actions">
                    <a href="{{ route('kasi.wilayah.index') }}" class="btn btn-secondary">
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

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
    function toggleConditionalFields() {
        const tipeRadios = document.getElementsByName('tipe');
        const fieldNomorRt = document.getElementById('field_nomor_rt');
        const fieldNomorRw = document.getElementById('field_nomor_rw');
        
        let selectedTipe = '';
        for (const radio of tipeRadios) {
            if (radio.checked) {
                selectedTipe = radio.value;
                break;
            }
        }
        
        // Show/hide fields based on selection
        if (selectedTipe === 'rt') {
            fieldNomorRt.classList.add('show');
            fieldNomorRw.classList.remove('show');
        } else if (selectedTipe === 'rw') {
            fieldNomorRw.classList.add('show');
            fieldNomorRt.classList.remove('show');
        } else {
            fieldNomorRt.classList.remove('show');
            fieldNomorRw.classList.remove('show');
        }
    }

    // Get existing coordinates
    const existingLat = {{ $wilayah->latitude ?? -7.50 }};
    const existingLng = {{ $wilayah->longitude ?? 110.50 }};

    // Initialize map
    const map = L.map('mapPreview').setView([existingLat, existingLng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add existing marker
    let marker = L.marker([existingLat, existingLng]).addTo(map);
    marker.bindPopup(`<b>Lokasi Saat Ini</b><br>Lat: ${existingLat}<br>Lng: ${existingLng}`).openPopup();

    // Handle map click
    map.on('click', function(e) {
        const lat = e.latlng.lat.toFixed(7);
        const lng = e.latlng.lng.toFixed(7);
        
        // Update input fields
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        
        // Remove existing marker
        if (marker) {
            map.removeLayer(marker);
        }
        
        // Add new marker
        marker = L.marker([lat, lng]).addTo(map);
        marker.bindPopup(`<b>Lokasi Baru</b><br>Lat: ${lat}<br>Lng: ${lng}`).openPopup();
    });

    // Call on page load to show correct fields
    document.addEventListener('DOMContentLoaded', function() {
        toggleConditionalFields();
    });
</script>
@endsection
