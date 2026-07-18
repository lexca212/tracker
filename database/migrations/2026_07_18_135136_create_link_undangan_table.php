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
        Schema::create('link_undangan', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('link_undangan')->nullable();
            $table->string('nama_pasangan_1')->nullable();
            $table->string('nama_pasangan_2')->nullable();
            $table->string('tanggal_pernikahan')->nullable();
            $table->string('lokasi_pernikahan')->nullable();
            $table->string('tamu_undangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_undangan');
    }
};
