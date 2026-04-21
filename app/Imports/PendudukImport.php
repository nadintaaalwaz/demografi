<?php

namespace App\Imports;

use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class PendudukImport implements ToCollection, WithHeadingRow
{
    public $errors = [];
    public $validData = [];
    private array $dusunMap = [];
    private int $rowNumber = 1;
    private array $nikInFile = [];

    public function __construct()
    {
        // Load mapping nama dusun ke ID
        $this->loadDusunMap();
    }

    /**
     * Load mapping nama dusun ke ID
     */
    private function loadDusunMap(): void
    {
        $wilayah = Wilayah::where('tipe', 'dusun')->get();
        foreach ($wilayah as $w) {
            $key = strtolower(trim($w->nama));
            $this->dusunMap[$key] = $w->id;
        }
    }

    /**
     * Bersihkan numeric string dari Excel (handle scientific notation & leading zeros)
     */
    private function cleanNumericString($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return number_format((float) $value, 0, '', '');
        }

        return preg_replace('/[^0-9]/', '', trim((string) $value));
    }

    /**
     * Process collection dari Excel
     */
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $this->rowNumber++;
            $rowErrors = [];

            $nomorKartuKeluarga = $this->cleanNumericString($row['nomor_kartu_keluarga'] ?? null);
            $nik = $this->cleanNumericString($row['nik'] ?? null);
            $namaLengkap = trim((string) ($row['nama_lengkap'] ?? ''));
            $jenisKelamin = strtoupper(trim((string) ($row['jenis_kelamin'] ?? '')));
            $tempatLahir = trim((string) ($row['tempat_lahir'] ?? ''));
            $tanggalLahir = $this->parseTanggal($row['tanggal_lahir'] ?? null);
            $statusKeluarga = trim((string) ($row['status_keluarga'] ?? ''));
            $statusPerkawinan = trim((string) ($row['status_perkawinan'] ?? ''));
            $pendidikan = trim((string) ($row['pendidikan'] ?? ''));
            $pekerjaan = trim((string) ($row['pekerjaan'] ?? ''));
            $dusun = trim((string) ($row['dusun'] ?? ''));
            $rw = trim((string) ($row['rw'] ?? ''));
            $rt = trim((string) ($row['rt'] ?? ''));
            $alamat = trim((string) ($row['alamat'] ?? ''));
            $status = trim((string) ($row['status'] ?? ''));
            $tanggalStatus = $this->parseTanggal($row['tanggal_status'] ?? null);

            // Validasi wajib: NIK
            if (empty($nik)) {
                $rowErrors[] = "Kolom 'nik' wajib diisi";
            }

            // Validasi unik NIK dalam file
            if (!empty($nik)) {
                if (in_array($nik, $this->nikInFile, true)) {
                    $rowErrors[] = "NIK duplikat dalam file Excel";
                }
                $this->nikInFile[] = $nik;
            }

            // Validasi jenis_kelamin
            if (!in_array($jenisKelamin, ['L', 'P'], true)) {
                $rowErrors[] = "Kolom 'jenis_kelamin' harus bernilai L atau P";
            }

            // Validasi tanggal_lahir
            if (!$tanggalLahir) {
                $rowErrors[] = "Kolom 'tanggal_lahir' harus format tanggal yang valid";
            }

            // Validasi mapping dusun
            $namaDusun = strtolower($dusun);
            if (empty($namaDusun) || !Arr::exists($this->dusunMap, $namaDusun)) {
                $rowErrors[] = "Dusun '{$dusun}' tidak ditemukan di tabel wilayah";
            }

            if (!empty($rowErrors)) {
                $this->errors[] = [
                    'row' => $this->rowNumber,
                    'errors' => $rowErrors,
                ];
                continue;
            }

            $this->validData[] = [
                'nik' => $nik,
                'nomor_kartu_keluarga' => $nomorKartuKeluarga,
                'nama_lengkap' => $namaLengkap,
                'jenis_kelamin' => $jenisKelamin,
                'tempat_lahir' => $tempatLahir !== '' ? $tempatLahir : null,
                'tanggal_lahir' => $tanggalLahir->format('Y-m-d'),
                'status_keluarga' => $statusKeluarga !== '' ? $statusKeluarga : null,
                'status_perkawinan' => $statusPerkawinan !== '' ? $statusPerkawinan : null,
                'pendidikan' => $pendidikan !== '' ? $pendidikan : null,
                'pekerjaan' => $pekerjaan !== '' ? $pekerjaan : null,
                'alamat' => $alamat,
                'id_dusun' => $this->dusunMap[$namaDusun],
                'rw' => $rw !== '' ? $rw : null,
                'rt' => $rt !== '' ? $rt : null,
                'status' => $status !== '' ? $status : 'Aktif',
                'tanggal_status' => $tanggalStatus?->format('Y-m-d'),
            ];
        }
    }

    /**
     * Parse tanggal dari berbagai format
     */
    private function parseTanggal($tanggal): ?Carbon
    {
        if (!$tanggal) {
            return null;
        }

        try {
            if ($tanggal instanceof Carbon) {
                return $tanggal;
            }

            if (is_numeric($tanggal)) {
                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal)
                );
            }

            return Carbon::parse($tanggal);
        } catch (\Exception $e) {
            return null;
        }
    }
}
