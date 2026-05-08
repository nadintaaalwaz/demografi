# 📋 Laporan Demografi & Dinamika Penduduk - Dokumentasi Implementasi

## ✅ Status: COMPLETE (v1.0)

Sistem laporan telah diimplementasikan dengan fitur dasar yang sesuai requirement.

---

## 📁 File yang Dibuat/Diupdate

### 1. **Controller** 
- **File:** `app/Http/Controllers/ReportController.php` (NEW)
- **Methods:**
  - `index()` - Render view dengan data default
  - `getData()` - API untuk fetch data laporan (JSON)
  - `exportExcel()` - Export ke Excel
  - `exportPdf()` - Placeholder PDF export
  - `getDemografiData()` - Aggregasi data demografi
  - `getDinamikaData()` - Aggregasi data dinamika
  - Private helpers untuk breakdown per dusun, occupation mapping, etc.

### 2. **View**
- **File:** `resources/views/kasi/reports.blade.php` (NEW)
- **Fitur:**
  - Filter: Tahun (wajib), Bulan (optional), Dusun (optional)
  - Tab toggle: Demografi | Dinamika
  - Chart.js integration (pie, bar, doughnut)
  - Breakdown table per dusun
  - Ringkasan angka (summary cards)
  - Export buttons (Excel / PDF)

### 3. **Routes**
- **File:** `routes/web.php` (UPDATED)
- **Routes Baru:**
  ```
  GET  /kasi/laporan                → kasi.laporan.index
  POST /kasi/laporan/data           → kasi.laporan.data
  POST /kasi/laporan/export-excel   → kasi.laporan.export-excel
  POST /kasi/laporan/export-pdf     → kasi.laporan.export-pdf
  ```

### 4. **Menu Sidebar**
- **File:** `resources/views/kasi/layout.blade.php`
- **Status:** Link sudah ada (di line ~515)
- ```blade
  <a href="{{ route('kasi.laporan.index') }}" class="menu-link">
    <i class="fas fa-file-alt"></i>
    <span>Pelaporan</span>
  </a>
  ```

---

## 🎯 Fitur Utama

### A. **Laporan Demografi**
Menampilkan snapshot penduduk aktif dengan breakdown:
- Ringkasan: Total penduduk, L/P, persentase
- Grafik: Gender (pie), Pendidikan (bar), Pekerjaan (bar)
- Tabel: Breakdown per dusun (Total, L/P)

### B. **Laporan Dinamika**
Menampilkan pergerakan penduduk berdasarkan waktu:
- Ringkasan: Total lahir, meninggal, masuk, keluar
- Grafik: Per bulan (bar chart multi-dataset)
- Tabel: Breakdown per dusun (Lahir, Meninggal, Masuk, Keluar)

### C. **Filter Interaktif**
- **Tahun:** Dropdown range 2022-2026 (bisa disesuaikan)
- **Bulan:** Dropdown 1-12 (optional, default semua)
- **Dusun:** Dropdown dari wilayah tipe "dusun" (optional, default semua)

### D. **Export**
- **Excel:** Judul laporan, ringkasan angka, tabel breakdown per dusun
- **PDF:** Placeholder (opsional, bisa dikembangkan dengan dompdf)

---

## 🚀 Cara Menggunakan

### 1. **Akses Laporan**
1. Login dengan role `kasi` (Kasi Pemerintahan)
2. Klik menu **Pelaporan** di sidebar
3. Halaman laporan akan loaded dengan data default (tahun sekarang)

### 2. **Filter Data**
1. Pilih **Tahun** (wajib)
2. Pilih **Bulan** (optional - kosongkan untuk "Semua Bulan")
3. Pilih **Dusun** (optional - kosongkan untuk "Semua Dusun")
4. Klik **Tampilkan Laporan**

### 3. **Toggle Tab**
- Klik tab **Demografi** untuk laporan demografi
- Klik tab **Dinamika** untuk laporan dinamika penduduk

### 4. **Export Laporan**
- Klik **📥 Download Excel** - Export tabel breakdown ke Excel
- Klik **📥 Download PDF** - (Placeholder, perlu dompdf)

