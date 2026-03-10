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
            $table->string('nik', 16);
            $table->enum('jenis_dinamika', ['Kelahiran', 'Kematian', 'Migrasi Masuk', 'Migrasi Keluar']);
            $table->date('tanggal_peristiwa');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign key - nullable karena NIK bisa saja sudah tidak ada di tabel penduduk
            $table->foreign('nik')->references('nik')->on('penduduk')->onDelete('cascade');
            
            // Index
            $table->index('nik');
            $table->index('jenis_dinamika');
            $table->index('tanggal_peristiwa');
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
