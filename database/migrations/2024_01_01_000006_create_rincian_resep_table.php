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
        Schema::create('rincian_resep', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_resep')->constrained('resep')->onDelete('cascade');
            $table->integer('qty')->default(0);
            $table->enum('hitungan', ['pcs', 'gram', 'kg', 'ml', 'liter', 'sdm' , 'slice'])->nullable();
            $table->integer('harga')->default(0);
            $table->string('nama_bahan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rincian_resep');
    }
};
