# TODO - Perubahan Dinamika Penduduk -> Riwayat Dinamika

## Rencana implementasi
- [x] app/Http/Controllers/DinamikaPendudukController.php
  - [x] Tambah import `use App\Models\RiwayatDinamika;`
  - [x] Method `store()`:
    - [x] Hapus validasi `jumlah_meninggal` dan `jumlah_keluar`
    - [x] Ubah validasi sesuai requirement (record_id, tahun, bulan, id_dusun, jumlah_lahir, jumlah_masuk)
    - [x] Hapus field `jumlah_meninggal` dan `jumlah_keluar` dari `$record->fill()`
    - [x] Pastikan `store()` tetap hanya menyimpan rekap Lahir/Masuk (tanpa buat riwayat untuk Lahir/Masuk karena riwayat butuh `penduduk_nik`)
  - [x] Method `index()`:
    - [x] Hapus dependensi agregasi dari `dinamika_penduduk` untuk `SUM(jumlah_meninggal)` dan `SUM(jumlah_keluar)`
    - [x] Hitung Meninggal/Keluar dari `riwayat_dinamika` berdasarkan `jenis_dinamika` dan filter tahun/bulan
    - [x] Sesuaikan perhitungan chart/grafik yang menggunakan jumlah meninggal/keluar.


- [x] Jalankan pengecekan cepat aplikasi (manual) / minimal `php artisan test`. (sementara minimal `php -l` sudah sukses)

