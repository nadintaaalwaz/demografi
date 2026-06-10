<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Penduduk;

class PengajuanPenduduk extends Model
{
    protected $table = 'pengajuan_penduduk';

    protected $fillable = [
        'id_pengaju',
        'nik',
        'kode_pengajuan',
        'jenis_pengajuan',
        'status',
        'data_pengajuan',
        'catatan',
        'alasan_penolakan',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'data_pengajuan' => 'array',
        'approved_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relasi Pengaju (Kasun)
    |--------------------------------------------------------------------------
    */
    public function pengaju()
    {
        return $this->belongsTo(User::class, 'id_pengaju');
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi Penduduk
    |--------------------------------------------------------------------------
    */
    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class, 'nik', 'nik');
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi Approver (Kasi)
    |--------------------------------------------------------------------------
    */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}