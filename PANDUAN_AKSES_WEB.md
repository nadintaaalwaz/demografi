# 🌐 Panduan Akses Website Sistem Demografi Desa Sebalor

## 🚀 Cara Menjalankan

Server Laravel sudah berjalan di: **http://localhost:8000**

---

## 📑 Daftar URL yang Bisa Diakses

### 🌍 HALAMAN PUBLIK (Masyarakat)
Dapat diakses tanpa login:

| Halaman | URL |
|---------|-----|
| **Beranda** | http://localhost:8000/ |
| Profil Desa | http://localhost:8000/profil |
| Data Demografi | http://localhost:8000/demografi |
| Statistik | http://localhost:8000/statistik |
| Peta Desa | http://localhost:8000/peta |
| Kontak | http://localhost:8000/kontak |

---

### 🔐 HALAMAN LOGIN

| Halaman | URL |
|---------|-----|
| **Login** | http://localhost:8000/login |

**Tampilan ini sudah sesuai dengan gambar yang Anda kirimkan!**

---

### 👤 HALAMAN KASI PEMERINTAHAN

Akses setelah login sebagai Kasi:

| Halaman | URL |
|---------|-----|
| **Dashboard Kasi** | http://localhost:8000/kasi/dashboard |
| Data Penduduk | http://localhost:8000/kasi/penduduk |
| Upload Excel | http://localhost:8000/kasi/upload |
| Wilayah | http://localhost:8000/kasi/wilayah |
| Laporan | http://localhost:8000/kasi/laporan |
| Manajemen User | http://localhost:8000/kasi/users |
| Settings | http://localhost:8000/kasi/settings |

**Fitur di Dashboard:**
- ✅ Statistik lengkap (Total Penduduk, Gender, Usia)
- ✅ Grafik Chart.js (Pie, Bar, Doughnut)
- ✅ Peta Interaktif Leaflet.js
- ✅ Activity Log terbaru

---

### 🏘️ HALAMAN KASUN (Kepala Dusun)

Akses setelah login sebagai Kasun:

| Halaman | URL |
|---------|-----|
| **Dashboard Kasun** | http://localhost:8000/kasun/dashboard |
| Statistik Dusun | http://localhost:8000/kasun/statistik |
| Peta Dusun | http://localhost:8000/kasun/peta |
| Profile | http://localhost:8000/kasun/profile |

**Fitur di Dashboard:**
- ✅ Statistik per Dusun (auto-filtered)
- ✅ Grafik khusus Dusun
- ✅ Dinamika Penduduk (Kelahiran, Kematian, Migrasi)
- ✅ Peta lokasi Dusun

---

## 🎨 Fitur Desain yang Sudah Aktif

### Warna Tema
- **Primary**: #076653 (Hijau Tua)
- **Secondary**: #0C342C (Hijau Gelap)
- **Accent**: #E3EF26 (Kuning Lime)

### Komponen UI
- ✅ Responsive Design (Mobile, Tablet, Desktop)
- ✅ Sidebar Navigation dengan icon Font Awesome
- ✅ Cards dengan shadow & hover effect
- ✅ Buttons dengan smooth transition
- ✅ Forms modern & user-friendly
- ✅ Tables dengan search & pagination
- ✅ Alert messages (success/error)

### Library External
- ✅ **Chart.js 4.4.0** - Untuk grafik statistik
- ✅ **Leaflet.js 1.9.4** - Untuk peta interaktif
- ✅ **Font Awesome 6.5.1** - Untuk icons

---

## 📂 Struktur File Views

```
resources/views/
├── kasi/
│   ├── layout.blade.php          # Layout utama Kasi
│   ├── login.blade.php            # Halaman login ⭐
│   ├── dashboard.blade.php        # Dashboard Kasi
│   ├── upload.blade.php           # Upload Excel
│   └── penduduk/
│       └── index.blade.php        # Tabel data penduduk
│
├── kasun/
│   ├── layout.blade.php           # Layout utama Kasun
│   └── dashboard.blade.php        # Dashboard Kasun
│
└── masyarakat/
    ├── layout.blade.php           # Layout publik
    └── index.blade.php            # Landing page
```

---

## 🧪 Testing Frontend

### 1. Halaman Publik
Buka browser → http://localhost:8000/

**Yang Terlihat:**
- Hero section dengan gradient hijau
- Statistik penduduk (cards)
- Grafik interaktif (Gender, Usia, Pendidikan, Pekerjaan)
- Peta persebaran dusun dengan markers
- Footer lengkap

### 2. Halaman Login
Buka browser → http://localhost:8000/login

