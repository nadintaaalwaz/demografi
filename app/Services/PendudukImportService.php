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

            // Jika ada error validasi, return langsung
            if (!empty($import->errors)) {
                Log::info('Validation errors found during import', ['errors_count' => count($import->errors)]);
                return [
                    'success' => false,
                    'errors' => $import->errors,
                ];
            }

            // Step 2: Jika validasi lolos, lakukan replace data
            // Hapus semua data penduduk lama dan insert yang baru
            Penduduk::truncate();
            
            foreach ($import->validData as $row) {
                Penduduk::create($row);
            }

            UploadLog::create([
                'user_id' => (int) (Auth::user()?->id ?? 0),
                'nama_file' => $originalFileName,
                'total_record' => count($import->validData),
            ]);

            return [
                'success' => true,
                'total_record' => count($import->validData),
                'message' => 'Data berhasil diimport',
            ];
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
