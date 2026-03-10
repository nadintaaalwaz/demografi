<?php

namespace App\Imports;

use App\Models\Penduduk;
use App\Models\Wilayah;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class PendudukImport implements ToCollection, WithHeadingRow
{
    public $errors = [];
    public $validData = [];
    private $dusunMap = [];
    private $rowNumber = 1; // Start from 1 (header di row 1)

    public function __construct()
    {
        // Load mapping nama dusun ke ID
        $this->loadDusunMap();
    }

    /**
     * Load mapping nama dusun ke ID
     */
    private function loadDusunMap()
    {
        $wilayah = Wilayah::all();
        foreach ($wilayah as $w) {
            $key = strtolower(trim($w->nama_dusun));
            $this->dusunMap[$key] = $w->id;
        }
    }

    /**
     * Bersihkan numeric string dari Excel (handle scientific notation & leading zeros)
     */
    private function cleanNumericString($value)
    {
        if (empty($value)) {
            return null;
        }

        // Jika numeric (Excel kadang convert ke scientific notation)
        if (is_numeric($value)) {
            // Convert ke string dan hapus decimal
            $cleaned = number_format($value, 0, '', '');
            // Pad dengan 0 di depan jika kurang dari 16 digit
            return str_pad($cleaned, 16, '0', STR_PAD_LEFT);
        }

        // Jika string, bersihkan spasi dan karakter non-numeric
        $cleaned = preg_replace('/[^0-9]/', '', trim($value));
        
        // Pad dengan 0 jika kurang dari 16 digit
        if (strlen($cleaned) < 16 && strlen($cleaned) > 0) {
            return str_pad($cleaned, 16, '0', STR_PAD_LEFT);
        }

        return $cleaned;
    }

    /**
     * Process collection dari Excel
     */
    public function collection(Collection $rows)
    {
        $nikList = [];

        foreach ($rows as $row) {
            $this->rowNumber++;
            $rowErrors = [];

            // Konversi heading ke nama kolom yang diharapkan
            $data = [
                'NIK' => $this->cleanNumericString($row['nik'] ?? null),
                'Nama Lengkap' => $row['nama_lengkap'] ?? null,
                'Jenis Kelamin' => $row['jenis_kelamin'] ?? null,
                'Tanggal Lahir' => $row['tanggal_lahir'] ?? null,
                'Alamat' => $row['alamat'] ?? null,
                'Dusun' => $row['dusun'] ?? null,
                'Pendidikan' => $row['pendidikan'] ?? null,
                'Pekerjaan' => $row['pekerjaan'] ?? null,
                'Nomor Kartu Keluarga' => $this->cleanNumericString($row['nomor_kartu_keluarga'] ?? null),
            ];

            // Validasi kolom wajib
            $requiredColumns = ['NIK', 'Nama Lengkap', 'Jenis Kelamin', 'Tanggal Lahir', 'Alamat', 'Dusun', 'Nomor Kartu Keluarga'];
            
            foreach ($requiredColumns as $col) {
                if (empty($data[$col])) {
                    $rowErrors[] = "Kolom '$col' wajib diisi";
                }
            }

            if (!empty($rowErrors)) {
                $this->errors[] = [
                    'row' => $this->rowNumber,
                    'errors' => $rowErrors,
                ];
                continue;
            }

            // Validasi NIK (16 digit)
            $nik = $data['NIK'];
            if (strlen($nik) !== 16 || !ctype_digit($nik)) {
                $rowErrors[] = "NIK harus 16 digit angka (pastikan diformat sebagai Text di Excel)";
            }

            // Cek duplikasi NIK dalam file
            if (in_array($nik, $nikList)) {
                $rowErrors[] = "NIK duplikat dalam file Excel";
            }
            $nikList[] = $nik;

            // Validasi Jenis Kelamin
            $jenisKelamin = strtoupper(trim($data['Jenis Kelamin']));
            if (!in_array($jenisKelamin, ['L', 'P'])) {
                $rowErrors[] = "Jenis Kelamin harus 'L' atau 'P'";
            }

            // Validasi Tanggal Lahir
            $tanggalLahir = $this->parseTanggal($data['Tanggal Lahir']);
            if (!$tanggalLahir) {
                $rowErrors[] = "Format Tanggal Lahir tidak valid";
            } else {
                // Validasi umur (0-120 tahun)
                $umur = Penduduk::hitungUmur($tanggalLahir);
                if ($umur < 0 || $umur > 120) {
                    $rowErrors[] = "Umur tidak valid (harus 0-120 tahun)";
                }
            }

            // Validasi Dusun
            $namaDusun = strtolower(trim($data['Dusun']));
            if (!isset($this->dusunMap[$namaDusun])) {
                $rowErrors[] = "Dusun '{$data['Dusun']}' tidak ditemukan di database";
            }

            // Validasi Nomor KK
            $nomorKK = $data['Nomor Kartu Keluarga'];
            if (strlen($nomorKK) !== 16 || !ctype_digit($nomorKK)) {
                $rowErrors[] = "Nomor Kartu Keluarga harus 16 digit angka (pastikan diformat sebagai Text di Excel)";
            }

            // Jika ada error di row ini, simpan dan skip
            if (!empty($rowErrors)) {
                $this->errors[] = [
                    'row' => $this->rowNumber,
                    'errors' => $rowErrors,
                ];
                continue;
            }

            // Data valid, siapkan untuk insert
            $umur = Penduduk::hitungUmur($tanggalLahir);
            $kategoriUsia = Penduduk::tentukanKategoriUsia($umur);

            $this->validData[] = [
                'nik' => $nik,
                'nama_lengkap' => trim($data['Nama Lengkap']),
                'jenis_kelamin' => $jenisKelamin,
                'tanggal_lahir' => $tanggalLahir->format('Y-m-d'),
                'umur' => $umur,
                'kategori_usia' => $kategoriUsia,
                'pendidikan' => trim($data['Pendidikan'] ?? ''),
                'pekerjaan' => trim($data['Pekerjaan'] ?? ''),
                'alamat' => trim($data['Alamat']),
                'id_dusun' => $this->dusunMap[$namaDusun],
                'nomor_kk' => $nomorKK,
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    }

    /**
     * Parse tanggal dari berbagai format
     */
    private function parseTanggal($tanggal)
    {
        if (empty($tanggal)) {
            return null;
        }

        try {
            // Jika sudah Carbon instance
            if ($tanggal instanceof Carbon) {
                return $tanggal;
            }

            // Coba parse sebagai Excel date number
            if (is_numeric($tanggal)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal));
            }

            // Coba berbagai format string
            $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d', 'd-M-Y', 'd/M/Y', 'm/d/Y', 'Y/m/d'];
            
            foreach ($formats as $format) {
                try {
                    $date = Carbon::createFromFormat($format, trim($tanggal));
                    if ($date) {
                        return $date;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Fallback ke parser otomatis
            return Carbon::parse($tanggal);
            
        } catch (\Exception $e) {
            return null;
        }
    }
}