**Yang Terlihat:**
- Form login dengan desain sesuai gambar
- Icon user di atas
- Field Username & Password
- Toggle show/hide password
- Checkbox "Ingat Saya"
- Link "Lupa Password"
- Button "Masuk" dengan warna kuning (#E3EF26)

### 3. Dashboard Kasi
Buka browser → http://localhost:8000/kasi/dashboard

**Yang Terlihat:**
- Sidebar hijau dengan menu lengkap
- Top navbar dengan user profile
- 6 stat cards (Total Penduduk, Laki-laki, Perempuan, Balita, Produktif, Lansia)
- 4 grafik (Gender, Usia, Pendidikan, Pekerjaan)
- Peta dengan 3 marker dusun
- Activity log terbaru

### 4. Data Penduduk
Buka browser → http://localhost:8000/kasi/penduduk

**Yang Terlihat:**
- Toolbar dengan search box & filter
- Button Tambah Data & Upload Excel
- Tabel data penduduk (5 sample data)
- Badge gender (biru untuk L, pink untuk P)
- Action buttons (Edit & Hapus)
- Pagination

### 5. Upload Excel
Buka browser → http://localhost:8000/kasi/upload

**Yang Terlihat:**
- Info card dengan petunjuk upload
- Drag & drop area untuk upload file
- Progress bar (simulasi)
- Button download template
- File info setelah pilih file

### 6. Dashboard Kasun
Buka browser → http://localhost:8000/kasun/dashboard

**Yang Terlihat:**
- Info banner dengan nama Kasun & Dusun
- 6 stat cards khusus dusun
- 4 grafik khusus dusun
- Peta dengan 1 marker dusun
- Dinamika penduduk (Kelahiran, Kematian, Migrasi)

---

## ⚡ Fitur Interaktif yang Berfungsi

### ✅ Yang Sudah Berjalan (Frontend Only):
1. **Search** - Di halaman data penduduk
2. **Hover Effects** - Semua cards & buttons
3. **Toggle Password** - Di halaman login
4. **Mobile Menu** - Hamburger menu di publik
5. **Drag & Drop Upload** - Di halaman upload
6. **Chart Hover** - Tooltip di semua grafik
7. **Map Popup** - Klik marker untuk detail dusun
8. **Responsive Layout** - Otomatis adjust di mobile

### ⏳ Yang Perlu Backend (Nanti):
1. Authentication (Login/Logout)
2. CRUD Database (Create, Read, Update, Delete)
3. Upload & Import Excel
4. Generate Report PDF
5. Role-based Access Control
6. Real data dari database

---

## 🔧 Troubleshooting

### Error "View not found"
**Solusi:** Pastikan semua file blade sudah tersimpan dengan benar di folder `resources/views/`

### Grafik tidak muncul
**Solusi:** Pastikan koneksi internet aktif (Chart.js & Leaflet.js load dari CDN)

### Styling berantakan
**Solusi:** Clear browser cache (Ctrl + Shift + R) atau gunakan Incognito mode

### Server tidak jalan
**Solusi:** Jalankan ulang: `php artisan serve` di terminal

---

## 📸 Screenshot Testing

Coba buka URL berikut untuk testing visual:

1. **Login Page** → http://localhost:8000/login ⭐⭐⭐
2. **Public Home** → http://localhost:8000/
3. **Kasi Dashboard** → http://localhost:8000/kasi/dashboard
4. **Data Penduduk** → http://localhost:8000/kasi/penduduk
5. **Upload Excel** → http://localhost:8000/kasi/upload
6. **Kasun Dashboard** → http://localhost:8000/kasun/dashboard

---

## 🎯 Next Steps

Setelah frontend OK, langkah selanjutnya:

1. ✅ **Database Migration** - Buat tabel (users, penduduk, wilayah, dll)
2. ✅ **Models** - Eloquent models untuk setiap tabel
3. ✅ **Controllers** - Logic CRUD & business process
4. ✅ **Authentication** - Laravel Breeze/Fortify
5. ✅ **Middleware** - Role-based access control
6. ✅ **Excel Import** - Maatwebsite/Laravel-Excel
7. ✅ **PDF Export** - DomPDF untuk laporan

---

## 💡 Tips

- Gunakan **Ctrl + Shift + I** (Developer Tools) untuk inspect element
- Test di berbagai ukuran browser (Desktop, Tablet, Mobile)
- Cek console browser untuk JavaScript errors
- Semua warna sudah sesuai tema (#076653, #0C342C, #E3EF26)

---

**Status:** ✅ Frontend 100% Complete | ⏳ Backend 0% (Belum dimulai)

**Dibuat tanggal:** 4 Februari 2026
