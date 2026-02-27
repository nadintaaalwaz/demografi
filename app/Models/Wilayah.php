<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = 'wilayah';

    protected $fillable = [
        'nama',
        'tipe', // 'dusun', 'rt', 'rw'
        'nomor_rt',
        'nomor_rw',
        'id_dusun',
        'luas_wilayah',
        'latitude',
        'longitude',
    ];

    /**
     * Get the users (kasun) that belong to this dusun
     */
    public function users()
    {
        return $this->hasMany(User::class, 'id_dusun');
    }

    /**
     * Get jumlah penduduk di wilayah ini
     */
    public function getJumlahPendudukAttribute()
    {
        if ($this->tipe === 'dusun') {
            return $this->users()->count();
        }
        return 0; // Untuk RT/RW bisa dikembangkan nanti
    }

    /**
     * Get kepadatan penduduk (jiwa/km²)
     */
    public function getKepadatanAttribute()
    {
        if ($this->luas_wilayah > 0) {
            return round($this->jumlah_penduduk / $this->luas_wilayah, 2);
        }
        return 0;
    }

    /**
     * Get formatted luas wilayah
     */
    public function getLuasFormattedAttribute()
    {
        return $this->luas_wilayah ? number_format($this->luas_wilayah, 2) . ' km²' : '-';
    }

    /**
     * Get coordinates as array
     */
    public function getCoordinatesAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
            ];
        }
        return null;
    }

    /**
     * Scope untuk filter berdasarkan tipe
     */
    public function scopeOfType($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    /**
     * Scope untuk dusun saja
     */
    public function scopeDusun($query)
    {
        return $query->where('tipe', 'dusun');
    }
}
