<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Penduduk;
use Illuminate\Support\Facades\DB;

$total = Penduduk::where('status', 'Aktif')->count();
$nullDates = Penduduk::where('status', 'Aktif')->whereNull('tanggal_lahir')->count();

// Check age distribution
$ageRaw = Penduduk::where('status', 'Aktif')
    ->selectRaw('SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 0 AND 5 THEN 1 ELSE 0 END) as bayi_balita')
    ->selectRaw('SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 6 AND 11 THEN 1 ELSE 0 END) as anak')
    ->selectRaw('SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 12 AND 18 THEN 1 ELSE 0 END) as remaja')
    ->selectRaw('SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 19 AND 59 THEN 1 ELSE 0 END) as dewasa')
    ->selectRaw('SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60 THEN 1 ELSE 0 END) as lansia')
    ->first();

$sumAge = ($ageRaw->bayi_balita ?? 0) + ($ageRaw->anak ?? 0) + ($ageRaw->remaja ?? 0) + ($ageRaw->dewasa ?? 0) + ($ageRaw->lansia ?? 0);

echo "Total Penduduk Aktif: $total\n";
echo "Tanggal Lahir NULL: $nullDates\n";
echo "\nAge Distribution:\n";
echo "Bayi & Balita (0-5): " . ($ageRaw->bayi_balita ?? 0) . "\n";
echo "Anak-anak (6-11): " . ($ageRaw->anak ?? 0) . "\n";
echo "Remaja (12-18): " . ($ageRaw->remaja ?? 0) . "\n";
echo "Dewasa (19-59): " . ($ageRaw->dewasa ?? 0) . "\n";
echo "Lansia (60+): " . ($ageRaw->lansia ?? 0) . "\n";
echo "Sum Age Categories: $sumAge\n";
echo "Gap: " . ($total - $sumAge) . "\n";
