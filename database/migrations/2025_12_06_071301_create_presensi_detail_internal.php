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
        Schema::create('presensi_detail_internal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detail_program_id')->constrained('detail_internal')->cascadeOnDelete();
            $table->date('tanggal')->nullable();
            $table->string('nama_karyawan')->nullable();
            $table->string('nrp')->nullable();
            $table->integer('jam_diklat')->nullable();
            $table->decimal('prescore')->nullable();
            $table->decimal('postscore')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_detail_internal');
    }
};
