<?php

namespace App\Http\Controllers;

use App\Services\PendudukImportService;
use App\Models\Penduduk;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class PendudukController extends Controller
{
    protected $importService;

    public function __construct(PendudukImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Tampilkan halaman upload
     */
    public function uploadForm()
    {
        return view('kasi.upload-penduduk');
    }

    /**
     * Proses upload file Excel
     */
    public function upload(Request $request)
{
    // Validasi file
    $request->validate([
        'file_excel' => 'required|file|mimes:xlsx,xls|max:10240',
    ], [
        'file_excel.required' => 'File wajib dipilih',
        'file_excel.mimes' => 'File harus berformat Excel (.xlsx, .xls)',
        'file_excel.max' => 'Ukuran file maksimal 10MB',
    ]);

    $file = $request->file('file_excel');
    $originalFileName = $file->getClientOriginalName();

    try {

        // kirim langsung file ke service
        $result = $this->importService->import($file, $originalFileName);

        if ($result['success']) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Import data penduduk berhasil',
                    'total_record' => $result['total_record'] ?? 0,
                ]);
            }

            return redirect()
                ->route('kasi.upload.form')
                ->with('success', "Data berhasil diimport! Total {$result['total_record']} record.");
        } else {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import data penduduk gagal',
                    'errors' => $result['errors'] ?? [],
                ], 422);
            }

            return redirect()
                ->route('kasi.upload.form')
                ->with('import_errors', $result['errors'])
                ->withInput();
        }

    } catch (\Exception $e) {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat import data',
                'error' => $e->getMessage(),
            ], 500);
        }

        return redirect()
            ->route('kasi.upload.form')
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Tampilkan data penduduk
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $jenisKelamin = $request->query('jenis_kelamin');
        $kategoriUsia = $request->query('kategori_usia');
        $status = $request->query('status');
        $idDusun = $request->query('id_dusun');

        $allowedPerPage = [25, 50, 100];
        $perPage = (int) $request->query('per_page', 50);
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 50;
        }

        $pendudukQuery = Penduduk::query()->with('dusun');

        if ($search !== '') {
            $pendudukQuery->where(function ($query) use ($search) {
                $query->where('nik', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nomor_kartu_keluarga', 'like', "%{$search}%")
                    ->orWhere('pekerjaan', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhereHas('dusun', function ($dusunQuery) use ($search) {
                        $dusunQuery->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        if (in_array($jenisKelamin, ['L', 'P'], true)) {
            $pendudukQuery->where('jenis_kelamin', $jenisKelamin);
        }

        if (in_array($kategoriUsia, ['Balita', 'Produktif', 'Lansia'], true)) {
            if ($kategoriUsia === 'Balita') {
                $pendudukQuery->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 5');
            } elseif ($kategoriUsia === 'Lansia') {
                $pendudukQuery->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60');
            } else {
                $pendudukQuery->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 5 AND 59');
            }
        }

        if (in_array($status, ['Aktif', 'Meninggal', 'Keluar'], true)) {
            $pendudukQuery->where('status', $status);
        }

        if (!empty($idDusun) && ctype_digit((string) $idDusun)) {
            $pendudukQuery->where('id_dusun', (int) $idDusun);
        }

        $penduduk = $pendudukQuery
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $dusunList = Wilayah::query()
            ->where('tipe', 'dusun')
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return view('kasi.data-penduduk', compact('penduduk', 'dusunList'));
    }

    /**
     * Tampilkan detail penduduk
     */
    public function show($nik)
    {
        $penduduk = Penduduk::with('dusun')
            ->findOrFail($nik);

        return view('kasi.detail-penduduk', compact('penduduk'));
    }
}
