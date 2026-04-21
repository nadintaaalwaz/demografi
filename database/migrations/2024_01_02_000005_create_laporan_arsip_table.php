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
        Schema::create('laporan_arsip', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_laporan', ['Bulanan', 'Demografi', 'Dinamika', 'Kepadatan']);
            $table->integer('bulan')->nullable();
            $table->integer('tahun');
            $table->string('file_path');
            $table->unsignedBigInteger('dibuat_oleh');
            $table->timestamps();
            $table->string('nama_file')->nullable();

            // Foreign key
            $table->foreign('dibuat_oleh')->references('id')->on('users')->onDelete('cascade');
            
            // Index
            $table->index('jenis_laporan');
            $table->index(['bulan', 'tahun']);
            $table->index('dibuat_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_arsip');
    }
};
