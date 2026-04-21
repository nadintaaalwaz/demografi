<?php

namespace App\Services;

use App\Models\Penduduk;
use App\Models\UploadLog;
use App\Imports\PendudukImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PendudukImportService
{
    public function import($file, $originalFileName)
    {
        try {
            DB::beginTransaction();

            $import = new PendudukImport();
            Excel::import($import, $file);

            if (!empty($import->errors)) {
                DB::rollBack();
                return [
                    'success' => false,
                    'errors' => $import->errors,
                ];
            }

            foreach ($import->validData as $row) {
                Penduduk::updateOrCreate(
                    ['nik' => $row['nik']],
                    $row
                );
            }

            UploadLog::create([
                'user_id' => (int) (Auth::user()?->id ?? 0),
                'nama_file' => $originalFileName,
                'total_record' => count($import->validData),
            ]);

            DB::commit();

            return [
                'success' => true,
                'total_record' => count($import->validData),
                'message' => 'Data berhasil diimport',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'errors' => [['message' => 'Error: ' . $e->getMessage()]],
            ];
        }
    }
}