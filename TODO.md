# TODO

- [x] Update `app/Http/Controllers/ChartDataController.php`: tambahkan payload `rw.details` dan `rt.details` berisi data numerik terstruktur dari kolom penduduk (dusun, RW, RT) supaya frontend tidak perlu parsing string label.

- [x] Update `resources/views/kasi/dashboard.blade.php`: refactor rendering RW tabs & RT detail list agar pakai `rw.details` & `rt.details` (bukan regex/parsing label). 
- [x] Samakan tampilan komponen RW tabs & RT list di dashboard kasi dengan style di `resources/views/kasun/dashboard.blade.php` (kelas hijau, aktif kuning, layout list).

- [x] Verifikasi endpoint `kasi.dashboard.chart-data` mengembalikan JSON baru dan halaman dashboard kasi tidak error.
- [x] Refactor logika RW/RT tabs & list pada dashboard kasi agar memakai `rw.details` dan `rt.details`.
- [x] Samakan style RW tabs & RT list pada dashboard kasi dengan dashboard kasun.