---

## 📊 Data yang Ditampilkan

### Demografi
```
Ringkasan:
├── Total Penduduk Aktif
├── Total Laki-laki (%)
└── Total Perempuan (%)

Grafik:
├── Pie Chart: Gender
├── Bar Chart: Pendidikan (6+ kategori)
└── Bar Chart: Pekerjaan (9 kategori)

Tabel Breakdown per Dusun:
├── Nama Dusun
├── Total Penduduk
├── Total Laki-laki
└── Total Perempuan
```

### Dinamika
```
Ringkasan:
├── Total Kelahiran
├── Total Kematian
├── Total Masuk (Migrasi)
└── Total Keluar (Migrasi)

Grafik:
└── Stacked Bar Chart per Bulan:
    ├── Kelahiran (hijau)
    ├── Kematian (merah)
    ├── Masuk (biru)
    └── Keluar (orange)

Tabel Breakdown per Dusun:
├── Nama Dusun
├── Lahir
├── Meninggal
├── Masuk
└── Keluar
```

---

## 🔧 Database & Query

### Tabel yang Digunakan
- `penduduk` - Data penduduk (filter: `status = 'Aktif'`)
- `dinamika_penduduk` - Kelahiran, kematian, migrasi per bulan/dusun
- `wilayah` - Dusun & admin boundaries

### Query Key Features
- ✅ Filter by `status = 'Aktif'` untuk demografi
- ✅ CASE WHEN mapping untuk kategori (pendidikan, pekerjaan)
- ✅ Breakdown per dusun via LEFT JOIN + GROUP BY
- ✅ Perbulan aggregation untuk dinamika

---

## 📝 Notes & To-Do (Opsional)

### Selesai:
- ✅ API endpoints dengan filter tahun/bulan/dusun
- ✅ Chart.js visualization (pie, bar)
- ✅ Export Excel dengan tabel breakdown
- ✅ Responsive design (mobile-friendly)
- ✅ Loading state & error handling
- ✅ Tab switching Demografi/Dinamika

### Belum (optional):
- ⏳ PDF export (placeholder siap untuk dompdf/mpdf)
- ⏳ Caching hasil query untuk performa besar
- ⏳ Chart export (embed di PDF)
- ⏳ Banding data YoY (year-over-year comparison)
- ⏳ Custom date range (saat ini per bulan/tahun fixed)

---

## ✨ Quick Test Checklist

Untuk manual testing:

- [ ] Akses `/kasi/laporan` → UI loaded
- [ ] Filter Tahun 2026, Bulan Januari, All Dusun → Data muncul
- [ ] Tab Demografi → Chart & tabel demografi muncul
- [ ] Tab Dinamika → Chart & tabel dinamika muncul
- [ ] Click "Download Excel" Demografi → File `.xlsx` terdownload
- [ ] Click "Download Excel" Dinamika → File `.xlsx` terdownload
- [ ] Open Excel → Judul, ringkasan, tabel sesuai spesifikasi
- [ ] Filter different dusun → Tabel breakdown berubah
- [ ] Mobile responsive → Layout adaptif di < 768px

---

## 🎨 Styling & UX

- Modern gradient sidebar menu
- Smooth tab switching
- Loading spinner saat fetch data
- Summary cards dengan left-border color
- Breakdown table dengan hover effect
- Export button prominent (bottom of report)
- Color scheme match dashboard (dark green #076653, yellow #E3EF26)

---

## 🔐 Security

- ✅ Middleware: `['auth', 'role:kasi']` - hanya role kasi
- ✅ CSRF token di form
- ✅ Input validation (`tahun`, `bulan`, `dusun_id`, `laporan_tipe`)
- ✅ Eloquent query builder (safe from SQL injection)

---

## 📞 Support

Jika ada bug atau request fitur:
1. Check detail di `ReportController.php` - logic Query
2. Check UI di `reports.blade.php` - frontend
3. Check routes di `routes/web.php` - endpoint mapping

Selamat! Laporan Demografi & Dinamika Penduduk sudah siap digunakan. 🚀
