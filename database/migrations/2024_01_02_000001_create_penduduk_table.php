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
        Schema::create('penduduk', function (Blueprint $table) {
            $table->string('nik', 16)->primary();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir');
            $table->integer('umur');
            $table->enum('kategori_usia', ['Balita', 'Produktif', 'Lansia']);
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->text('alamat');
            $table->unsignedBigInteger('id_dusun');
            $table->string('nomor_kk', 16);
            $table->enum('status', ['Aktif', 'Meninggal', 'Keluar'])->default('Aktif');
            $table->timestamps();

            // Foreign key
            $table->foreign('id_dusun')->references('id')->on('wilayah')->onDelete('restrict');
            
            // Index untuk performa query
            $table->index('nomor_kk');
            $table->index('id_dusun');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penduduk');
    }
};
