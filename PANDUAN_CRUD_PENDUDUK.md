# 📋 Panduan CRUD Data Penduduk

## 🎯 Tujuan
Setelah data awal diimport dari Excel, pengelolaan data penduduk dilakukan melalui menu **Data Penduduk** (tanpa perlu upload Excel berulang kali).

---

## 📍 Lokasi Menu

### Navigasi:
- **Menu Sidebar Kiri** → Klik **"Data Penduduk"** (highlight kuning)
- **URL:** `http://localhost/kasi/penduduk`

---

## ✅ FITUR-FITUR UTAMA

### 1️⃣ **TAMBAH DATA PENDUDUK (CREATE)**

#### Lokasi Tombol:
```
┌─────────────────────────────────────────────────────────────┐
│  Cari... │ [Filter] │ [🟢 TAMBAH DATA] │ [📤 Upload Excel]  │
└─────────────────────────────────────────────────────────────┘
                        ↑ KLIK SINI
```

#### Langkah-Langkah:
1. Klik tombol **"Tambah Data"** (warna hijau, ikon +)
2. Halaman form akan terbuka: `/kasi/penduduk/create`
3. Isi form:
   - **NIK** (16 digit, wajib, unik)
   - **Nomor KK** (16 digit, opsional)
   - **Nama Lengkap** (wajib)
   - **Jenis Kelamin** (L/P, wajib)
   - **Tempat Lahir** (opsional)
   - **Tanggal Lahir** (opsional)
   - **Dusun** (opsional)
4. Klik **"Simpan Data Penduduk"** (warna hijau)
5. Sistem akan kembali ke tabel dan menampilkan notifikasi sukses

**Validasi:**
- ❌ NIK tidak boleh kosong
- ❌ NIK harus 16 digit
- ❌ NIK harus unik (belum ada di database)
- ❌ Nama Lengkap tidak boleh kosong
- ❌ Jenis Kelamin harus dipilih

---

### 2️⃣ **EDIT DATA PENDUDUK (UPDATE)**

#### Lokasi Tombol:
```
┌────────────────────────────────────────────────────────┐
│ NIK │ Nama │ J.Kelamin │ ... │ AKSI                   │
├────────────────────────────────────────────────────────┤
│ ... │ ...  │ ...       │ ... │ [✏️] [🗑️] [👁️]      │
│                            ↑ KLIK PENSIL KUNING
└────────────────────────────────────────────────────────┘
```

#### Langkah-Langkah:
1. Cari baris data yang ingin diedit di tabel
2. Klik ikon **pensil kuning** (✏️) di kolom **"Aksi"**
3. Halaman form edit akan terbuka: `/kasi/penduduk/{nik}/edit`
4. Edit field yang ingin diubah:
   - ⚠️ **NIK TIDAK BISA DIUBAH** (hanya-baca, karena ID utama)
   - Bisa edit: Nomor KK, Nama, Jenis Kelamin, Tempat Lahir, Tgl Lahir, Dusun
5. Klik **"Simpan Perubahan"** (warna hijau)
6. Sistem kembali ke tabel dengan notifikasi sukses

**Catatan:**
- Field dengan tanda **`*`** (merah) wajib diisi
- NIK ditampilkan `disabled` (abu-abu) dengan note "(tidak dapat diubah)"

---

### 3️⃣ **HAPUS DATA PENDUDUK (DELETE)**

#### Lokasi Tombol:
```
┌────────────────────────────────────────────────────────┐
│ NIK │ Nama │ J.Kelamin │ ... │ AKSI                   │
├────────────────────────────────────────────────────────┤
│ ... │ ...  │ ...       │ ... │ [✏️] [🗑️] [👁️]      │
│                            ↑ KLIK TEMPAT SAMPAH MERAH
└────────────────────────────────────────────────────────┘
```

#### Langkah-Langkah:
1. Cari baris data yang ingin dihapus di tabel
2. Klik ikon **tempat sampah merah** (🗑️) di kolom **"Aksi"**
3. Muncul dialog konfirmasi: **"Apakah Anda yakin ingin menghapus data ini?"**
4. Klik **OK** untuk menghapus
5. Sistem kembali ke tabel dengan notifikasi sukses "Data berhasil dihapus"

**⚠️ Perhatian:**
- Penghapusan **TIDAK BISA DIBATALKAN** (permanent)
- Pastikan benar-benar ingin menghapus sebelum klik OK

