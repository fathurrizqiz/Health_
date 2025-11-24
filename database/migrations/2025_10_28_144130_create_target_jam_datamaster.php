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
        Schema::create('target_jam_datamaster', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // ex: 'Klinis', 'Non Klinis', 'Manajerial Klinis', 'Manajerial Non Klinis'
            $table->decimal('target_jam')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_jam_datamaster');
    }
};
