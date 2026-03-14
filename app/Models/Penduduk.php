<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Penduduk extends Model
{
    protected $table = 'penduduk';
    
    protected $primaryKey = 'nik';
    
    public $incrementing = false;
    
    protected $keyType = 'string';

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'jenis_kelamin',
        'tanggal_lahir',
        'umur',
        'kategori_usia',
        'pendidikan',
        'pekerjaan',
        'alamat',
        'id_dusun',
        'nomor_kk',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'umur' => 'integer',
    ];

    /**
     * Relasi ke tabel wilayah (dusun)
     */
    public function dusun(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'id_dusun');
    }

    /**
     * Relasi ke tabel dinamika_penduduk
     */
    public function dinamika(): HasMany
    {
        return $this->hasMany(DinamikaPenduduk::class, 'nik', 'nik');
    }

    /**
     * Hitung umur otomatis dari tanggal lahir
     */
    public static function hitungUmur($tanggalLahir)
    {
        if (!$tanggalLahir) {
            return -1;
        }

        try {
            return Carbon::parse($tanggalLahir)->age;
        } catch (\Exception $e) {
            return -1;
        }
    }

    /**
     * Tentukan kategori usia berdasarkan umur
     */
    public static function tentukanKategoriUsia($umur)
    {
        if ($umur < 5) {
            return 'Balita';
        } elseif ($umur >= 60) {
            return 'Lansia';
        } else {
            return 'Produktif';
        }
    }
}