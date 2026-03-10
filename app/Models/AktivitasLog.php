<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AktivitasLog extends Model
{
    protected $table = 'aktivitas_logs';
    
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'aksi',
        'nik',
        'field_diubah',
        'nilai_lama',
        'nilai_baru',
        'waktu',
    ];

    protected $casts = [
        'waktu' => 'datetime',
    ];

    /**
     * Relasi ke tabel users
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
