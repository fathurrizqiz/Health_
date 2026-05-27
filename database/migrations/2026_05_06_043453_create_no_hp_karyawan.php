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
        Schema::create('no_hp_karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nomor_wa')->unique(); // Unique agar tidak ada duplikasi nomor
            $table->string('bagian')->nullable();
            $table->string('nrp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('no_hp_karyawan');
    }
};
