# Debug Login Issue

## Test Steps:

### 1. Clear semua cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan session:clear
```

### 2. Cek credentials
- Username: `kasi`
- Password: `kasisebalor726`
✅ Password Match (sudah di-test)

### 3. Test login
1. Buka: http://127.0.0.1:8000/login
2. Login dengan credentials di atas
3. Setelah submit, cek:
   - Apakah ada redirect?
   - Apakah tetap di halaman login?
   - Apakah ada error message?

### 4. Test session
Setelah login, buka: http://127.0.0.1:8000/test-auth
- Jika logged_in: true → Session berhasil
- Jika logged_in: false → Session gagal tersimpan

### 5. Cek log
```bash
tail -f storage/logs/laravel.log
```

### Kemungkinan Masalah:

1. **Session tidak tersimpan**
   - Cek file .env: SESSION_DRIVER=database
   - Cek tabel sessions ada
   
2. **Middleware blocking**
   - Middleware 'auth' atau 'role:kasi' error
   
3. **Redirect loop**
   - showLoginForm() redirect ke dashboard
   - Middleware redirect ke login lagi

### Quick Fix:

Ubah sementara di routes/web.php, route kasi.dashboard TANPA middleware:
```php
Route::get('/kasi/dashboard-test', function () {
    return view('kasi.dashboard', [
        'totalPenduduk' => 1450,
        'totalLakiLaki' => 754,
        'totalPerempuan' => 696,
        'persenLakiLaki' => 52,
        'persenPerempuan' => 48,
        'totalBalita' => 145,
        'totalProduktif' => 980,
        'totalLansia' => 325,
    ]);
});
```

Lalu akses: http://127.0.0.1:8000/kasi/dashboard-test
- Jika berhasil tampil → Masalah di middleware/auth
- Jika error → Masalah di view
