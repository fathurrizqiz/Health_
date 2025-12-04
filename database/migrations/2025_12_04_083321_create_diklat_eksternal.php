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
        Schema::create('diklat_eksternal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('program_diklat_eksternal')->cascadeOnDelete();
            $table->string('nama_karyawan')->nullable();
            $table->string('nrp')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->integer('jam_diklat')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->string('status')->nullable();
            $table->string('diklat')->default('EKSTERNAL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diklat_eksternal');
    }
};
