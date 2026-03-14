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
    private array $errors = [];

    public function import($file, $originalFileName)
    {
        try {

            DB::beginTransaction();

            // 1. Import Excel
            $import = new PendudukImport();
            Excel::import($import, $file);

            if (!empty($import->errors)) {
                DB::rollBack();
                return [
                    'success' => false,
                    'errors' => $import->errors,
                ];
            }

            // 2. Ambil data lama
            $dataLama = Penduduk::all()->keyBy('nik');

            // 3. Simpan / update data penduduk baru
            foreach ($import->validData as $row) {

                Penduduk::updateOrCreate(
                    ['nik' => $row['nik']],
                    $row
                );
            }

            // 4. Proses dinamika SETELAH penduduk ada
            $this->prosesPerbandinganData($dataLama, $import->validData);

            // 5. Simpan log upload
            UploadLog::create([
                'user_id' => Auth::user()->id,
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
     * Deteksi dinamika penduduk
     */
    private function prosesPerbandinganData($dataLama, $dataBaru)
    {

        $nikBaru = collect($dataBaru)->pluck('nik')->toArray();
        $nikLama = $dataLama->keys()->toArray();

        // 1. NIK baru
        $nikBaruMasuk = array_diff($nikBaru, $nikLama);

        foreach ($nikBaruMasuk as $nik) {

            $penduduk = collect($dataBaru)->firstWhere('nik', $nik);

            $umur = $penduduk['umur'];

            $jenisDinamika = ($umur < 1) ? 'Kelahiran' : 'Migrasi Masuk';

            DinamikaPenduduk::create([
                'nik' => $nik,
                'jenis_dinamika' => $jenisDinamika,
                'tanggal_peristiwa' => now(),
                'keterangan' => "Terdeteksi dari upload data pada " . now()->format('d-m-Y H:i'),
            ]);

            AktivitasLog::create([
                'user_id' => Auth::user()->id,
                'aksi' => 'INSERT',
                'nik' => $nik,
                'field_diubah' => 'Data Baru',
                'nilai_lama' => null,
                'nilai_baru' => $penduduk['nama_lengkap'],
                'waktu' => now(),
            ]);
        }

        // 2. NIK hilang
        $nikHilang = array_diff($nikLama, $nikBaru);

        foreach ($nikHilang as $nik) {

            $penduduk = $dataLama[$nik];

            DinamikaPenduduk::create([
                'nik' => $nik,
                'jenis_dinamika' => 'Migrasi Keluar',
                'tanggal_peristiwa' => now(),
                'keterangan' => "NIK tidak ditemukan dalam upload data terbaru",
            ]);

            AktivitasLog::create([
                'user_id' => Auth::user()->id,
                'aksi' => 'DELETE',
                'nik' => $nik,
                'field_diubah' => 'Data Dihapus',
                'nilai_lama' => $penduduk->nama_lengkap,
                'nilai_baru' => null,
                'waktu' => now(),
            ]);
        }

        // 3. Perubahan data
        $nikSama = array_intersect($nikBaru, $nikLama);

        foreach ($nikSama as $nik) {

            $old = $dataLama[$nik];
            $new = collect($dataBaru)->firstWhere('nik', $nik);

            $fieldsToCheck = [
                'nama_lengkap',
                'alamat',
                'id_dusun',
                'pekerjaan',
                'pendidikan',
            ];

            foreach ($fieldsToCheck as $field) {

                if ($old->$field != $new[$field]) {

                    AktivitasLog::create([
                        'user_id' => Auth::user()->id,
                        'aksi' => 'UPDATE',
                        'nik' => $nik,
                        'field_diubah' => $field,
                        'nilai_lama' => $old->$field,
                        'nilai_baru' => $new[$field],
                        'waktu' => now(),
                    ]);
                }
            }
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}