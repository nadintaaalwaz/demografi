<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DinamikaPenduduk extends Model
{
    protected $table = 'dinamika_penduduk';

    protected $fillable = [
        'tahun',
        'bulan',
        'jumlah_lahir',
        'jumlah_meninggal',
        'jumlah_masuk',
        'jumlah_keluar',
        'id_dusun',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jumlah_lahir' => 'integer',
        'jumlah_meninggal' => 'integer',
        'jumlah_masuk' => 'integer',
        'jumlah_keluar' => 'integer',
        'id_dusun' => 'integer',
    ];

    /**
     * Relasi ke tabel wilayah (opsional per dusun)
     */
    public function dusun(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'id_dusun');
    }
}
