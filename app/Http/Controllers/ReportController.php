<?php

namespace App\Http\Controllers;

use App\Models\Penduduk;
use App\Models\DinamikaPenduduk;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
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
            'dusun_id' => 'nullable|integer',
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

        // Filter dusun jika dipilih
        if ($dusunId) {
            $query->whereHas('wilayah', function ($q) use ($dusunId) {
                $q->where('id', $dusunId)->orWhereHas('parent', function ($p) use ($dusunId) {
                    $p->where('id', $dusunId);
                });
            });
        }

        $totalPenduduk = (clone $query)->count();
        $totalLakiLaki = (clone $query)->where('jenis_kelamin', 'Laki-laki')->count();
        $totalPerempuan = (clone $query)->where('jenis_kelamin', 'Perempuan')->count();

        // Pendidikan breakdown
        $educationData = (clone $query)
            ->selectRaw("COALESCE(pendidikan, 'Tidak Terdata') as kategori, COUNT(*) as total")
            ->groupBy('pendidikan')
            ->orderByDesc('total')
            ->pluck('total', 'kategori')
            ->toArray();

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
            'educationChart' => [
                'labels' => array_keys($educationData),
                'data' => array_values($educationData),
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

        // Aggregate total per kategori
        $totalLahir = (clone $query)->sum('jumlah_lahir');
        $totalMeninggal = (clone $query)->sum('jumlah_meninggal');
        $totalMasuk = (clone $query)->sum('jumlah_masuk');
        $totalKeluar = (clone $query)->sum('jumlah_keluar');

        // Per bulan breakdown (hanya untuk laporan tahunan tanpa bulan terpilih)
        $perBulanData = [];
        if (!$bulan) {
            for ($m = 1; $m <= 12; $m++) {
                $bulanQuery = DinamikaPenduduk::where('tahun', $tahun)->where('bulan', $m);
                if ($dusunId) {
                    $bulanQuery->where('id_dusun', $dusunId);
                }

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

        // Breakdown per dusun (jika tidak dipilih dusun spesifik)
        $dusunBreakdown = [];
        if (!$dusunId) {
            $dusunBreakdown = $this->getDusunDinamikaBreakdown($tahun, $bulan);
        }

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

        $occupationRaw = (clone $query)
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('PELAJAR','PELAJAR/MAHASISWA','MAHASISWA') THEN 1 ELSE 0 END) as pelajar")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('PETANI','PETANI/PETERNAK','PETERNAK') THEN 1 ELSE 0 END) as petani")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('IRT','IBU RUMAH TANGGA','IBURUNAH TANGGA') THEN 1 ELSE 0 END) as irt")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('WIRASWASTA','WIRAUSAHA','PENGUSAHA','PEDAGANG') THEN 1 ELSE 0 END) as wiraswasta")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('GURU','PENDIDIK') THEN 1 ELSE 0 END) as guru")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('DOSEN','TENAGA PENGAJAR') THEN 1 ELSE 0 END) as dosen")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('PNS','PEGAWAI NEGERI SIPIL','PEGAWAI NEGERI','ASN','APARATUR SIPIL NEGARA') THEN 1 ELSE 0 END) as pns")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('TNI','TENTARA NASIONAL INDONESIA','ANGGOTA TNI') THEN 1 ELSE 0 END) as tni")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pekerjaan, ''))) IN ('POLRI','POLISI','ANGGOTA POLRI','KEPOLISIAN NEGARA RI') THEN 1 ELSE 0 END) as polri")
            ->first();

        $labels = ['Pelajar', 'Petani', 'IRT', 'Wiraswasta', 'Guru', 'Dosen', 'PNS', 'TNI', 'POLRI'];
        $values = [
            (int) ($occupationRaw->pelajar ?? 0),
            (int) ($occupationRaw->petani ?? 0),
            (int) ($occupationRaw->irt ?? 0),
            (int) ($occupationRaw->wiraswasta ?? 0),
            (int) ($occupationRaw->guru ?? 0),
            (int) ($occupationRaw->dosen ?? 0),
            (int) ($occupationRaw->pns ?? 0),
            (int) ($occupationRaw->tni ?? 0),
            (int) ($occupationRaw->polri ?? 0),
        ];

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
     * Get dusun breakdown untuk demografi
     */
    private function getDusunDemografiBreakdown($tahun, $dusunId = null)
    {
        $query = Penduduk::where('status', 'Aktif')
            ->selectRaw("
                wilayah.nama as dusun,
                COUNT(*) as total,
                SUM(CASE WHEN penduduk.jenis_kelamin = 'Laki-laki' THEN 1 ELSE 0 END) as laki_laki,
                SUM(CASE WHEN penduduk.jenis_kelamin = 'Perempuan' THEN 1 ELSE 0 END) as perempuan
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
    private function getDusunDinamikaBreakdown($tahun, $bulan = null)
    {
        $query = DinamikaPenduduk::where('tahun', $tahun);

        if ($bulan) {
            $query->where('bulan', $bulan);
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
            'dusun_id' => 'nullable|integer',
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

        // Title
        $sheet->setCellValue('A1', 'LAPORAN DEMOGRAFI PENDUDUK');
        $sheet->setCellValue('A2', 'Desa Sebalor - Tahun ' . $tahun);
        $sheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:D2')->getFont()->setSize(12);

        // Summary
        $sheet->setCellValue('A4', 'RINGKASAN');
        $sheet->getStyle('A4')->getFont()->setBold(true);

        $query = Penduduk::where('status', 'Aktif');
        if ($dusunId) {
            $query->whereHas('wilayah', function ($q) use ($dusunId) {
                $q->where('id', $dusunId)->orWhereHas('parent', function ($p) use ($dusunId) {
                    $p->where('id', $dusunId);
                });
            });
        }

        $totalPenduduk = (clone $query)->count();
        $totalLakiLaki = (clone $query)->where('jenis_kelamin', 'Laki-laki')->count();
        $totalPerempuan = (clone $query)->where('jenis_kelamin', 'Perempuan')->count();

        $sheet->setCellValue('A5', 'Total Penduduk Aktif');
        $sheet->setCellValue('B5', $totalPenduduk);
        $sheet->setCellValue('A6', 'Laki-laki');
        $sheet->setCellValue('B6', $totalLakiLaki);
        $sheet->setCellValue('A7', 'Perempuan');
        $sheet->setCellValue('B7', $totalPerempuan);

        // Breakdown per Dusun
        $sheet->setCellValue('A9', 'BREAKDOWN PER DUSUN');
        $sheet->getStyle('A9')->getFont()->setBold(true);

        $dusunData = $this->getDusunDemografiBreakdown($tahun, $dusunId);

        $sheet->setCellValue('A10', 'Dusun');
        $sheet->setCellValue('B10', 'Total');
        $sheet->setCellValue('C10', 'Laki-laki');
        $sheet->setCellValue('D10', 'Perempuan');

        $sheet->getStyle('A10:D10')->getFont()->setBold(true)->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKBLUE);

        $row = 11;
        foreach ($dusunData as $data) {
            $sheet->setCellValue('A' . $row, $data['dusun']);
            $sheet->setCellValue('B' . $row, $data['total']);
            $sheet->setCellValue('C' . $row, $data['laki_laki']);
            $sheet->setCellValue('D' . $row, $data['perempuan']);
            $row++;
        }

        // Auto width
        foreach (['A', 'B', 'C', 'D'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Laporan_Demografi_' . $tahun . '_' . now()->format('YmdHis') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
        exit;
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

        $dusunData = $this->getDusunDinamikaBreakdown($tahun, $bulan);

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
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export ke PDF (optional - basic implementation)
     */
    public function exportPdf(Request $request)
    {
        // Placeholder - bisa dikembangkan dengan dompdf atau mpdf
        return response()->json(['message' => 'PDF export belum tersedia'], 501);
    }
}

