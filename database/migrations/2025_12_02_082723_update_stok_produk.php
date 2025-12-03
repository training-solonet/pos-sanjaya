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
        Schema::create('update_stok_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_produk')->constrained('produk')->onDelete('cascade');
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_baru')->default(0);
            $table->integer('total_stok')->default(0);
            $table->dateTime('kadaluarsa');
            $table->dateTime('tanggal_update');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('update_stok_produk');
    }
};
