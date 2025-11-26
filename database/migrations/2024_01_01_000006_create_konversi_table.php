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
            $table->foreignId('id_satuan')->constrained('satuan')->onDelete('cascade');
            $table->integer('jumlah');
            $table->integer('satuan_dasar');
            $table->dateTime('tgl');
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
