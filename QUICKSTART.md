# 🚀 Quick Start - Authentication System

## ⚡ Langkah Cepat

### 1. Jalankan Migration
```bash
php artisan migrate
```

### 2. Jalankan Seeder (Buat Data User Testing)
```bash
php artisan db:seed
```

### 3. Test Login
Buka browser: `http://localhost/demografi/login`

**Login sebagai Kasi:**
- Username: `kasi`
- Password: `password123`
- Redirect ke: `/kasi/dashboard`

**Login sebagai Kasun:**
- Username: `kasun1` (atau kasun2-kasun5)
- Password: `password123`
- Redirect ke: `/kasun/dashboard`

---

## ✅ Yang Sudah Dibuat

### 1. Migration
- ✅ `database/migrations/0001_01_01_000000_create_users_table.php`
  - Field: id, username, password, nama, role, id_dusun, timestamps

### 2. Model
- ✅ `app/Models/User.php`
  - Authentication menggunakan username
  - Methods: isKasi(), isKasun()
  - Relationship dengan tabel wilayah

### 3. Controller
- ✅ `app/Http/Controllers/AuthController.php`
  - showLoginForm() - Tampil form login
  - login() - Process login dengan validasi
  - logout() - Logout dan clear session
  - redirectToDashboard() - Redirect sesuai role

### 4. Middleware
- ✅ `app/Http/Middleware/CheckRole.php`
  - Proteksi route berdasarkan role
  - Registered di `bootstrap/app.php`

### 5. Routes
- ✅ `routes/web.php`
  - Route login/logout menggunakan AuthController
  - Route kasi dengan middleware: `['auth', 'role:kasi']`
  - Route kasun dengan middleware: `['auth', 'role:kasun']`

### 6. Seeder
- ✅ `database/seeders/UserSeeder.php`
  - 1 user kasi
  - 5 user kasun (kasun1-kasun5)

### 7. Dokumentasi
- ✅ `PANDUAN_AUTHENTICATION.md` - Panduan lengkap
- ✅ `database/sql/testing_users.sql` - SQL commands untuk testing

---

## 🔐 Default Credentials

| Username | Password | Role | Dusun |
|----------|----------|------|-------|
| kasi | password123 | kasi | - |
| kasun1 | password123 | kasun | Dusun Mawar |
| kasun2 | password123 | kasun | Dusun Melati |
| kasun3 | password123 | kasun | Dusun Anggrek |
| kasun4 | password123 | kasun | Dusun Kenanga |
| kasun5 | password123 | kasun | Dusun Dahlia |

---

## 🛡️ Security Features

- ✅ Password hashing (bcrypt)
- ✅ Session regeneration
- ✅ CSRF protection
- ✅ Remember me functionality
- ✅ Role-based access control
- ✅ Middleware protection

---

## 📊 Flow Diagram

```
User Login
    ↓
Input Username & Password
    ↓
AuthController::login()
    ↓
Validate Credentials
    ↓
Auth::attempt()
    ↓
[Success] → Check Role
    ↓
    ├─→ Role = 'kasi' → /kasi/dashboard
    └─→ Role = 'kasun' → /kasun/dashboard

[Failed] → Redirect back with error
```

---

## 🔍 Troubleshooting

### Error: "Base table or view not found: 'users'"
**Solusi:** Jalankan migration
```bash
php artisan migrate
```

### Error: "Class 'UserSeeder' not found"
**Solusi:** Composer autoload
```bash
composer dump-autoload
```

### Login gagal terus
**Solusi:** Pastikan seeder sudah dijalankan atau buat user manual:
```bash
php artisan tinker
```
```php
User::create([
    'username' => 'kasi',
    'password' => Hash::make('password123'),
    'nama' => 'Kasi Test',
    'role' => 'kasi',
    'id_dusun' => null
]);
```

### Redirect loop / Error 419
**Solusi:** Clear cache dan config
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## 📝 Next Steps

1. ✅ Setup authentication - **SELESAI**
2. ⏳ Buat tabel wilayah untuk data dusun
3. ⏳ Tambahkan foreign key id_dusun di migration users
4. ⏳ Implementasi CRUD data penduduk
5. ⏳ Implementasi dashboard statistik
6. ⏳ Implementasi upload Excel
7. ⏳ Implementasi peta dengan Leaflet

---

## 🎉 Selamat!

Sistem authentication sudah siap digunakan. Jalankan migration dan seeder, lalu test login!

**Created by:** GitHub Copilot  
**Date:** 20 Februari 2026
