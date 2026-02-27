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
        Schema::create('wilayah', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('tipe', ['dusun', 'rt', 'rw']);
            $table->integer('nomor_rt')->nullable();
            $table->integer('nomor_rw')->nullable();
            $table->unsignedBigInteger('id_dusun')->nullable();
            $table->decimal('luas_wilayah', 10, 2)->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 11, 7);
            $table->timestamps();

            $table->index('tipe');
            $table->index('id_dusun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wilayah');
    }
};
