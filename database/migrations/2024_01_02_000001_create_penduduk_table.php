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
            $table->string('nomor_kartu_keluarga', 16);

            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir');

            $table->enum('status_keluarga', ['Kepala Keluarga', 'Istri', 'Anak'])->nullable();
            $table->enum('status_perkawinan', ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'])->nullable();

            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();

            $table->text('alamat');
            $table->unsignedBigInteger('id_dusun');
            $table->string('rw')->nullable();
            $table->string('rt')->nullable();

            $table->enum('status', ['Aktif', 'Meninggal', 'Keluar'])->default('Aktif');
            $table->date('tanggal_status')->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('id_dusun')
                ->references('id')
                ->on('wilayah')
                ->onDelete('restrict');

            // Index
            $table->index('nomor_kartu_keluarga');
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
