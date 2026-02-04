# 🔐 Konsep Akses & Role Management Sistem SIDESA

## 📊 Ringkasan Konsep
**1 Website untuk 3 Role** dengan level akses yang berbeda:

```
┌──────────────────────────────────────────────────────────┐
│                    SIDESA WEBSITE                        │
│              (Single Application)                        │
└──────────────────────────────────────────────────────────┘
                          │
         ┌────────────────┼────────────────┐
         │                │                │
    ┌────▼────┐      ┌────▼────┐     ┌────▼────┐
    │MASYARAKAT│      │  KASI   │     │  KASUN  │
    │(Public) │      │(Admin)  │     │(Dusun)  │
    └──────────┘      └──────────┘     └──────────┘
    No Login         Need Login       Need Login
    Read Only        Full Access      Limited Access
```

---

## 👥 Detail Role & Akses

### 1. **MASYARAKAT (Public/Guest)** 
**Akses:** ❌ Tanpa Login (Read-only)

#### ✅ Halaman yang Dapat Diakses:
- **Beranda** (`/`) - Hero, statistik umum, grafik, peta
- **Profil Desa** (`/profil-desa`) - Visi, misi, sejarah
- **Statistik** (`/statistik`) - Data demografi publik
- **Peta Wilayah** (`/peta-wilayah`) - Peta sebaran dusun

#### 📋 Fitur yang Dapat Digunakan:
- ✅ Melihat statistik agregat (total penduduk, gender, usia)
- ✅ Melihat grafik demografi (Chart.js)
- ✅ Melihat peta wilayah (Leaflet.js)
- ✅ Membaca profil dan sejarah desa
- ✅ Melihat kontak desa

#### ❌ Fitur yang TIDAK Dapat Diakses:
- ❌ Data detail individu (NIK, nama lengkap, alamat)
- ❌ CRUD (Create, Read, Update, Delete) data
- ❌ Upload/Download file
- ❌ Laporan detail
- ❌ Dashboard admin

