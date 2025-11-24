<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_data', function (Blueprint $table) {
            $table->id();
            $table->string('nama_karyawan');
            $table->date('tmt')->nullable();
            $table->string('nrp')->unique()->nullable();
            $table->string('bagian')->nullable();
            $table->string('unit_kerja')->nullable();
            $table->string('posisi_jabatan')->nullable();
            $table->string('klinis_non_klinis')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_data');
    }
};
