<?php

namespace App\Http\Controllers;

use App\Models\Penduduk;
use App\Models\DinamikaPenduduk;
use App\Models\Wilayah;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    private const MALE_VALUES = ['LAKI-LAKI', 'LAKI LAKI', 'LAKI', 'L', 'PRIA', 'MALE'];
    private const FEMALE_VALUES = ['PEREMPUAN', 'WANITA', 'P', 'FEMALE'];

    /**
     * Render laporan view dengan data default (tahun & bulan sekarang)
     */
    public function index()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $currentDusunId = null;

        // Get dusun list for filter
        $dusunList = Wilayah::where('tipe', 'dusun')
            ->orderBy('nama')
            ->pluck('nama', 'id');

        // Generate tahun list (misal 5 tahun terakhir)
        $yearList = collect(range(2022, $currentYear))->reverse();

        $initialDemografiData = $this->buildDemografiData($currentYear, $currentDusunId);

        return view('kasi.reports', [
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth,
            'yearList' => $yearList,
            'dusunList' => $dusunList,
            'initialDemografiData' => $initialDemografiData,
        ]);
    }

    /**
     * API: Get laporan data berdasarkan filter (Tahun, Bulan, Dusun)
     * POST /kasi/laporan/data
     */
    public function getData(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2020|max:2099',
            'bulan' => 'nullable|integer|min:1|max:12',
            'dusun_id' => 'nullable|integer|exists:wilayah,id',
            'laporan_tipe' => 'required|in:demografi,dinamika',
        ]);

        $tahun = $validated['tahun'];
        $bulan = $validated['bulan'] ?? null;
        $dusunId = $validated['dusun_id'] ?? null;
        $laporanTipe = $validated['laporan_tipe'];

        if ($laporanTipe === 'demografi') {
            return $this->getDemografiData($tahun, $dusunId);
        } else {
            return $this->getDinamikaData($tahun, $bulan, $dusunId);
        }
    }

    /**
     * Get data demografi (snapshot penduduk aktif)
     */
    private function getDemografiData($tahun, $dusunId = null)
    {
        return response()->json($this->buildDemografiData($tahun, $dusunId));
    }

    /**
     * Build data demografi (snapshot penduduk aktif)
     */
    private function buildDemografiData($tahun, $dusunId = null): array
    {
        // Base query: penduduk aktif
        $query = Penduduk::where('status', 'Aktif');

        // Filter dusun jika dipilih (gunakan relasi dusun yang benar)
        if ($dusunId) {
            $query->where('id_dusun', $dusunId);
        }

        $totalPenduduk = (clone $query)->count();
        $totalLakiLaki = (clone $query)
            ->whereRaw("UPPER(TRIM(COALESCE(jenis_kelamin, ''))) IN ('" . implode("','", self::MALE_VALUES) . "')")
            ->count();
        $totalPerempuan = (clone $query)
            ->whereRaw("UPPER(TRIM(COALESCE(jenis_kelamin, ''))) IN ('" . implode("','", self::FEMALE_VALUES) . "')")
            ->count();

        // Age distribution
        $ageRaw = (clone $query)
            ->selectRaw('SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 0 AND 5 THEN 1 ELSE 0 END) as usia_bayi_balita')
            ->selectRaw('SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 6 AND 11 THEN 1 ELSE 0 END) as usia_anak')
            ->selectRaw('SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 12 AND 18 THEN 1 ELSE 0 END) as usia_remaja')
            ->selectRaw('SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 19 AND 59 THEN 1 ELSE 0 END) as usia_dewasa')
            ->selectRaw('SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60 THEN 1 ELSE 0 END) as usia_lansia')
            ->first();

        // Pendidikan breakdown (ordered sesuai kebutuhan laporan)
        $educationData = $this->getOrderedEducationBreakdown($query);

        // Pekerjaan breakdown (using same mapping as dashboard)
        $occupationData = $this->getOccupationBreakdown($query);

        // Breakdown per dusun
        $dusunBreakdown = $this->getDusunDemografiBreakdown($tahun, $dusunId);

        return [
            'type' => 'demografi',
            'summary' => [
                'totalPenduduk' => $totalPenduduk,
                'totalLakiLaki' => $totalLakiLaki,
                'totalPerempuan' => $totalPerempuan,
                'persenLakiLaki' => $totalPenduduk > 0 ? round(($totalLakiLaki / $totalPenduduk) * 100, 1) : 0,
                'persenPerempuan' => $totalPenduduk > 0 ? round(($totalPerempuan / $totalPenduduk) * 100, 1) : 0,
            ],
            'ageChart' => [
                'labels' => ['Bayi & Balita (0–5)', 'Anak-anak (6–11)', 'Remaja (12–18)', 'Dewasa (19–59)', 'Lansia (60+)'],
                'data' => [
                    (int) ($ageRaw->usia_bayi_balita ?? 0),
                    (int) ($ageRaw->usia_anak ?? 0),
                    (int) ($ageRaw->usia_remaja ?? 0),
                    (int) ($ageRaw->usia_dewasa ?? 0),
                    (int) ($ageRaw->usia_lansia ?? 0),
                ],
            ],
            'educationChart' => [
                'labels' => $educationData['labels'],
                'data' => $educationData['values'],
            ],
            'occupationChart' => [
                'labels' => $occupationData['labels'],
                'data' => $occupationData['values'],
            ],
            'genderChart' => [
                'labels' => ['Laki-laki', 'Perempuan'],
                'data' => [$totalLakiLaki, $totalPerempuan],
            ],
            'dusunBreakdown' => $dusunBreakdown,
        ];
    }

    /**
     * Get data dinamika (kelahiran, kematian, migrasi)
     */
    private function getDinamikaData($tahun, $bulan = null, $dusunId = null)
    {
        return response()->json($this->buildDinamikaData($tahun, $bulan, $dusunId));
    }

    /**
     * Build data dinamika (kelahiran, kematian, migrasi)
     */
    private function buildDinamikaData($tahun, $bulan = null, $dusunId = null): array
    {
        // Base query: dinamika penduduk
        $query = DinamikaPenduduk::where('tahun', $tahun);

        if ($bulan) {
            $query->where('bulan', $bulan);
        }

        if ($dusunId) {
            $query->where('id_dusun', $dusunId);
        }

        $filteredRecordCount = (clone $query)->count();

        // Aggregate total per kategori
        $totalLahir = (clone $query)->sum('jumlah_lahir');
        $totalMeninggal = (clone $query)->sum('jumlah_meninggal');
        $totalMasuk = (clone $query)->sum('jumlah_masuk');
        $totalKeluar = (clone $query)->sum('jumlah_keluar');

        // Per bulan breakdown hanya ditampilkan jika tidak ada filter bulan dan tidak ada filter dusun
        $showPerBulanChart = !$bulan && !$dusunId;
        $perBulanData = [];
        if ($showPerBulanChart) {
            for ($m = 1; $m <= 12; $m++) {
                $bulanQuery = DinamikaPenduduk::where('tahun', $tahun)->where('bulan', $m);

                $perBulanData[] = [
                    'bulan' => $m,
                    'bulanNama' => $this->getBulanNama($m),
                    'lahir' => $bulanQuery->sum('jumlah_lahir'),
                    'meninggal' => $bulanQuery->sum('jumlah_meninggal'),
                    'masuk' => $bulanQuery->sum('jumlah_masuk'),
                    'keluar' => $bulanQuery->sum('jumlah_keluar'),
                ];
            }
        }

        // Breakdown per dusun selalu mengikuti filter yang dipilih
        $dusunBreakdown = $this->getDusunDinamikaBreakdown($tahun, $bulan, $dusunId);

        return [
            'type' => 'dinamika',
            'summary' => [
                'totalLahir' => $totalLahir,
                'totalMeninggal' => $totalMeninggal,
                'totalMasuk' => $totalMasuk,
                'totalKeluar' => $totalKeluar,
            ],
            'perBulanChart' => [
                'labels' => array_column($perBulanData, 'bulanNama'),
                'lahir' => array_column($perBulanData, 'lahir'),
                'meninggal' => array_column($perBulanData, 'meninggal'),
                'masuk' => array_column($perBulanData, 'masuk'),
                'keluar' => array_column($perBulanData, 'keluar'),
            ],
            'dusunBreakdown' => $dusunBreakdown,
            'meta' => [
                'showPerBulanChart' => $showPerBulanChart,
                'hasBulanFilter' => (bool) $bulan,
                'hasDusunFilter' => (bool) $dusunId,
                'hasData' => $filteredRecordCount > 0,
                'filteredRecordCount' => $filteredRecordCount,
            ],
        ];
    }

    /**
     * Get occupation breakdown (dari penduduk aktif)
     */
    private function getOccupationBreakdown($query = null)
    {
        if ($query === null) {
            $query = Penduduk::where('status', 'Aktif');
        }

        $labels = ['Pelajar', 'Petani', 'IRT', 'Wiraswasta', 'Guru', 'Dosen', 'PNS', 'TNI', 'POLRI'];
        $buckets = array_fill_keys($labels, 0);

        $allRecords = (clone $query)->select('pekerjaan')->get();

        foreach ($allRecords as $record) {
            $normalized = strtoupper(trim((string) $record->pekerjaan));
            $normalized = preg_replace('/\s*\/\s*/', '/', $normalized);
            $normalized = preg_replace('/\s+/', ' ', $normalized);

            if (in_array($normalized, ['PELAJAR', 'PELAJAR/MAHASISWA', 'MAHASISWA'], true)) {
                $buckets['Pelajar']++;
            } elseif (in_array($normalized, ['PETANI', 'PETANI/PETERNAK', 'PETERNAK'], true)) {
                $buckets['Petani']++;
            } elseif (in_array($normalized, ['IRT', 'IBU RUMAH TANGGA', 'IBURUMAH TANGGA', 'IBURUNAH TANGGA'], true)) {
                $buckets['IRT']++;
            } elseif (in_array($normalized, ['WIRASWASTA', 'WIRAUSAHA', 'PENGUSAHA', 'PEDAGANG'], true)) {
                $buckets['Wiraswasta']++;
            } elseif (in_array($normalized, ['GURU', 'PENDIDIK'], true)) {
                $buckets['Guru']++;
            } elseif (in_array($normalized, ['DOSEN', 'TENAGA PENGAJAR'], true)) {
                $buckets['Dosen']++;
            } elseif (str_contains($normalized, 'PEGAWAI NEGERI SIPIL') || str_contains($normalized, 'PNS') || str_contains($normalized, 'ASN') || str_contains($normalized, 'APARATUR SIPIL NEGARA') || str_contains($normalized, 'PEGAWAI NEGERI')) {
                $buckets['PNS']++;
            } elseif (str_contains($normalized, 'TENTARA NASIONAL INDONESIA') || str_contains($normalized, 'TNI') || str_contains($normalized, 'ANGGOTA TNI')) {
                $buckets['TNI']++;
            } elseif (in_array($normalized, ['POLRI', 'POLISI', 'ANGGOTA POLRI', 'KEPOLISIAN NEGARA RI'], true)) {
                $buckets['POLRI']++;
            }
        }

        $values = array_values($buckets);

        $totalMatched = array_sum($values);
        $totalActive = (clone $query)->count();
        $othersTotal = max(0, $totalActive - $totalMatched);

        if ($othersTotal > 0) {
            $labels[] = 'Lainnya';
            $values[] = $othersTotal;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /**
     * Get education breakdown dengan urutan kategori baku
     */
    private function getOrderedEducationBreakdown($query): array
    {
        // Get all active penduduk education data
        $allRecords = (clone $query)->select('pendidikan')->get();

        // Ordered labels sesuai permintaan user
        $orderedLabels = [
            'TAMAT SD/SEDERAJAT',
            'SMP',
            'SMA',
            'AKADEMI/DIPLOMA III/S. MUDA',
            'DIPLOMA IV/STRATA I',
            'STRATA II',
        ];

        // Initialize buckets
        $buckets = array_fill_keys($orderedLabels, 0);
        $otherCount = 0;

        // Process each record
        foreach ($allRecords as $record) {
            $normalized = strtoupper(trim((string) $record->pendidikan));
            // Remove extra spaces around slashes and normalize whitespace
            $normalized = preg_replace('/\s*\/\s*/', '/', $normalized);
            $normalized = preg_replace('/\s+/', ' ', $normalized);

            if (in_array($normalized, ['SD', 'SEKOLAH DASAR', 'TAMAT SD', 'TAMAT SD/SEDERAJAT', 'SD/SEDERAJAT'], true)) {
                $buckets['TAMAT SD/SEDERAJAT']++;
            } elseif (in_array($normalized, ['SMP', 'SLTP', 'SEKOLAH MENENGAH PERTAMA'], true)) {
                $buckets['SMP']++;
            } elseif (in_array($normalized, ['SMA', 'SMK', 'SLTA', 'SEKOLAH MENENGAH ATAS', 'SEKOLAH MENENGAH KEJURUAN'], true)) {
                $buckets['SMA']++;
            } elseif (in_array($normalized, ['AKADEMI/DIPLOMA III/S. MUDA', 'AKADEMI', 'DIPLOMA III', 'D3', 'D-3', 'DIII', 'DIPLOMA 3', 'S. MUDA', 'SARJANA MUDA'], true)) {
                $buckets['AKADEMI/DIPLOMA III/S. MUDA']++;
            } elseif (in_array($normalized, ['DIPLOMA IV/STRATA I', 'DIPLOMA IV', 'D4', 'D-4', 'DIV', 'DIPLOMA 4', 'STRATA I', 'STRATA 1', 'S1', 'SARJANA'], true)) {
                $buckets['DIPLOMA IV/STRATA I']++;
            } elseif (in_array($normalized, ['STRATA II', 'STRATA 2', 'S2', 'MAGISTER'], true)) {
                $buckets['STRATA II']++;
            } else {
                $otherCount++;
            }
        }

        // Build final output
        $labels = array_keys($buckets);
        $values = array_values($buckets);

        // Add LAINNYA if there are unmatched records
        if ($otherCount > 0) {
            $labels[] = 'LAINNYA';
            $values[] = $otherCount;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /**
     * Get dusun breakdown untuk demografi
     */
    private function getDusunDemografiBreakdown($tahun, $dusunId = null)
    {
        $query = Penduduk::where('status', 'Aktif')
            ->selectRaw("
                wilayah.nama as dusun,
                COUNT(*) as total,
                SUM(CASE WHEN UPPER(TRIM(COALESCE(penduduk.jenis_kelamin, ''))) IN ('LAKI-LAKI','LAKI LAKI','LAKI','L','PRIA','MALE') THEN 1 ELSE 0 END) as laki_laki,
                SUM(CASE WHEN UPPER(TRIM(COALESCE(penduduk.jenis_kelamin, ''))) IN ('PEREMPUAN','WANITA','P','FEMALE') THEN 1 ELSE 0 END) as perempuan
            ")
            ->leftJoin('wilayah', function ($join) {
                $join->on('penduduk.id_dusun', '=', 'wilayah.id')
                    ->where('wilayah.tipe', '=', DB::raw("'dusun'"));
            })
            ->groupBy('wilayah.nama');

        if ($dusunId) {
            $query->where('penduduk.id_dusun', $dusunId);
        }

        $data = $query->orderBy('dusun')->get();

        return $data->map(function ($row) {
            return [
                'dusun' => $row->dusun ?? 'Tidak Terdata',
                'total' => (int) $row->total,
                'laki_laki' => (int) $row->laki_laki,
                'perempuan' => (int) $row->perempuan,
            ];
        })->toArray();
    }

    /**
     * Get dusun breakdown untuk dinamika
     */
    private function getDusunDinamikaBreakdown($tahun, $bulan = null, $dusunId = null)
    {
        $query = DinamikaPenduduk::where('tahun', $tahun);

        if ($bulan) {
            $query->where('bulan', $bulan);
        }

        if ($dusunId) {
            $query->where('dinamika_penduduk.id_dusun', $dusunId);
        }

        $query->selectRaw("
            COALESCE(wilayah.nama, 'Tidak Terdata') as dusun,
            SUM(dinamika_penduduk.jumlah_lahir) as lahir,
            SUM(dinamika_penduduk.jumlah_meninggal) as meninggal,
            SUM(dinamika_penduduk.jumlah_masuk) as masuk,
            SUM(dinamika_penduduk.jumlah_keluar) as keluar
        ")
            ->leftJoin('wilayah', 'dinamika_penduduk.id_dusun', '=', 'wilayah.id')
            ->groupBy('wilayah.nama')
            ->orderBy('dusun');

        $data = $query->get();

        return $data->map(function ($row) {
            return [
                'dusun' => $row->dusun ?? 'Tidak Terdata',
                'lahir' => (int) $row->lahir,
                'meninggal' => (int) $row->meninggal,
                'masuk' => (int) $row->masuk,
                'keluar' => (int) $row->keluar,
            ];
        })->toArray();
    }

    /**
     * Helper: convert month number to Indonesian name
     */
    private function getBulanNama($bulan)
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $bulanNames[$bulan] ?? '';
    }

    /**
     * Export ke Excel
     * POST /kasi/laporan/export-excel
     */
    public function exportExcel(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2020|max:2099',
            'bulan' => 'nullable|integer|min:1|max:12',
            'dusun_id' => 'nullable|integer|exists:wilayah,id',
            'laporan_tipe' => 'required|in:demografi,dinamika',
        ]);

        $tahun = $validated['tahun'];
        $bulan = $validated['bulan'] ?? null;
        $dusunId = $validated['dusun_id'] ?? null;
        $laporanTipe = $validated['laporan_tipe'];

        if ($laporanTipe === 'demografi') {
            return $this->exportDemografiExcel($tahun, $dusunId);
        } else {
            return $this->exportDinamikaExcel($tahun, $bulan, $dusunId);
        }
    }

    /**
     * Export Demografi ke Excel
     */
    private function exportDemografiExcel($tahun, $dusunId = null)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheetTitle = $sheet->getTitle();

        $demografi = $this->buildDemografiData($tahun, $dusunId);

        // Title
        $sheet->setCellValue('A1', 'LAPORAN DEMOGRAFI PENDUDUK');
        $sheet->setCellValue('A2', 'Desa Sebalor - Tahun ' . $tahun);
        $sheet->getStyle('A1:P1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:P2')->getFont()->setSize(12);

        // Summary
        $sheet->setCellValue('A4', 'RINGKASAN');
        $sheet->getStyle('A4')->getFont()->setBold(true);

        $totalPenduduk = (int) ($demografi['summary']['totalPenduduk'] ?? 0);
        $totalLakiLaki = (int) ($demografi['summary']['totalLakiLaki'] ?? 0);
        $totalPerempuan = (int) ($demografi['summary']['totalPerempuan'] ?? 0);

        $sheet->setCellValue('A5', 'Total Penduduk Aktif');
        $sheet->setCellValue('B5', $totalPenduduk);
        $sheet->setCellValue('A6', 'Laki-laki');
        $sheet->setCellValue('B6', $totalLakiLaki);
        $sheet->setCellValue('A7', 'Perempuan');
        $sheet->setCellValue('B7', $totalPerempuan);

        // Data chart jenis kelamin
        $sheet->setCellValue('A9', 'DATA GRAFIK JENIS KELAMIN');
        $sheet->setCellValue('A10', 'Kategori');
        $sheet->setCellValue('B10', 'Jumlah');
        $sheet->setCellValue('A11', 'Laki-laki');
        $sheet->setCellValue('B11', $totalLakiLaki);
        $sheet->setCellValue('A12', 'Perempuan');
        $sheet->setCellValue('B12', $totalPerempuan);
        $sheet->getStyle('A9:B10')->getFont()->setBold(true);

        // Data chart pendidikan
        $sheet->setCellValue('D9', 'DATA GRAFIK TINGKAT PENDIDIKAN');
        $sheet->setCellValue('D10', 'Tingkat Pendidikan');
        $sheet->setCellValue('E10', 'Jumlah');
        $educationLabels = $demografi['educationChart']['labels'] ?? [];
        $educationValues = $demografi['educationChart']['data'] ?? [];
        $educationStart = 11;
        foreach ($educationLabels as $i => $label) {
            $row = $educationStart + $i;
            $sheet->setCellValue('D' . $row, $label);
            $sheet->setCellValue('E' . $row, (int) ($educationValues[$i] ?? 0));
        }
        $sheet->getStyle('D9:E10')->getFont()->setBold(true);

        // Data chart pekerjaan
        $sheet->setCellValue('H9', 'DATA GRAFIK TINGKAT PEKERJAAN');
        $sheet->setCellValue('H10', 'Pekerjaan');
        $sheet->setCellValue('I10', 'Jumlah');
        $occupationLabels = $demografi['occupationChart']['labels'] ?? [];
        $occupationValues = $demografi['occupationChart']['data'] ?? [];
        $occupationStart = 11;
        foreach ($occupationLabels as $i => $label) {
            $row = $occupationStart + $i;
            $sheet->setCellValue('H' . $row, $label);
            $sheet->setCellValue('I' . $row, (int) ($occupationValues[$i] ?? 0));
        }
        $sheet->getStyle('H9:I10')->getFont()->setBold(true);

        // Chart: Jenis Kelamin (Pie)
        $genderLabel = [new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', "'{$sheetTitle}'!\$B\$10", null, 1)];
        $genderCategories = [new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', "'{$sheetTitle}'!\$A\$11:\$A\$12", null, 2)];
        $genderValues = [new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', "'{$sheetTitle}'!\$B\$11:\$B\$12", null, 2)];
        $genderSeries = new \PhpOffice\PhpSpreadsheet\Chart\DataSeries(
            \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_PIECHART,
            null,
            range(0, count($genderValues) - 1),
            $genderLabel,
            $genderCategories,
            $genderValues
        );
        $genderPlot = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea(null, [$genderSeries]);
        $genderChart = new \PhpOffice\PhpSpreadsheet\Chart\Chart(
            'gender_chart',
            new \PhpOffice\PhpSpreadsheet\Chart\Title('Grafik Jenis Kelamin'),
            new \PhpOffice\PhpSpreadsheet\Chart\Legend(\PhpOffice\PhpSpreadsheet\Chart\Legend::POSITION_RIGHT, null, false),
            $genderPlot,
            true,
            0,
            null,
            null
        );
        $genderChart->setTopLeftPosition('A14');
        $genderChart->setBottomRightPosition('D28');
        $sheet->addChart($genderChart);

        // Chart: Tingkat Pendidikan (Bar)
        $educationEnd = $educationStart + max(count($educationLabels) - 1, 0);
        $educationLabel = [new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', "'{$sheetTitle}'!\$E\$10", null, 1)];
        $educationCategories = [new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', "'{$sheetTitle}'!\$D\${educationStart}:\$D\${educationEnd}", null, max(count($educationLabels), 1))];
        $educationDataValues = [new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', "'{$sheetTitle}'!\$E\${educationStart}:\$E\${educationEnd}", null, max(count($educationValues), 1))];
        $educationSeries = new \PhpOffice\PhpSpreadsheet\Chart\DataSeries(
            \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_BARCHART,
            \PhpOffice\PhpSpreadsheet\Chart\DataSeries::GROUPING_CLUSTERED,
            range(0, count($educationDataValues) - 1),
            $educationLabel,
            $educationCategories,
            $educationDataValues
        );
        $educationSeries->setPlotDirection(\PhpOffice\PhpSpreadsheet\Chart\DataSeries::DIRECTION_COL);
        $educationPlot = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea(null, [$educationSeries]);
        $educationChart = new \PhpOffice\PhpSpreadsheet\Chart\Chart(
            'education_chart',
            new \PhpOffice\PhpSpreadsheet\Chart\Title('Grafik Tingkat Pendidikan'),
            new \PhpOffice\PhpSpreadsheet\Chart\Legend(\PhpOffice\PhpSpreadsheet\Chart\Legend::POSITION_BOTTOM, null, false),
            $educationPlot,
            true,
            0,
            new \PhpOffice\PhpSpreadsheet\Chart\Title('Kategori'),
            new \PhpOffice\PhpSpreadsheet\Chart\Title('Jumlah')
        );
        $educationChart->setTopLeftPosition('E14');
        $educationChart->setBottomRightPosition('J28');
        $sheet->addChart($educationChart);

        // Chart: Tingkat Pekerjaan (Bar)
        $occupationEnd = $occupationStart + max(count($occupationLabels) - 1, 0);
        $occupationLabel = [new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', "'{$sheetTitle}'!\$I\$10", null, 1)];
        $occupationCategories = [new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', "'{$sheetTitle}'!\$H\${occupationStart}:\$H\${occupationEnd}", null, max(count($occupationLabels), 1))];
        $occupationDataValues = [new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', "'{$sheetTitle}'!\$I\${occupationStart}:\$I\${occupationEnd}", null, max(count($occupationValues), 1))];
        $occupationSeries = new \PhpOffice\PhpSpreadsheet\Chart\DataSeries(
            \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_BARCHART,
            \PhpOffice\PhpSpreadsheet\Chart\DataSeries::GROUPING_CLUSTERED,
            range(0, count($occupationDataValues) - 1),
            $occupationLabel,
            $occupationCategories,
            $occupationDataValues
        );
        $occupationSeries->setPlotDirection(\PhpOffice\PhpSpreadsheet\Chart\DataSeries::DIRECTION_COL);
        $occupationPlot = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea(null, [$occupationSeries]);
        $occupationChart = new \PhpOffice\PhpSpreadsheet\Chart\Chart(
            'occupation_chart',
            new \PhpOffice\PhpSpreadsheet\Chart\Title('Grafik Tingkat Pekerjaan'),
            new \PhpOffice\PhpSpreadsheet\Chart\Legend(\PhpOffice\PhpSpreadsheet\Chart\Legend::POSITION_BOTTOM, null, false),
            $occupationPlot,
            true,
            0,
            new \PhpOffice\PhpSpreadsheet\Chart\Title('Kategori'),
            new \PhpOffice\PhpSpreadsheet\Chart\Title('Jumlah')
        );
        $occupationChart->setTopLeftPosition('K14');
        $occupationChart->setBottomRightPosition('P28');
        $sheet->addChart($occupationChart);

        // Breakdown per Dusun
        $sheet->setCellValue('A31', 'BREAKDOWN PER DUSUN');
        $sheet->getStyle('A31')->getFont()->setBold(true);

        $dusunData = $this->getDusunDemografiBreakdown($tahun, $dusunId);

        $sheet->setCellValue('A32', 'Dusun');
        $sheet->setCellValue('B32', 'Total');
        $sheet->setCellValue('C32', 'Laki-laki');
        $sheet->setCellValue('D32', 'Perempuan');

        $sheet->getStyle('A32:D32')->getFont()->setBold(true)->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKBLUE);

        $row = 33;
        foreach ($dusunData as $data) {
            $sheet->setCellValue('A' . $row, $data['dusun']);
            $sheet->setCellValue('B' . $row, $data['total']);
            $sheet->setCellValue('C' . $row, $data['laki_laki']);
            $sheet->setCellValue('D' . $row, $data['perempuan']);
            $row++;
        }

        // Auto width
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Laporan_Demografi_' . $tahun . '_' . now()->format('YmdHis') . '.xlsx';
        $relativePath = 'laporan/' . $filename;
        $absolutePath = storage_path('app/public/' . $relativePath);

        if (!Storage::disk('public')->exists('laporan')) {
            Storage::disk('public')->makeDirectory('laporan');
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save($absolutePath);

        $this->simpanArsipLaporan('Demografi', $tahun, null, $relativePath, $filename);

        return response()->download($absolutePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Export Dinamika ke Excel
     */
    private function exportDinamikaExcel($tahun, $bulan = null, $dusunId = null)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title
        $bulanStr = $bulan ? $this->getBulanNama($bulan) . ' ' . $tahun : 'Tahun ' . $tahun;
        $sheet->setCellValue('A1', 'LAPORAN DINAMIKA PENDUDUK');
        $sheet->setCellValue('A2', 'Desa Sebalor - ' . $bulanStr);
        $sheet->getStyle('A1:E1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:E2')->getFont()->setSize(12);

        // Summary
        $sheet->setCellValue('A4', 'RINGKASAN');
        $sheet->getStyle('A4')->getFont()->setBold(true);

        $query = DinamikaPenduduk::where('tahun', $tahun);
        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        if ($dusunId) {
            $query->where('id_dusun', $dusunId);
        }

        $totalLahir = (clone $query)->sum('jumlah_lahir');
        $totalMeninggal = (clone $query)->sum('jumlah_meninggal');
        $totalMasuk = (clone $query)->sum('jumlah_masuk');
        $totalKeluar = (clone $query)->sum('jumlah_keluar');

        $sheet->setCellValue('A5', 'Total Kelahiran');
        $sheet->setCellValue('B5', $totalLahir);
        $sheet->setCellValue('A6', 'Total Kematian');
        $sheet->setCellValue('B6', $totalMeninggal);
        $sheet->setCellValue('A7', 'Total Masuk');
        $sheet->setCellValue('B7', $totalMasuk);
        $sheet->setCellValue('A8', 'Total Keluar');
        $sheet->setCellValue('B8', $totalKeluar);

        // Breakdown per Dusun
        $sheet->setCellValue('A10', 'BREAKDOWN PER DUSUN');
        $sheet->getStyle('A10')->getFont()->setBold(true);

        $dusunData = $this->getDusunDinamikaBreakdown($tahun, $bulan, $dusunId);

        $sheet->setCellValue('A11', 'Dusun');
        $sheet->setCellValue('B11', 'Lahir');
        $sheet->setCellValue('C11', 'Meninggal');
        $sheet->setCellValue('D11', 'Masuk');
        $sheet->setCellValue('E11', 'Keluar');

        $sheet->getStyle('A11:E11')->getFont()->setBold(true)->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKBLUE);

        $row = 12;
        foreach ($dusunData as $data) {
            $sheet->setCellValue('A' . $row, $data['dusun']);
            $sheet->setCellValue('B' . $row, $data['lahir']);
            $sheet->setCellValue('C' . $row, $data['meninggal']);
            $sheet->setCellValue('D' . $row, $data['masuk']);
            $sheet->setCellValue('E' . $row, $data['keluar']);
            $row++;
        }

        // Auto width
        foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Laporan_Dinamika_' . $tahun . '_' . now()->format('YmdHis') . '.xlsx';
        $relativePath = 'laporan/' . $filename;
        $absolutePath = storage_path('app/public/' . $relativePath);

        if (!Storage::disk('public')->exists('laporan')) {
            Storage::disk('public')->makeDirectory('laporan');
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($absolutePath);

        $this->simpanArsipLaporan('Dinamika', $tahun, $bulan, $relativePath, $filename);

        return response()->download($absolutePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Export ke PDF
     */
    public function exportPdf(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2020|max:2099',
            'bulan' => 'nullable|integer|min:1|max:12',
            'dusun_id' => 'nullable|integer|exists:wilayah,id',
            'laporan_tipe' => 'required|in:demografi,dinamika',
        ]);

        $tahun = (int) $validated['tahun'];
        $bulan = $validated['bulan'] ?? null;
        $dusunId = $validated['dusun_id'] ?? null;
        $laporanTipe = $validated['laporan_tipe'];

        if ($laporanTipe === 'demografi') {
            $data = $this->buildDemografiData($tahun, $dusunId);
            $judul = 'Laporan Demografi Penduduk';
            $subjudul = 'Desa Sebalor - Tahun ' . $tahun;
            $html = $this->buildDemografiPdfHtml($data, $judul, $subjudul);
            $filename = 'Laporan_Demografi_' . $tahun . '_' . now()->format('YmdHis') . '.pdf';
            $jenisLaporan = 'Demografi';
        } else {
            $data = $this->buildDinamikaData($tahun, $bulan, $dusunId);
            $judul = 'Laporan Dinamika Penduduk';
            $subjudul = $bulan
                ? 'Desa Sebalor - ' . $this->getBulanNama($bulan) . ' ' . $tahun
                : 'Desa Sebalor - Tahun ' . $tahun;
            $html = $this->buildDinamikaPdfHtml($data, $judul, $subjudul);
            $filename = 'Laporan_Dinamika_' . $tahun . '_' . now()->format('YmdHis') . '.pdf';
            $jenisLaporan = 'Dinamika';
        }

        $relativePath = 'laporan/' . $filename;
        $absolutePath = storage_path('app/public/' . $relativePath);

        if (!Storage::disk('public')->exists('laporan')) {
            Storage::disk('public')->makeDirectory('laporan');
        }

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');
        Storage::disk('public')->put($relativePath, $pdf->output());

        $this->simpanArsipLaporan($jenisLaporan, $tahun, $bulan, $relativePath, $filename);

        return response()->download($absolutePath, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function simpanArsipLaporan(string $jenisLaporan, int $tahun, ?int $bulan, string $filePath, string $namaFile): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $data = [
            'jenis_laporan' => $jenisLaporan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'file_path' => $filePath,
            'dibuat_oleh' => (int) $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('laporan_arsip', 'nama_file')) {
            $data['nama_file'] = $namaFile;
        }

        DB::table('laporan_arsip')->insert($data);
    }

    private function buildDemografiPdfHtml(array $data, string $judul, string $subjudul): string
    {
        $rowsDusun = collect($data['dusunBreakdown'] ?? [])->map(function ($row) {
            return '<tr>'
                . '<td>' . e($row['dusun']) . '</td>'
                . '<td style="text-align:right;">' . number_format((int) $row['total'], 0, ',', '.') . '</td>'
                . '<td style="text-align:right;">' . number_format((int) $row['laki_laki'], 0, ',', '.') . '</td>'
                . '<td style="text-align:right;">' . number_format((int) $row['perempuan'], 0, ',', '.') . '</td>'
                . '</tr>';
        })->implode('');

        $buildBars = function (array $labels, array $values, string $color): string {
            $max = max($values ?: [0]);
            return collect($labels)->map(function ($label, $i) use ($values, $max, $color) {
                $value = (int) ($values[$i] ?? 0);
                $percent = $max > 0 ? max(2, (int) round(($value / $max) * 100)) : 0;
                return '<div class="bar-row">'
                    . '<div class="bar-label">' . e((string) $label) . '</div>'
                    . '<div class="bar-wrap"><div class="bar-fill" style="width:' . $percent . '%;background:' . $color . ';"></div></div>'
                    . '<div class="bar-value">' . number_format($value, 0, ',', '.') . '</div>'
                    . '</div>';
            })->implode('');
        };

        $genderBars = $buildBars(
            $data['genderChart']['labels'] ?? [],
            $data['genderChart']['data'] ?? [],
            '#0ea5e9'
        );
        $educationBars = $buildBars(
            $data['educationChart']['labels'] ?? [],
            $data['educationChart']['data'] ?? [],
            '#10b981'
        );
        $occupationBars = $buildBars(
            $data['occupationChart']['labels'] ?? [],
            $data['occupationChart']['data'] ?? [],
            '#f59e0b'
        );

        return '
            <html>
            <head>
                <style>
                    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
                    h1 { font-size: 18px; margin: 0; }
                    h2 { font-size: 13px; margin: 4px 0 20px 0; font-weight: normal; color: #4b5563; }
                    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
                    th, td { border: 1px solid #d1d5db; padding: 8px; }
                    th { background: #f3f4f6; text-align: left; }
                    .right { text-align: right; }
                    .chart-title { margin-top: 16px; margin-bottom: 6px; font-size: 13px; font-weight: bold; }
                    .bar-row { display: table; width: 100%; margin-bottom: 6px; }
                    .bar-label { display: table-cell; width: 36%; font-size: 11px; vertical-align: middle; }
                    .bar-wrap { display: table-cell; width: 44%; background: #eef2f7; height: 12px; border-radius: 6px; overflow: hidden; vertical-align: middle; }
                    .bar-fill { height: 12px; border-radius: 6px; }
                    .bar-value { display: table-cell; width: 20%; text-align: right; font-size: 11px; vertical-align: middle; }
                </style>
            </head>
            <body>
                <h1>' . e($judul) . '</h1>
                <h2>' . e($subjudul) . '</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th class="right">Jumlah</th>
                            <th class="right">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Penduduk Aktif</td>
                            <td class="right">' . number_format((int) ($data['summary']['totalPenduduk'] ?? 0), 0, ',', '.') . '</td>
                            <td class="right">100%</td>
                        </tr>
                        <tr>
                            <td>Laki-laki</td>
                            <td class="right">' . number_format((int) ($data['summary']['totalLakiLaki'] ?? 0), 0, ',', '.') . '</td>
                            <td class="right">' . ($data['summary']['persenLakiLaki'] ?? 0) . '%</td>
                        </tr>
                        <tr>
                            <td>Perempuan</td>
                            <td class="right">' . number_format((int) ($data['summary']['totalPerempuan'] ?? 0), 0, ',', '.') . '</td>
                            <td class="right">' . ($data['summary']['persenPerempuan'] ?? 0) . '%</td>
                        </tr>
                    </tbody>
                </table>

                <div class="chart-title">Grafik Jenis Kelamin</div>
                <div>' . $genderBars . '</div>

                <div class="chart-title">Grafik Tingkat Pendidikan</div>
                <div>' . $educationBars . '</div>

                <div class="chart-title">Grafik Tingkat Pekerjaan</div>
                <div>' . $occupationBars . '</div>

                <table>
                    <thead>
                        <tr>
                            <th>Dusun</th>
                            <th class="right">Total</th>
                            <th class="right">Laki-laki</th>
                            <th class="right">Perempuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        ' . $rowsDusun . '
                    </tbody>
                </table>
            </body>
            </html>
        ';
    }

    private function buildDinamikaPdfHtml(array $data, string $judul, string $subjudul): string
    {
        $rowsDusun = collect($data['dusunBreakdown'] ?? [])->map(function ($row) {
            return '<tr>'
                . '<td>' . e($row['dusun']) . '</td>'
                . '<td style="text-align:right;">' . number_format((int) $row['lahir'], 0, ',', '.') . '</td>'
                . '<td style="text-align:right;">' . number_format((int) $row['meninggal'], 0, ',', '.') . '</td>'
                . '<td style="text-align:right;">' . number_format((int) $row['masuk'], 0, ',', '.') . '</td>'
                . '<td style="text-align:right;">' . number_format((int) $row['keluar'], 0, ',', '.') . '</td>'
                . '</tr>';
        })->implode('');

        return '
            <html>
            <head>
                <style>
                    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
                    h1 { font-size: 18px; margin: 0; }
                    h2 { font-size: 13px; margin: 4px 0 20px 0; font-weight: normal; color: #4b5563; }
                    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
                    th, td { border: 1px solid #d1d5db; padding: 8px; }
                    th { background: #f3f4f6; text-align: left; }
                    .right { text-align: right; }
                </style>
            </head>
            <body>
                <h1>' . e($judul) . '</h1>
                <h2>' . e($subjudul) . '</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th class="right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Kelahiran</td><td class="right">' . number_format((int) ($data['summary']['totalLahir'] ?? 0), 0, ',', '.') . '</td></tr>
                        <tr><td>Kematian</td><td class="right">' . number_format((int) ($data['summary']['totalMeninggal'] ?? 0), 0, ',', '.') . '</td></tr>
                        <tr><td>Migrasi Masuk</td><td class="right">' . number_format((int) ($data['summary']['totalMasuk'] ?? 0), 0, ',', '.') . '</td></tr>
                        <tr><td>Migrasi Keluar</td><td class="right">' . number_format((int) ($data['summary']['totalKeluar'] ?? 0), 0, ',', '.') . '</td></tr>
                    </tbody>
                </table>

                <table>
                    <thead>
                        <tr>
                            <th>Dusun</th>
                            <th class="right">Lahir</th>
                            <th class="right">Meninggal</th>
                            <th class="right">Masuk</th>
                            <th class="right">Keluar</th>
                        </tr>
                    </thead>
                    <tbody>
                        ' . $rowsDusun . '
                    </tbody>
                </table>
            </body>
            </html>
        ';
    }
}

