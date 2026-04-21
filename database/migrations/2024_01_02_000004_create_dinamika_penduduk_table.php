<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dinamika_penduduk', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan'); // 1–12

            $table->integer('jumlah_lahir')->default(0);
            $table->integer('jumlah_meninggal')->default(0);
            $table->integer('jumlah_masuk')->default(0);
            $table->integer('jumlah_keluar')->default(0);

            $table->unsignedBigInteger('id_dusun')->nullable(); // opsional (kalau per dusun)

            $table->timestamps();

            // Foreign key (opsional)
            $table->foreign('id_dusun')
                ->references('id')
                ->on('wilayah')
                ->nullOnDelete();

            // Index
            $table->index(['tahun', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dinamika_penduduk');
    }
};
