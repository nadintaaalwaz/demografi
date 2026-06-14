<?php

namespace App\Http\Controllers;

use App\Services\PendudukImportService;
use App\Models\Penduduk;
use App\Models\Wilayah;
use App\Models\RiwayatDinamika;
use App\Exports\PendudukTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
     * Download template Excel with required headings
     */
    public function downloadTemplate()
    {
        $filename = 'template-penduduk.xlsx';
        return Excel::download(new PendudukTemplateExport(), $filename);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $dusunList = Wilayah::where('tipe', 'dusun')->orderBy('nama')->get();
        return view('kasi.penduduk.create', compact('dusunList'));
    }

    /**
     * Store new penduduk
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nik' => 'required|digits:16|unique:penduduk,nik',
            'nomor_kartu_keluarga' => 'required|digits:16',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'required|date',
            'status_keluarga' => 'nullable|in:Kepala Keluarga,Istri,Anak',
            'status_perkawinan' => 'nullable|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'pendidikan' => 'nullable|string|max:255',
            'pekerjaan' => 'nullable|string|max:255',
            'alamat' => 'required|string',
            'id_dusun' => 'required|integer|exists:wilayah,id',
            'rw' => 'nullable|string|max:10',
            'rt' => 'nullable|string|max:10',
            'status' => 'required|in:Aktif,Meninggal,Keluar',
            'tanggal_status' => 'nullable|date',
        ]);

        // Insert penduduk hanya sekali (hindari duplicate NIK saat dijalankan)
        Penduduk::create($data);

        return redirect()

            ->route('kasi.penduduk.index')
            ->with('success', 'Penduduk berhasil ditambahkan');
    }

    /**
     * Edit form
     */
    public function edit($nik)
    {
        $penduduk = Penduduk::findOrFail($nik);
        $dusunList = Wilayah::where('tipe', 'dusun')->orderBy('nama')->get();
        return view('kasi.penduduk.edit', compact('penduduk', 'dusunList'));
    }

    /**
     * Update penduduk
     */
    public function update(Request $request, $nik)
    {
        $penduduk = Penduduk::findOrFail($nik);

        // simpan status lama
        $statusLama = $penduduk->status;

        $data = $request->validate([
            'nomor_kartu_keluarga' => 'required|digits:16',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'required|date',
            'status_keluarga' => 'nullable|in:Kepala Keluarga,Istri,Anak',
            'status_perkawinan' => 'nullable|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'pendidikan' => 'nullable|string|max:255',
            'pekerjaan' => 'nullable|string|max:255',
            'alamat' => 'required|string',
            'id_dusun' => 'required|integer|exists:wilayah,id',
            'rw' => 'nullable|string|max:10',
            'rt' => 'nullable|string|max:10',
            'status' => 'required|in:Aktif,Meninggal,Keluar',
            'tanggal_status' => 'nullable|date',
        ]);

        $penduduk->update($data);

        /*
        |--------------------------------------------------------------------------
        | Otomatis simpan riwayat meninggal / keluar
        |--------------------------------------------------------------------------
        */
        if (
            $statusLama !== $data['status']
            && in_array($data['status'], ['Meninggal', 'Keluar'])
        ) {

            $sudahAda = RiwayatDinamika::where(
                'penduduk_nik',
                $penduduk->nik
            )
            ->where(
                'jenis_dinamika',
                $data['status']
            )
            ->exists();

            if (!$sudahAda) {

                RiwayatDinamika::create([
                    'penduduk_nik' => $penduduk->nik,
                    'jenis_dinamika' => $data['status'],
                    'tanggal_dinamika' => $data['tanggal_status']
                        ?? now()->toDateString(),
                    'keterangan' => 'Otomatis dari perubahan status penduduk',
                ]);
            }
        }

        return redirect()
            ->route('kasi.penduduk.index')
            ->with('success', 'Data penduduk berhasil diperbarui');
    }

    /**
     * Delete penduduk
     */
    public function destroy($nik)
    {
        $penduduk = Penduduk::findOrFail($nik);
        $penduduk->delete();

        return redirect()->route('kasi.penduduk.index')->with('success', 'Data penduduk berhasil dihapus');
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

        // treat as success when at least some records were imported
        if (($result['success'] ?? false) || (!empty($result['total_record']) && $result['total_record'] > 0)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Import data penduduk berhasil',
                    'total_record' => $result['total_record'] ?? 0,
                ]);
            }

            $redirect = redirect()->route('kasi.upload.form');
            $msg = "Data berhasil diimport! Total " . ($result['total_record'] ?? 0) . " record.";
            if (!empty($result['errors'])) {
                $redirect->with('import_errors', $result['errors']);
                $msg .= ' (Beberapa baris diabaikan karena error)';
            }

            return $redirect->with('success', $msg);
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
                ->with('import_errors', $result['errors'] ?? [])
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
        $kategoriUsia = strtolower((string) $request->query('kategori_usia', ''));
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

        // FILTER: Kategori Usia (values: balita, anak, remaja, dewasa, lansia)
        if (in_array($kategoriUsia, ['balita', 'anak', 'remaja', 'dewasa', 'lansia'], true)) {
            if ($kategoriUsia === 'balita') {
                $pendudukQuery->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 0 AND 5');
            } elseif ($kategoriUsia === 'anak') {
                $pendudukQuery->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 6 AND 11');
            } elseif ($kategoriUsia === 'remaja') {
                // sesuai permintaan: 10-19 tahun
                $pendudukQuery->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 10 AND 19');
            } elseif ($kategoriUsia === 'dewasa') {
                // sesuai permintaan: 19-59 tahun
                $pendudukQuery->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 19 AND 59');
            } elseif ($kategoriUsia === 'lansia') {
                $pendudukQuery->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60');
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
