<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DinamikaPenduduk extends Model
{
    protected $table = 'dinamika_penduduk';

    protected $fillable = [
        'nik',
        'jenis_dinamika',
        'tanggal_peristiwa',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_peristiwa' => 'date',
    ];

    /**
     * Relasi ke tabel penduduk
     */
    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class, 'nik', 'nik');
    }
}