#### 🎨 Tampilan:
- Header: Logo SIDESA + Menu (Beranda, Profil Desa, Statistik, Peta) + **Button "Login Admin"**
- Desain: Hijau gelap (#0C342C, #076653) + Accent kuning (#E3EF26)
- Footer: Info kontak, jam operasional, social media

---

### 2. **KASI PEMERINTAHAN (Super Admin)**
**Akses:** ✅ Perlu Login (Full Control)

#### ✅ Halaman yang Dapat Diakses:
**Semua halaman publik +**
- **Dashboard Kasi** (`/kasi/dashboard`) - Statistik SELURUH desa
- **Data Penduduk** (`/kasi/penduduk`) - CRUD data penduduk
- **Upload Excel** (`/kasi/upload`) - Import data dari Excel
- **Wilayah** (`/kasi/wilayah`) - CRUD data dusun/RT/RW
- **Laporan** (`/kasi/laporan`) - Generate PDF/Excel
- **Manajemen User** (`/kasi/users`) - CRUD akun Kasun
- **Settings** (`/kasi/settings`) - Pengaturan sistem

#### 📋 Fitur yang Dapat Digunakan:
- ✅ **Full CRUD** untuk data penduduk
- ✅ **Upload & Validasi** file Excel (Bank KK)
- ✅ **Edit & Hapus** data penduduk manual
- ✅ **CRUD Wilayah** (tambah/edit/hapus dusun)
- ✅ **Generate Laporan** (PDF, Excel)
- ✅ **Manajemen User** (buat akun Kasun)
- ✅ **View semua statistik** (seluruh desa)
- ✅ **View grafik & peta** (seluruh dusun)
- ✅ **Log aktivitas** (audit trail)

#### 🎯 Use Case:
1. Login via `/login` dengan username & password
2. Redirect ke `/kasi/dashboard`
3. Upload Excel dengan data penduduk terbaru
4. Sistem validasi (NIK unik, format tanggal, dusun valid)
5. Jika valid → Import data → Replace data lama
6. Dashboard auto-update (statistik, grafik, peta)
7. Edit manual jika ada typo/kesalahan kecil
8. Generate laporan bulanan (PDF/Excel)
9. Logout

#### 🎨 Tampilan:
- Sidebar kiri: Logo + Menu navigasi
- Top navbar: Judul halaman + Notifikasi + User profile
- Main content: Cards, tables, charts, forms
- Warna: Sama dengan public (hijau + kuning)

---

### 3. **KASUN (Kepala Dusun)**
**Akses:** ✅ Perlu Login (Read-only untuk Dusunnya)

#### ✅ Halaman yang Dapat Diakses:
**Halaman publik +**
- **Dashboard Kasun** (`/kasun/dashboard`) - Statistik DUSUNNYA saja
- **Statistik Dusun** (`/kasun/statistik`) - Detail statistik dusun
- **Peta Dusun** (`/kasun/peta`) - Peta 1 dusun
- **Profile** (`/kasun/profile`) - Edit profile sendiri

#### 📋 Fitur yang Dapat Digunakan:
- ✅ **View statistik dusunnya** (auto-filtered by `id_dusun`)
- ✅ **View grafik** (Gender, Usia, Pendidikan, Pekerjaan - hanya dusunnya)
- ✅ **View peta** (hanya 1 marker untuk dusunnya)
- ✅ **View dinamika** (Kelahiran, Kematian, Migrasi - dusunnya)
- ✅ **Monitoring real-time** (data update otomatis setelah Kasi upload)

#### ❌ Fitur yang TIDAK Dapat Diakses:
- ❌ CRUD data penduduk (tidak bisa edit/hapus)
- ❌ Upload Excel
- ❌ View data dusun lain
- ❌ Generate laporan
- ❌ Manajemen user
- ❌ Settings sistem

#### 🎯 Use Case:
1. Login via `/login` dengan username & password
2. Redirect ke `/kasun/dashboard`
3. View statistik dusunnya (data otomatis filtered)
4. Monitoring dinamika penduduk bulan ini
5. View peta lokasi dusun
6. Tidak bisa ubah data apapun (read-only)
7. Logout

#### 🎨 Tampilan:
- Sidebar kiri: Logo + Menu (lebih sedikit dari Kasi)
- Badge: Nama Dusun ditampilkan di sidebar
- Top navbar: Nama Kasun + Dusun
- Main content: Cards statistik, grafik, peta dusun
- Info banner: "Selamat Datang, [Nama Kasun]" + "Dusun [Nama]"

---

## 🔐 Flow Authentication

### **Login Process:**
```
User buka /login
     │
     ├─ Input username & password
     │
     ├─ Submit form
     │
     ├─ Backend cek database users
     │    ├─ Username & password cocok?
     │    │   ├─ Cek kolom 'role'
     │    │   │   ├─ role = "kasi" → redirect /kasi/dashboard
     │    │   │   ├─ role = "kasun" → redirect /kasun/dashboard
     │    │   │   └─ role lainnya → error
     │    │   │
     │    │   └─ Simpan session (Auth::login())
     │    │
     │    └─ Username/password salah?
     │        └─ Tampilkan error "Username atau Password salah"
     │
     └─ Logout: Hapus session → redirect /login
```

### **Access Control (Middleware):**
```php
Route::prefix('kasi')->middleware(['auth', 'role:kasi'])->group(function() {
    // Hanya Kasi yang bisa akses
});

Route::prefix('kasun')->middleware(['auth', 'role:kasun'])->group(function() {
    // Hanya Kasun yang bisa akses
    // + Filter data by id_dusun
});

Route::get('/')->group(function() {
    // Semua bisa akses (tanpa middleware)
});
```

---

## 🗄️ Database Schema

### **Tabel: users**
```sql
id          INT PRIMARY KEY AUTO_INCREMENT
name        VARCHAR(100)
username    VARCHAR(50) UNIQUE
password    VARCHAR(255) -- hashed
role        ENUM('kasi', 'kasun')
id_dusun    INT NULL -- hanya untuk Kasun
created_at  TIMESTAMP
updated_at  TIMESTAMP
```

**Sample Data:**
```
| id | name              | username | role  | id_dusun |
|----|-------------------|----------|-------|----------|
| 1  | Budi Santoso      | kasi001  | kasi  | NULL     |
| 2  | Ahmad Fauzi       | kasun01  | kasun | 1        |
| 3  | Siti Nurjanah     | kasun02  | kasun | 2        |
| 4  | Eko Prasetyo      | kasun03  | kasun | 3        |
```

---

## 🛡️ Security Features

### 1. **Password Protection**
- Password di-hash dengan `bcrypt` (Laravel Hash::make())
- Tidak ada plain text password di database
- Password minimum 8 karakter

### 2. **Session Management**
- Session disimpan di server (Laravel Session)
- Auto logout setelah tidak aktif 120 menit
- Remember me: Session extended sampai 2 minggu

### 3. **CSRF Protection**
- Semua form POST harus include `@csrf` token
- Laravel otomatis validasi token

### 4. **Role-based Access Control (RBAC)**
```php
// Middleware: CheckRole.php
if (Auth::user()->role !== $requiredRole) {
    abort(403, 'Akses Ditolak');
}
```

### 5. **Data Filtering (Kasun)**
```php
// Otomatis filter by id_dusun
$penduduk = Penduduk::where('id_dusun', Auth::user()->id_dusun)->get();
```

### 6. **Audit Log**
```sql
-- Tabel: activity_logs
id, user_id, action, table_name, record_id, old_value, new_value, created_at
```
Semua aksi (upload, edit, delete) dicatat untuk audit trail.

---

## 📱 User Experience

### **Masyarakat (Public):**
- Buka website → Langsung lihat data
- Tidak perlu registrasi/login
- Tampilan public-friendly (hero, cards, grafik cantik)
- Button **"Login Admin"** di navbar kanan atas
- Footer lengkap dengan info kontak

### **Kasi & Kasun:**
- Klik "Login Admin" di navbar
- Input username & password
- Redirect ke dashboard masing-masing
- Sidebar tetap di kiri (fixed)
- Logout button di sidebar bawah

---

## 🎨 Design Consistency

### **Color Scheme (Semua Role):**
- Primary: `#076653` (Hijau Tua)
- Secondary: `#0C342C` (Hijau Gelap)
- Accent: `#E3EF26` (Kuning Lime)

### **Typography:**
- Font: Inter, Segoe UI (modern, professional)
- Heading: Bold (700-800)
- Body: Regular (400-500)

### **Components:**
- Cards: Shadow + Rounded corner (16px)
- Buttons: Rounded (8-10px) + Hover effect
- Tables: Striped + Hover row
- Forms: Border radius + Focus state
- Icons: Font Awesome 6.5.1

---

## ✅ Summary

| Feature | Masyarakat | Kasi | Kasun |
|---------|-----------|------|-------|
| **Login** | ❌ Tidak perlu | ✅ Perlu | ✅ Perlu |
| **View Statistik Publik** | ✅ | ✅ | ✅ |
| **View Data Detail** | ❌ | ✅ Semua | ✅ Dusunnya |
| **CRUD Data** | ❌ | ✅ | ❌ |
| **Upload Excel** | ❌ | ✅ | ❌ |
| **Generate Laporan** | ❌ | ✅ | ❌ |
| **Manajemen User** | ❌ | ✅ | ❌ |
| **View Grafik** | ✅ Agregat | ✅ Semua | ✅ Dusunnya |
| **View Peta** | ✅ Semua dusun | ✅ Semua dusun | ✅ 1 dusun |

---

## 🚀 Implementation Steps (Backend)

1. ✅ **Frontend** sudah selesai
2. **Migration** - Buat tabel users, penduduk, wilayah, dll
3. **Seeder** - Sample data untuk testing
4. **Authentication** - Laravel Breeze/Fortify
5. **Middleware** - CheckRole untuk RBAC
6. **Controller** - Logic CRUD & filtering
7. **Model** - Eloquent relationships
8. **Validation** - Request validation rules
9. **Excel Import** - Maatwebsite/Laravel-Excel
10. **PDF Export** - DomPDF untuk laporan

---

**Status:** ✅ Konsep Finalized | 📐 Arsitektur Defined | 🎨 UI/UX Complete
