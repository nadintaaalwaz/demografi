<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PendudukTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        // No data rows, only header template
        return [];
    }

    public function headings(): array
    {
        return [
            'nomor_kartu_keluarga',
            'nik',
            'nama_lengkap',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'status_keluarga',
            'status_perkawinan',
            'pendidikan',
            'pekerjaan',
            'dusun',
            'rw',
            'rt',
            'alamat',
            'status',
            'tanggal_status',
        ];
    }
}
