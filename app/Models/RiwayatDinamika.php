<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatDinamika extends Model
{
    protected $table = 'riwayat_dinamika';

    protected $fillable = [
        'penduduk_nik',
        'jenis_dinamika',
        'tanggal_dinamika',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_dinamika' => 'date',
    ];

    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(
            Penduduk::class,
            'penduduk_nik',
            'nik'
        );
    }
}