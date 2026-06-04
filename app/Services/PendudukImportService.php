<?php

namespace App\Services;

use App\Models\Penduduk;
use App\Models\UploadLog;
use App\Imports\PendudukImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PendudukImportService
{
    public function import($file, $originalFileName)
    {
        try {
            // Step 1: Baca dan validasi file Excel terlebih dahulu
            $import = new PendudukImport();
            Excel::import($import, $file);

            // Step 2: Jika ada error pada beberapa baris, kita tidak langsung gagal.
            // Import semua baris valid, dan kembalikan daftar error untuk ditampilkan.
            $validCount = count($import->validData ?? []);

            if ($validCount > 0) {
                Penduduk::truncate();
                foreach ($import->validData as $row) {
                    Penduduk::create($row);
                }
            }

            UploadLog::create([
                'user_id' => (int) (Auth::user()?->id ?? 0),
                'nama_file' => $originalFileName,
                'total_record' => $validCount,
            ]);

            $result = [
                'success' => $validCount > 0,
                'total_record' => $validCount,
                'message' => $validCount > 0 ? 'Data berhasil diimport' : 'Tidak ada record valid untuk diimport',
            ];

            if (!empty($import->errors)) {
                $result['errors'] = $import->errors;
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Import error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                'success' => false,
                'errors' => [['message' => 'Error: ' . $e->getMessage()]],
            ];
        }
    }
}
