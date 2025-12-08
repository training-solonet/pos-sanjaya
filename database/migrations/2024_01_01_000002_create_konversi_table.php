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
        Schema::create('konversi', function (Blueprint $table) {
            $table->id();
            $table->integer('jumlah');
            $table->integer('nilai');
            $table->foreignId('satuan_besar')->constrained('satuan')->onDelete('cascade');
            $table->foreignId('satuan_kecil')->constrained('satuan')->onDelete('cascade');
            $table->dateTime('tgl')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konversi');
    }
};
