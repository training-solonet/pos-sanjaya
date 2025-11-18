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
        Schema::create('bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->dateTime('tglupdate');
            $table->string('nama');
            $table->integer('stok')->default(0);
            $table->enum('kategori', ['Bahan Utama', 'Bahan Pembantu'])->nullable();
            $table->integer('min_stok')->default(0);
            $table->integer('harga_satuan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_baku');
    }
};
