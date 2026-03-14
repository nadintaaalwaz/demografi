<?php

namespace App\Http\Controllers;

use App\Services\PendudukImportService;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            return redirect()
                ->route('kasi.upload.form')
                ->with('success', "Data berhasil diimport! Total {$result['total_record']} record.");
        } else {
            return redirect()
                ->route('kasi.upload.form')
                ->with('import_errors', $result['errors'])
                ->withInput();
        }

    } catch (\Exception $e) {

        return redirect()
            ->route('kasi.upload.form')
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Tampilkan data penduduk
     */
    public function index()
    {
        $penduduk = Penduduk::with('dusun')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('kasi.data-penduduk', compact('penduduk'));
    }

    /**
     * Tampilkan detail penduduk
     */
    public function show($nik)
    {
        $penduduk = Penduduk::with(['dusun', 'dinamika'])
            ->findOrFail($nik);

        return view('kasi.detail-penduduk', compact('penduduk'));
    }
}
