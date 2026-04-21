<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $dusunSebalorId = DB::table('wilayah')
            ->where('tipe', 'dusun')
            ->where(function ($query) {
                $query->whereRaw('LOWER(nama) = ?', ['dusun sebalor'])
                    ->orWhereRaw('LOWER(nama) = ?', ['sebalor']);
            })
            ->value('id');

        $dusunSirahKandangId = DB::table('wilayah')
            ->where('tipe', 'dusun')
            ->where(function ($query) {
                $query->whereRaw('LOWER(nama) = ?', ['dusun sirah kandang'])
                    ->orWhereRaw('LOWER(nama) = ?', ['sirah kandang']);
            })
            ->value('id');

        if (!$dusunSebalorId || !$dusunSirahKandangId) {
            return;
        }

        // RW 1 -> Dusun Sirah Kandang
        DB::table('wilayah')
            ->where('tipe', 'rw')
            ->where('nomor_rw', 1)
            ->update(['id_dusun' => $dusunSirahKandangId]);

        // RW 2, 3, 4 -> Dusun Sebalor
        DB::table('wilayah')
            ->where('tipe', 'rw')
            ->whereIn('nomor_rw', [2, 3, 4])
            ->update(['id_dusun' => $dusunSebalorId]);

        // RT yang berada di RW 1 -> Dusun Sirah Kandang
        DB::table('wilayah')
            ->where('tipe', 'rt')
            ->where('nomor_rw', 1)
            ->update(['id_dusun' => $dusunSirahKandangId]);

        // RT yang berada di RW 2, 3, 4 -> Dusun Sebalor
        DB::table('wilayah')
            ->where('tipe', 'rt')
            ->whereIn('nomor_rw', [2, 3, 4])
            ->update(['id_dusun' => $dusunSebalorId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('wilayah')
            ->where('tipe', 'rw')
            ->whereIn('nomor_rw', [1, 2, 3, 4])
            ->update(['id_dusun' => null]);

        DB::table('wilayah')
            ->where('tipe', 'rt')
            ->whereIn('nomor_rw', [1, 2, 3, 4])
            ->update(['id_dusun' => null]);
    }
};
