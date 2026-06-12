@extends('kasi.layout')

@section('title', 'Manajemen Wilayah')

@section('page-title')
Manajemen Wilayah
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .wilayah-container {
        display: grid;
        grid-template-columns: 1fr 450px;
        gap: 25px;
        align-items: start;
    }

    .wilayah-left {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .search-filter-section {
        padding: 20px 25px;
        background: #0C342C;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .search-box {
        flex: 1;
        position: relative;
    }

    .search-box input {
        width: 100%;
        padding: 12px 15px 12px 45px;
        border: none;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        font-size: 14px;
    }

    .search-box input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .search-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.7);
    }

    .btn-add {
        background: #E3EF26;
        color: #0C342C;
        padding: 12px 20px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-add:hover {
        background: #d4e01d;
        transform: translateY(-2px);
    }

    .filter-tabs {
        display: flex;
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }

    .wilayah-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        padding: 14px 18px;
        border-bottom: 1px solid #eef2f7;
        background: #fcfdfd;
    }

    .summary-item {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 12px;
    }

    .summary-item small {
        display: block;
        color: #6b7280;
        font-size: 11px;
        margin-bottom: 3px;
    }

    .summary-item strong {
        color: #0C342C;
        font-size: 16px;
    }

    .filter-tab {
        flex: 1;
        padding: 14px 20px;
        text-align: center;
        background: transparent;
        border: none;
        color: #6b7280;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        border-bottom: 3px solid transparent;
    }

    .filter-tab:hover {
        color: #076653;
        background: #f3f4f6;
    }

    .filter-tab.active {
        color: #076653;
        border-bottom-color: #076653;
        background: #fff;
    }

    .wilayah-table {
        width: 100%;
        border-collapse: collapse;
    }

    .wilayah-table thead {
        background: #f9fafb;
    }

    .wilayah-table th {
        padding: 14px 18px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e5e7eb;
    }

    .wilayah-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.2s ease;
    }

    .wilayah-table tbody tr:hover {
        background: #f9fafb;
    }

    .wilayah-table td {
        padding: 16px 18px;
        font-size: 13px;
        color: #374151;
    }

    .wilayah-name {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .wilayah-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .wilayah-dot.yellow { background: #E3EF26; }
    .wilayah-dot.green { background: #10b981; }
    .wilayah-dot.blue { background: #3b82f6; }

    .wilayah-name span {
        font-weight: 600;
        color: #0C342C;
    }

    .wilayah-sub {
        margin-top: 4px;
        font-size: 11px;
        color: #6b7280;
    }

    .relation-text {
        font-size: 12px;
        color: #374151;
        line-height: 1.4;
    }

    .relation-empty {
        color: #9ca3af;
        font-size: 12px;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-dusun { background: #fef3c7; color: #92400e; }
    .badge-rt { background: #d1fae5; color: #065f46; }
    .badge-rw { background: #dbeafe; color: #1e40af; }

    .action-btns {
        display: flex;
        gap: 6px;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .btn-edit { background: #fef3c7; color: #d97706; }
    .btn-edit:hover { background: #fde68a; transform: translateY(-2px); }

    .btn-delete { background: #fee2e2; color: #dc2626; }
    .btn-delete:hover { background: #fecaca; transform: translateY(-2px); }

    .map-container {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        position: sticky;
        top: 30px;
    }

    .map-header {
        padding: 18px 20px;
        background: #0C342C;
        color: #E3EF26;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .map-header h3 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    #map {
        height: 600px;
        width: 100%;
    }

    .map-hint {
        padding: 15px 20px;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
        font-size: 12px;
        color: #6b7280;
        text-align: center;
    }

    .map-legend {
        border-top: 1px solid #eef2f7;
        padding: 12px 15px;
        font-size: 12px;
        color: #4b5563;
        display: flex;
        flex-direction: column;
        gap: 8px;
        background: #fff;
    }

    .map-legend-items {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .map-legend span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        border: 2px solid #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }

    .legend-dot.dusun { background: #ff9f43; }
    .legend-dot.rw { background: #10b981; }
    .legend-dot.rt { background: #3b82f6; }

    /* Custom Beautiful Pin Markers CSS */
    .custom-marker-pin {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        box-shadow: -1px 3px 6px rgba(0,0,0,0.3);
    }

    .custom-marker-pin::after {
        content: '';
        width: 14px;
        height: 14px;
        margin: 0 0 1px 1px;
        background: #fff;
        border-radius: 50%;
        transform: rotate(45deg);
    }

    .empty-state {
        padding: 60px 25px;
        text-align: center;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .empty-state h4 {
        font-size: 16px;
        color: #6b7280;
        margin-bottom: 8px;
    }

    .empty-state p { font-size: 13px; }

    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }

    .modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        max-width: 450px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .modal-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #fee2e2;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #dc2626;
        font-size: 24px;
    }

    .modal-header h3 {
        font-size: 20px;
        color: #0C342C;
        font-weight: 700;
        margin: 0;
    }

    .modal-body {
        margin-bottom: 25px;
        color: #6b7280;
        font-size: 14px;
        line-height: 1.6;
    }

    .modal-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-cancel { background: #e5e7eb; color: #374151; }
    .btn-cancel:hover { background: #d1d5db; }

    .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; }
    .btn-danger:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }

    @media (max-width: 1200px) {
        .wilayah-container { grid-template-columns: 1fr; }
        .wilayah-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .map-container { position: static; }
    }

    @media (max-width: 768px) {
        .wilayah-summary { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<div class="wilayah-container">
    <div class="wilayah-left">
        <div class="search-filter-section">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama wilayah" onkeyup="searchWilayah()">
            </div>
            <a href="{{ route('kasi.wilayah.create') }}" class="btn-add">
                <i class="fas fa-plus"></i> Tambah Wilayah
            </a>
        </div>

        <div class="filter-tabs">
            <button class="filter-tab active" onclick="filterWilayah('all', this)">All</button>
            <button class="filter-tab" onclick="filterWilayah('dusun', this)">Dusun</button>
            <button class="filter-tab" onclick="filterWilayah('rt', this)">RT</button>
            <button class="filter-tab" onclick="filterWilayah('rw', this)">RW</button>
        </div>

        <div class="wilayah-summary">
            <div class="summary-item">
                <small>Luas Desa Sebalor (akumulasi dusun)</small>
                <strong>{{ number_format($totalLuasDusun ?? 0, 2) }} Ha</strong>
            </div>
            <div class="summary-item">
                <small>Total Dusun</small>
                <strong>{{ $wilayahCounts['dusun'] ?? 0 }}</strong>
            </div>
            <div class="summary-item">
                <small>Total RW</small>
                <strong>{{ $wilayahCounts['rw'] ?? 0 }}</strong>
            </div>
            <div class="summary-item">
                <small>Total RT</small>
                <strong>{{ $wilayahCounts['rt'] ?? 0 }}</strong>
            </div>
        </div>

        <div id="wilayahTableContainer">
            @if($wilayah->count() > 0)
                <table class="wilayah-table" id="wilayahTable">
                    <thead>
                        <tr>
                            <th>Nama Wilayah</th>
                            <th>Tipe</th>
                            <th>Relasi RT/RW</th>
                            <th>Luas (Ha)</th>
                            <th>Koordinat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wilayah as $item)
                            <tr data-tipe="{{ $item->tipe }}" data-lat="{{ $item->latitude }}" data-lng="{{ $item->longitude }}" data-nama="{{ $item->nama }}">
                                <td>
                                    <div class="wilayah-name">
                                        <div class="wilayah-dot {{ $item->tipe === 'dusun' ? 'yellow' : ($item->tipe === 'rt' ? 'blue' : 'green') }}"></div>
                                        <div>
                                            <span>{{ $item->nama }}</span>
                                            @if($item->tipe === 'rt' && $item->nomor_rt)
                                                <div class="wilayah-sub">RT {{ $item->nomor_rt }} - RW {{ $item->nomor_rw ?? '-' }}</div>
                                            @elseif($item->tipe === 'rw' && $item->nomor_rw)
                                                <div class="wilayah-sub">RW {{ $item->nomor_rw }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $item->tipe }}">{{ ucfirst($item->tipe) }}</span>
                                </td>
                                <td>
                                    @if($item->tipe === 'rt')
                                        <span class="relation-text">RT {{ $item->nomor_rt ?? '-' }} berada di RW {{ $item->nomor_rw ?? '-' }}</span>
                                    @elseif($item->tipe === 'rw')
                                        @php
                                            $rtKey = (string) ($item->id_dusun ?? '') . '-' . (string) $item->nomor_rw;
                                            $daftarRt = $rtByDusunRw[$rtKey] ?? [];
                                        @endphp

                                        @if(count($daftarRt) > 0)
                                            <span class="relation-text">
                                                RW {{ $item->nomor_rw }} memiliki RT: {{ collect($daftarRt)->map(fn ($rt) => 'RT ' . $rt)->implode(', ') }}
                                            </span>
                                        @else
                                            <span class="relation-empty">Belum ada RT di RW ini</span>
                                        @endif
                                    @elseif($item->tipe === 'dusun')
                                        @php
                                            $daftarRw = $rwByDusun[$item->id] ?? [];
                                        @endphp
                                        @if(count($daftarRw) > 0)
                                            <span class="relation-text">
                                                Dusun {{ $item->nama }} memiliki RW: {{ collect($daftarRw)->map(fn ($rw) => 'RW ' . $rw)->implode(', ') }}
                                            </span>
                                        @else
                                            <span class="relation-empty">Belum ada RW di dusun ini</span>
                                        @endif
                                    @else
                                        <span class="relation-empty">-</span>
                                    @endif
                                </td>
                                <td>{{ $item->luas_wilayah ? number_format($item->luas_wilayah, 2) . ' Ha' : '-' }}</td>
                                <td><small>{{ $item->latitude }}, {{ $item->longitude }}</small></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="{{ route('kasi.wilayah.edit', $item->id) }}" class="btn-icon btn-edit" title="Edit">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <button type="button" class="btn-icon btn-delete" onclick="confirmDelete({{ $item->id }}, '{{ $item->nama }}')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <i class="fas fa-map"></i>
                    <h4>Belum Ada Data Wilayah</h4>
                    <p>Klik "Tambah Wilayah" untuk menambahkan data</p>
                </div>
            @endif
        </div>
    </div>

    <div class="map-container">
        <div class="map-header">
            <i class="fas fa-map-marked-alt"></i>
            <h3>Peta Lokasi</h3>
        </div>
        <div id="map"></div>
        <div class="map-legend">
            <div class="map-legend-items">
                <span><i class="legend-dot dusun"></i>Dusun (Pin Oranye)</span>
                <span><i class="legend-dot rw"></i>RW (Pin Hijau)</span>
                <span><i class="legend-dot rt"></i>RT (Pin Biru)</span>
            </div>
            <div style="margin-top: 5px; display: flex; flex-direction: column; gap: 4px;">
                <span><span style="border-top: 3px dashed #dc2626; width: 25px; display: inline-block; margin-right: 5px;"></span> <strong>Garis Putus Merah:</strong> Batas Wilayah Desa Sebalor</span>
                <span><span style="background: #fef3c7; border: 1px solid #d97706; width: 25px; height: 12px; display: inline-block; margin-right: 5px;"></span> <strong>Warna Amber:</strong> Area Cakupan Polygon Dusun</span>
            </div>
        </div>
        <div class="map-hint">
            Peta menampilkan garis batas merah polygon (GeoJSON) beserta sebaran marker titik internal. Klik pada area atau pin untuk melihat info detail.
        </div>
    </div>
</div>

<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>Konfirmasi Hapus</h3>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus wilayah <strong id="deleteWilayahName"></strong>?</p>
            <p>Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-cancel" onclick="closeDeleteModal()">Batal</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // 1. Inisialisasi Objek Peta Leaflet
    const map = L.map('map').setView([-8.161, 111.758], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const wilayahData = @json($wilayah);
    const dusunSummary = @json($dusunSummary ?? []);
    const markers = [];
    const mainLayer = L.layerGroup().addTo(map);
    const dusunPendudukMap = new Map(dusunSummary.map(item => [Number(item.id), Number(item.total_penduduk || 0)]));
    const bounds = [];

    // Fungsi pembuat marker modern baru agar selaras & elegan
    function buildPinIcon(color) {
        return L.divIcon({
            className: 'custom-div-icon',
            html: `<div class="custom-marker-pin" style="background: ${color};"></div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -28],
        });
    }

    // 2. Render Markers bawaan Database dengan Pin Baru yang Selaras
    wilayahData.forEach(item => {
        if (item.latitude && item.longitude) {
            let markerColor = '';
            if (item.tipe === 'dusun') markerColor = '#ff9f43'; // Oranye Cerah agar kontras dibanding kuning kusam
            else if (item.tipe === 'rt') markerColor = '#3b82f6';    // Biru modern
            else markerColor = '#10b981';                           // Hijau emerald

            // Menggunakan Pin terstandarisasi untuk semua jenis penanda agar selaras
            const marker = L.marker([item.latitude, item.longitude], { icon: buildPinIcon(markerColor) });

            marker.addTo(mainLayer);
            bounds.push([Number(item.latitude), Number(item.longitude)]);
            
            marker.bindPopup(`
                <div style="font-family: 'Segoe UI', sans-serif; min-width: 180px;">
                    <h3 style="margin: 0 0 8px 0; color: #0C342C; font-size: 15px;">${item.nama}</h3>
                    <p style="margin: 4px 0; font-size: 12px; color: #6b7280;"><strong>Tipe:</strong> ${item.tipe.toUpperCase()}</p>
                    <p style="margin: 4px 0; font-size: 12px; color: #6b7280;"><strong>Luas:</strong> ${item.luas_wilayah ? item.luas_wilayah + ' Ha' : '-'}</p>
                    ${item.tipe === 'dusun' ? `<p style="margin: 4px 0; font-size: 12px; color: #6b7280;"><strong>Penduduk:</strong> ${dusunPendudukMap.get(Number(item.id)) || 0} jiwa</p>` : ''}
                    <p style="margin: 4px 0; font-size: 12px; color: #6b7280;"><strong>Koordinat:</strong> ${item.latitude}, ${item.longitude}</p>
                </div>
            `);

            markers.push({ marker: marker, tipe: item.tipe, nama: item.nama.toLowerCase() });
        }
    });

    // 3. Data GeoJSON Batas Wilayah Dinamis
    const geojsonData = {
      "type": "FeatureCollection",
      "features": [
        {
          "type": "Feature",
          "geometry": {
            "coordinates": [[
              [111.751378, -8.16256], [111.754639, -8.158777], [111.757861, -8.156481], 
              [111.761679, -8.156928], [111.763159, -8.1585], [111.769831, -8.161242], 
              [111.769209, -8.162751], [111.767149, -8.162878], [111.766656, -8.164812], 
              [111.765154, -8.165237], [111.764983, -8.168404], [111.759684, -8.168404], 
              [111.757861, -8.167639], [111.755093, -8.165747], [111.752755, -8.1646], 
              [111.751378, -8.16256]
            ]],
            "type": "Polygon"
          },
          "properties": { "name": "Dusun Sebalor" },
          "id": "2XUvL"
        },
        {
          "type": "Feature",
          "geometry": {
            "coordinates": [
              [111.759678, -8.168443], [111.764922, -8.168447], [111.765095, -8.165267], 
              [111.766629, -8.164799], [111.767144, -8.162876], [111.769203, -8.162802], 
              [111.769814, -8.16124], [111.763121, -8.15851], [111.761662, -8.156927], 
              [111.757859, -8.156523], [111.754561, -8.158988], [111.751321, -8.16261]
            ],
            "type": "LineString"
          },
          "properties": { "name": "Batas Dusun" },
          "id": "Dui3w"
        },
        {
          "type": "Feature",
          "geometry": {
            "coordinates": [[
              [111.759665, -8.168445], [111.763354, -8.168445], [111.764981, -8.168471], 
              [111.76511, -8.165326], [111.766655, -8.164816], [111.767127, -8.162903], 
              [111.769229, -8.162818], [111.76983, -8.161246], [111.763137, -8.158525], 
              [111.761678, -8.156953], [111.757859, -8.156528], [111.753612, -8.155083], 
              [111.752453, -8.15538], [111.748546, -8.154576], [111.74846, -8.156531], 
              [111.747259, -8.157211], [111.749708, -8.159714], [111.750223, -8.162817], 
              [111.751338, -8.162605], [111.752797, -8.164602], [111.755071, -8.16575], 
              [111.757817, -8.167663], [111.759665, -8.168445]
            ]],
            "type": "Polygon"
          },
          "properties": { "name": "Wilayah Desa Sebalor" },
          "id": "Ivo1v"
        }
      ]
    };

    // 4. Render Garis Batas Secara Dinamis & Mengubah Nama Popup Sesuai yang Diklik
    const geojsonLayer = L.geoJSON(geojsonData, {
        style: function(feature) {
            const name = feature.properties.name.toLowerCase();
            
            if (name.includes('desa sebalor')) {
                return {
                    color: "#dc2626",
                    weight: 3.5,
                    dashArray: "8, 6",
                    opacity: 0.9,
                    fillColor: "#fee2e2",
                    fillOpacity: 0.05
                };
            } 
            
            return {
                color: "#d97706",
                weight: 2,
                opacity: 0.8,
                fillColor: "#fef3c7",
                fillOpacity: 0.4
            };
        },
        onEachFeature: function (feature, layer) {
            if (feature.properties && feature.properties.name) {
                // Di sini perbaikan utama dilakukan: Isi popup mengambil secara presisi properti asli dari objek GeoJSON yang diklik
                layer.bindPopup(`
                    <div style="font-family: 'Segoe UI', sans-serif; padding: 2px;">
                        <strong style="color: #0c342c; font-size: 14px;">${feature.properties.name}</strong><br>
                        <span style="font-size: 11px; color: #6b7280;">Format: ${feature.geometry.type}</span>
                    </div>
                `);
            }
        }
    }).addTo(map);

    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [40, 40] });
    } else {
        map.fitBounds(geojsonLayer.getBounds(), { padding: [40, 40] });
    }

    // Filter wilayah function
    function filterWilayah(tipe, button) {
        const tabs = document.querySelectorAll('.filter-tab');
        tabs.forEach(tab => tab.classList.remove('active'));
        if (button) button.classList.add('active');

        const rows = document.querySelectorAll('#wilayahTable tbody tr');
        rows.forEach(row => {
            if (tipe === 'all' || row.dataset.tipe === tipe) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Search wilayah function
    function searchWilayah() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#wilayahTable tbody tr');

        rows.forEach(row => {
            const namaWilayah = row.dataset.nama.toLowerCase();
            if (namaWilayah.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        markers.forEach(item => {
            if (item.nama.includes(searchValue)) {
                map.addLayer(item.marker);
            } else {
                map.removeLayer(item.marker);
            }
        });
    }

    // Delete modal functions
    function confirmDelete(wilayahId, wilayahName) {
        const modal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const deleteWilayahName = document.getElementById('deleteWilayahName');
        
        deleteForm.action = `/kasi/wilayah/${wilayahId}`;
        deleteWilayahName.textContent = wilayahName;
        modal.classList.add('active');
    }

    // Close Modal
    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('active');
    }

    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target === modal) {
            closeDeleteModal();
        }
    }
</script>
@endpush