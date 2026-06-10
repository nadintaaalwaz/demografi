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
        Schema::create('pengajuan_penduduk', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Pengaju (Kasun)
            |--------------------------------------------------------------------------
            */
            $table->foreignId('id_pengaju')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Penduduk yang diajukan
            | Nullable karena pengajuan kelahiran belum memiliki data penduduk
            |--------------------------------------------------------------------------
            */
            $table->string('nik', 16)->nullable();
            $table->foreign('nik')
                ->references('nik')
                ->on('penduduk')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Nomor Pengajuan
            |--------------------------------------------------------------------------
            */
            $table->string('kode_pengajuan')->unique();

            /*
            |--------------------------------------------------------------------------
            | Jenis Pengajuan
            |--------------------------------------------------------------------------
            */
            $table->enum('jenis_pengajuan', [
                'kelahiran',
                'kematian',
                'migrasi_masuk',
                'migrasi_keluar',
                'perubahan_data'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Status Approval
            |--------------------------------------------------------------------------
            */
            $table->enum('status', [
                'menunggu',
                'disetujui',
                'ditolak'
            ])->default('menunggu');

            /*
            |--------------------------------------------------------------------------
            | Data yang diajukan
            |
            | Contoh:
            | {
            |   "nama_lengkap": "Budi Santoso",
            |   "pekerjaan": "Wiraswasta",
            |   "alamat": "Dusun Sebalor"
            | }
            |--------------------------------------------------------------------------
            */
            $table->json('data_pengajuan')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Catatan dari Kasun
            |--------------------------------------------------------------------------
            */
            $table->text('catatan')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Alasan Penolakan dari Kasi
            |--------------------------------------------------------------------------
            */
            $table->text('alasan_penolakan')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Kasi yang melakukan approval
            |--------------------------------------------------------------------------
            */
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Waktu Approval
            |--------------------------------------------------------------------------
            */
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */
            $table->index('status');
            $table->index('jenis_pengajuan');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_penduduk');
    }
};