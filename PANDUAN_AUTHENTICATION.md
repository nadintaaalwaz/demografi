# Panduan Setup Authentication System

## 🔐 Sistem Login & Authentication

Sistem ini menggunakan authentication berbasis **username** (bukan email) dengan 2 role:
- **kasi** - Kepala Seksi Pemerintahan (akses penuh)
- **kasun** - Kepala Dusun (akses terbatas per dusun)

---

## 📋 Langkah-Langkah Setup

### 1. Jalankan Migration

```bash
php artisan migrate
```

Migration akan membuat tabel `users` dengan struktur:
- `id` - Primary key
- `username` - Unique username untuk login
- `password` - Password terenkripsi
- `nama` - Nama lengkap user
- `role` - Enum: 'kasi' atau 'kasun'
- `id_dusun` - Foreign key ke tabel wilayah (nullable untuk kasi)
- `remember_token` - Token untuk "remember me"
- `timestamps` - created_at & updated_at

### 2. Jalankan Seeder (Optional)

```bash
php artisan db:seed --class=UserSeeder
```

Atau jalankan semua seeder:

```bash
php artisan db:seed
```

Seeder akan membuat user default:

**Kasi Pemerintahan:**
- Username: `kasi`
- Password: `password123`
- Role: kasi

**Kepala Dusun:**
- Username: `kasun1` - `kasun5`
- Password: `password123` (semua kasun)
- Role: kasun
- Terikat dengan dusun masing-masing

---

## 🔑 Cara Login

1. Buka browser: `http://localhost/demografi/login`
2. Masukkan username dan password
3. Sistem akan otomatis redirect berdasarkan role:
   - Kasi → `/kasi/dashboard`
   - Kasun → `/kasun/dashboard`

---

## 🛡️ Proteksi Route

Semua route sudah dilindungi dengan middleware:

```php
// Route Kasi (hanya bisa diakses oleh user dengan role 'kasi')
Route::prefix('kasi')->middleware(['auth', 'role:kasi'])->group(function () {
    // ...
});

// Route Kasun (hanya bisa diakses oleh user dengan role 'kasun')
Route::prefix('kasun')->middleware(['auth', 'role:kasun'])->group(function () {
    // ...
});
```

---

## 📁 File-File yang Dibuat/Diupdate

### Controllers:
- `app/Http/Controllers/AuthController.php` - Handle login, logout, dan redirect

### Middleware:
- `app/Http/Middleware/CheckRole.php` - Validasi role user

### Models:
- `app/Models/User.php` - Updated dengan username authentication

### Migration:
- `database/migrations/0001_01_01_000000_create_users_table.php` - Updated struktur tabel

### Seeders:
- `database/seeders/UserSeeder.php` - Data user untuk testing
- `database/seeders/DatabaseSeeder.php` - Updated untuk call UserSeeder

### Routes:
- `routes/web.php` - Updated dengan AuthController dan middleware

### Config:
- `bootstrap/app.php` - Registered middleware alias

---

## 🧪 Testing

### Test Login Kasi:
1. Login dengan username: `kasi`, password: `password123`
2. Harus redirect ke: `/kasi/dashboard`
3. Tidak bisa akses: `/kasun/dashboard`

### Test Login Kasun:
1. Login dengan username: `kasun1`, password: `password123`
2. Harus redirect ke: `/kasun/dashboard`
3. Tidak bisa akses: `/kasi/dashboard`

### Test Proteksi Route:
1. Logout, lalu coba akses: `/kasi/dashboard` atau `/kasun/dashboard`
2. Harus redirect ke: `/login`

---

## 🔐 Security Features

✅ Password di-hash otomatis (bcrypt)
✅ Session regeneration setelah login
✅ CSRF Protection
✅ Remember Me functionality
✅ Role-based access control
✅ Automatic logout untuk role tidak valid

---

## 📝 Membuat User Baru Manual

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'username' => 'username_baru',
    'password' => Hash::make('password_baru'),
    'nama' => 'Nama Lengkap',
    'role' => 'kasi', // atau 'kasun'
    'id_dusun' => null, // atau ID dusun untuk kasun
]);
```

---

## ⚠️ Catatan Penting

1. **Foreign Key id_dusun**: Saat ini dicomment di migration. Uncomment setelah tabel `wilayah` dibuat:
   ```php
   $table->foreign('id_dusun')->references('id')->on('wilayah')->onDelete('set null');
   ```

2. **UserSeeder id_dusun**: Sesuaikan nilai `id_dusun` di seeder dengan ID yang ada di tabel `wilayah`

3. **Production**: Ganti semua password default sebelum deploy ke production!

---

## 🚀 Next Steps

1. ✅ Migration & Seeder sudah siap
2. ⏳ Buat tabel `wilayah` untuk data dusun
3. ⏳ Tambahkan foreign key constraint di migration users
4. ⏳ Sesuaikan `id_dusun` di UserSeeder
5. ⏳ Implementasi fitur-fitur dashboard

---

**Dibuat oleh:** GitHub Copilot
**Tanggal:** 20 Februari 2026
