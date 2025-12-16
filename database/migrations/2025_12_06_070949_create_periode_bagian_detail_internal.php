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
        Schema::create('periode_bagian_detail_internal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detail_program_id')->constrained('detail_internal')->cascadeOnDelete();
            $table->foreignId('periode_id')->constrained('periode_detail_internal')->cascadeOnDelete();
            $table->string('nama_karyawan');
            $table->date('tmt')->nullable();
            $table->string('nrp')->nullable();
            $table->string('bagian')->nullable();
            $table->string('unit_kerja')->nullable();
            $table->string('posisi_jabatan')->nullable();
            $table->string('klinis_non_klinis')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();

             $table->timestamp('hadir_at')->nullable();
            $table->timestamp('post_done_at')->nullable();
            $table->timestamp('pree_done_at')->nullable();

            // SERTIFIKAT
            $table->string('sertifikat_path')->nullable();
            $table->timestamp('sertifikat_generated_at')->nullable();
            $table->unique(['detail_program_id', 'nrp']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_bagian_detail_internal');
    }
};
