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

    .wilayah-dot.yellow {
        background: #E3EF26;
    }

    .wilayah-dot.green {
        background: #10b981;
    }

    .wilayah-dot.blue {
        background: #3b82f6;
    }

    .wilayah-name span {
        font-weight: 600;
        color: #0C342C;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-dusun {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-rt {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-rw {
        background: #fef3c7;
        color: #92400e;
    }

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

    .btn-edit {
        background: #fef3c7;
        color: #d97706;
    }

    .btn-edit:hover {
        background: #fde68a;
        transform: translateY(-2px);
    }

    .btn-delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-delete:hover {
        background: #fecaca;
        transform: translateY(-2px);
    }

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
        padding: 10px 15px;
        font-size: 12px;
        color: #4b5563;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        background: #fff;
    }

    .map-legend span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }

    .legend-dot.dusun {
        background: #E3EF26;
    }

    .legend-dot.rt {
        background: #10b981;
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

    .empty-state p {
        font-size: 13px;
    }

    /* Delete Modal */
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

    .btn-cancel {
        background: #e5e7eb;
        color: #374151;
    }

    .btn-cancel:hover {
        background: #d1d5db;
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    @media (max-width: 1200px) {
        .wilayah-container {
            grid-template-columns: 1fr;
        }

        .wilayah-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .map-container {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .wilayah-summary {
            grid-template-columns: 1fr;
        }
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
    <!-- Left: Table Section -->
    <div class="wilayah-left">
        <!-- Search and Add Button -->
        <div class="search-filter-section">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama wilayah" onkeyup="searchWilayah()">
            </div>
            <a href="{{ route('kasi.wilayah.create') }}" class="btn-add">
                <i class="fas fa-plus"></i> Tambah Wilayah
            </a>
        </div>

        <!-- Filter Tabs -->
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

        <!-- Wilayah Table -->
        <div id="wilayahTableContainer">
            @if($wilayah->count() > 0)
                <table class="wilayah-table" id="wilayahTable">
                    <thead>
                        <tr>
                            <th>Nama Wilayah</th>
                            <th>Tipe</th>
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
                                        <div class="wilayah-dot {{ $item->tipe === 'dusun' ? 'yellow' : ($item->tipe === 'rt' ? 'green' : 'blue') }}"></div>
                                        <span>{{ $item->nama }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $item->tipe }}">{{ ucfirst($item->tipe) }}</span>
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

    <!-- Right: Map Section -->
    <div class="map-container">
        <div class="map-header">
            <i class="fas fa-map-marked-alt"></i>
            <h3>Peta Lokasi</h3>
        </div>
        <div id="map"></div>
        <div class="map-legend">
            <span><i class="legend-dot dusun"></i>Dusun</span>
            <span><i class="legend-dot rt"></i>Ukuran lingkaran = jumlah penduduk dusun</span>
        </div>
        <div class="map-hint">
            Peta menampilkan persebaran dusun. Klik marker untuk detail.
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
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
            <button type="button" class="btn btn-cancel" onclick="closeDeleteModal()">
                Batal
            </button>
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
    // Initialize map
    const map = L.map('map').setView([-7.5, 110.5], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add markers for each wilayah
    const wilayahData = @json($wilayah);
    const dusunSummary = @json($dusunSummary ?? []);
    const markers = [];
    const dusunLayer = L.layerGroup().addTo(map);

    const dusunPendudukMap = new Map(dusunSummary.map(item => [Number(item.id), Number(item.total_penduduk || 0)]));
    const bounds = [];

    wilayahData.forEach(item => {
        if (item.tipe !== 'dusun') {
            return;
        }

        if (item.latitude && item.longitude) {
            let markerColor = '';
            if (item.tipe === 'dusun') markerColor = '#E3EF26';
            else if (item.tipe === 'rt') markerColor = '#10b981';
            else markerColor = '#3b82f6';

            let marker;
            if (item.tipe === 'dusun') {
                const jumlah = dusunPendudukMap.get(Number(item.id)) || 0;
                const radius = Math.max(8, Math.min(24, 8 + Math.sqrt(jumlah)));
                marker = L.circleMarker([item.latitude, item.longitude], {
                    radius,
                    color: '#ca8a04',
                    weight: 2,
                    fillColor: markerColor,
                    fillOpacity: 0.5,
                });
            } else {
                const customIcon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="background-color: ${markerColor}; width: 20px; height: 20px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>`,
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });
                marker = L.marker([item.latitude, item.longitude], { icon: customIcon });
            }

            marker.addTo(dusunLayer);
            bounds.push([Number(item.latitude), Number(item.longitude)]);
            
            marker.bindPopup(`
                <div style="font-family: 'Segoe UI', sans-serif; min-width: 180px;">
                    <h3 style="margin: 0 0 8px 0; color: #0C342C; font-size: 15px;">${item.nama}</h3>
                    <p style="margin: 4px 0; font-size: 12px; color: #6b7280;">
                        <strong>Tipe:</strong> ${item.tipe.toUpperCase()}
                    </p>
                    <p style="margin: 4px 0; font-size: 12px; color: #6b7280;">
                        <strong>Luas:</strong> ${item.luas_wilayah ? item.luas_wilayah + ' Ha' : '-'}
                    </p>
                    ${item.tipe === 'dusun' ? `<p style="margin: 4px 0; font-size: 12px; color: #6b7280;"><strong>Penduduk:</strong> ${dusunPendudukMap.get(Number(item.id)) || 0} jiwa</p>` : ''}
                    <p style="margin: 4px 0; font-size: 12px; color: #6b7280;">
                        <strong>Koordinat:</strong> ${item.latitude}, ${item.longitude}
                    </p>
                </div>
            `);

            markers.push({ marker: marker, tipe: item.tipe, nama: item.nama.toLowerCase() });
        }
    });

    if (bounds.length === 1) {
        map.setView(bounds[0], 15);
    } else if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [20, 20] });
    }

    // Filter wilayah function
    function filterWilayah(tipe, button) {
        // Update tab active state
        const tabs = document.querySelectorAll('.filter-tab');
        tabs.forEach(tab => tab.classList.remove('active'));
        if (button) {
            button.classList.add('active');
        }

        // Filter table rows
        const rows = document.querySelectorAll('#wilayahTable tbody tr');
        rows.forEach(row => {
            if (tipe === 'all' || row.dataset.tipe === tipe) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Peta tetap fokus ke sebaran dusun saja
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

        // Filter markers
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

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('active');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target === modal) {
            closeDeleteModal();
        }
    }
</script>
@endpush
