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
        Schema::create('aktivitas_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('aksi', ['INSERT', 'UPDATE', 'DELETE']);
            $table->string('nik', 16)->nullable();
            $table->text('field_diubah')->nullable();
            $table->text('nilai_lama')->nullable();
            $table->text('nilai_baru')->nullable();
            $table->timestamp('waktu')->useCurrent();

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Index
            $table->index('user_id');
            $table->index('aksi');
            $table->index('nik');
            $table->index('waktu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aktivitas_logs');
    }
};
