<?php

namespace App\Services;

use App\Models\Penduduk;
use App\Models\DinamikaPenduduk;
use App\Models\AktivitasLog;
use App\Models\UploadLog;
use App\Imports\PendudukImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PendudukImportService
{
    /**
     * Proses import file Excel menggunakan Laravel Excel
     */
    public function import($filePath, $originalFileName)
    {
        try {
            DB::beginTransaction();

            // 1. Import Excel menggunakan Laravel Excel
            $import = new PendudukImport();
            Excel::import($import, $filePath);

            // 2. Cek jika ada error validasi
            if (!empty($import->errors)) {
                DB::rollBack();
                return [
                    'success' => false,
                    'errors' => $import->errors,
                ];
            }

            // 3. Ambil data lama dari database
            $dataLama = Penduduk::all()->keyBy('nik');

            // 4. Proses perbandingan data dan deteksi dinamika
            $this->prosesPerbandinganData($dataLama, $import->validData);

            // 5. Replace data penduduk dengan data baru
            Penduduk::truncate(); // Hapus semua data lama
            Penduduk::insert($import->validData); // Insert data baru

            // 6. Simpan log upload
            UploadLog::create([
                'user_id' => Auth::id(),
                'nama_file' => $originalFileName,
                'total_record' => count($import->validData),
                'uploaded_at' => now(),
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

    /**
     * Proses perbandingan data lama vs baru dan deteksi dinamika
     */
    private function prosesPerbandinganData($dataLama, $dataBaru)
    {
        $nikBaru = collect($dataBaru)->pluck('nik')->toArray();
        $nikLama = $dataLama->keys()->toArray();

        // 1. Deteksi NIK baru (Kelahiran / Migrasi Masuk)
        $nikBaruMasuk = array_diff($nikBaru, $nikLama);
        foreach ($nikBaruMasuk as $nik) {
            $penduduk = collect($dataBaru)->firstWhere('nik', $nik);
            
            // Tentukan apakah kelahiran atau migrasi masuk berdasarkan umur
            $umur = $penduduk['umur'];
            $jenisDinamika = ($umur < 1) ? 'Kelahiran' : 'Migrasi Masuk';
            
            DinamikaPenduduk::create([
                'nik' => $nik,
                'jenis_dinamika' => $jenisDinamika,
                'tanggal_peristiwa' => now()->format('Y-m-d'),
                'keterangan' => "Terdeteksi dari upload data pada " . now()->format('d-m-Y H:i'),
            ]);

            // Log aktivitas
            AktivitasLog::create([
                'user_id' => Auth::id(),
                'aksi' => 'INSERT',
                'nik' => $nik,
                'field_diubah' => 'Data Baru',
                'nilai_lama' => null,
                'nilai_baru' => $penduduk['nama_lengkap'],
                'waktu' => now(),
            ]);
        }

        // 2. Deteksi NIK hilang (Kematian / Migrasi Keluar)
        $nikHilang = array_diff($nikLama, $nikBaru);
        foreach ($nikHilang as $nik) {
            $penduduk = $dataLama[$nik];
            
            // Default ke Migrasi Keluar
            $jenisDinamika = 'Migrasi Keluar';
            
            DinamikaPenduduk::create([
                'nik' => $nik,
                'jenis_dinamika' => $jenisDinamika,
                'tanggal_peristiwa' => now()->format('Y-m-d'),
                'keterangan' => "NIK tidak ditemukan dalam upload data terbaru pada " . now()->format('d-m-Y H:i'),
            ]);

            // Log aktivitas
            AktivitasLog::create([
                'user_id' => Auth::id(),
                'aksi' => 'DELETE',
                'nik' => $nik,
                'field_diubah' => 'Data Dihapus',
                'nilai_lama' => $penduduk->nama_lengkap,
                'nilai_baru' => null,
                'waktu' => now(),
            ]);
        }

        // 3. Deteksi perubahan data pada NIK yang sama
        $nikSama = array_intersect($nikBaru, $nikLama);
        foreach ($nikSama as $nik) {
            $old = $dataLama[$nik];
            $new = collect($dataBaru)->firstWhere('nik', $nik);

            $changes = [];
            
            // Bandingkan field-field penting
            $fieldsToCheck = [
                'nama_lengkap' => 'Nama',
                'alamat' => 'Alamat',
                'id_dusun' => 'Dusun',
                'pekerjaan' => 'Pekerjaan',
                'pendidikan' => 'Pendidikan',
            ];

            foreach ($fieldsToCheck as $field => $label) {
                if ($old->$field != $new[$field]) {
                    $changes[$label] = [
                        'old' => $old->$field,
                        'new' => $new[$field],
                    ];
                }
            }

            // Jika ada perubahan, log aktivitas
            if (!empty($changes)) {
                foreach ($changes as $field => $change) {
                    AktivitasLog::create([
                        'user_id' => Auth::id(),
                        'aksi' => 'UPDATE',
                        'nik' => $nik,
                        'field_diubah' => $field,
                        'nilai_lama' => $change['old'],
                        'nilai_baru' => $change['new'],
                        'waktu' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Get errors
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