---

### 4️⃣ **LIHAT DETAIL PENDUDUK (READ/SHOW)**

#### Lokasi Tombol:
```
┌────────────────────────────────────────────────────────┐
│ NIK │ Nama │ J.Kelamin │ ... │ AKSI                   │
├────────────────────────────────────────────────────────┤
│ ... │ ...  │ ...       │ ... │ [✏️] [🗑️] [👁️]      │
│                            ↑ KLIK MATA BIRU
└────────────────────────────────────────────────────────┘
```

#### Langkah-Langkah:
1. Klik ikon **mata biru** (👁️) di kolom **"Aksi"**
2. Halaman detail akan terbuka: `/kasi/penduduk/{nik}`
3. Lihat informasi lengkap penduduk
4. Klik **kembali** atau gunakan browser back button

---

## 🔄 ALUR LENGKAP

```
IMPORT EXCEL (AWAL)
    ↓
[OPERASI CRUD HARIAN]
    ├─ ➕ TAMBAH → Penduduk baru
    ├─ ✏️ EDIT   → Ubah data yang ada
    ├─ 🗑️ HAPUS  → Hapus data
    └─ 👁️ LIHAT  → Lihat detail
    ↓
DATABASE TERUPDATE
```

---

## 📊 VALIDASI & ERROR HANDLING

### Field Validasi:
| Field | Tipe | Wajib | Unik | Catatan |
|-------|------|-------|------|---------|
| NIK | String (16) | ✅ | ✅ | Tidak bisa diubah |
| Nomor KK | String (16) | ❌ | ❌ | - |
| Nama | String | ✅ | ❌ | Max 255 char |
| Jenis Kelamin | Select (L/P) | ✅ | ❌ | - |
| Tempat Lahir | String | ❌ | ❌ | Max 255 char |
| Tanggal Lahir | Date | ❌ | ❌ | Format YYYY-MM-DD |
| Dusun | Select | ❌ | ❌ | Dari tabel wilayah |

### Error Message:
```
❌ "NIK sudah terdaftar" → Gunakan NIK yang lain
❌ "NIK wajib diisi" → Masukkan NIK 16 digit
❌ "Nama Lengkap wajib diisi" → Isi nama
❌ "Jenis Kelamin wajib dipilih" → Pilih L atau P
```

---

## 🎨 DESAIN UI

### Warna Tombol:
- 🟢 **Hijau (#076653)** = Primary action (Tambah, Simpan)
- 🟡 **Kuning/Amber (#f59e0b)** = Edit
- 🔴 **Merah (#ef4444)** = Delete/Danger
- 🔵 **Biru (#0891b2)** = View/Info
- ⚫ **Abu-abu (#f3f4f6)** = Secondary (Cancel, Back)

### Responsive:
- ✅ Desktop: Tampilan full 2 kolom form
- ✅ Mobile: Tampilan 1 kolom (stacked)
- ✅ Tablet: Responsive grid

---

## 💡 TIPS & TRICKS

1. **Cari Cepat:** Gunakan search box untuk cari NIK atau nama
2. **Filter:** Klik tombol Filter untuk filter berdasarkan kriteria
3. **Upload Excel:** Gunakan hanya untuk import data awal (bulk import)
4. **Backup:** Gunakan fitur export/report untuk backup data
5. **Notifikasi:** Selalu perhatikan notifikasi sukses/gagal di atas form

---

## ❓ FAQ

**Q: Bisakah saya mengubah NIK?**
A: Tidak, NIK adalah primary key (identitas utama) dan tidak bisa diubah. Jika salah, hapus dan buat yang baru.

**Q: Apakah saya bisa recover data yang sudah dihapus?**
A: Tidak, penghapusan permanent. Pastikan benar sebelum delete.

**Q: Bagaimana jika lupa akses upload Excel?**
A: Klik tombol **"Upload Excel"** di toolbar Data Penduduk, atau navigasi ke **"Upload Data"** di menu sidebar.

**Q: Bisa bulk edit/delete?**
A: Saat ini belum ada (setiap perubahan per baris). Untuk bulk, gunakan fitur upload Excel.

---

**Dibuat:** 4 Juni 2026  
**Sistem:** Demografi Desa Sebalor - Kasi Pemerintahan
