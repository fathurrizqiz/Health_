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
        Schema::create('diklat_hlc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('program_diklat_hlc')->cascadeOnDelete();
            $table->string('nama');
            $table->string('pengajar')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diklat_hlc');
    }
};
