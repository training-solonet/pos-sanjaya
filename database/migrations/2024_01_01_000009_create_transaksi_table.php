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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->dateTime('tgl');
            $table->enum('metode', ['tunai', 'kartu', 'transfer', 'qris'])->default('tunai');
            $table->integer('ppn')->default(0);
            $table->integer('diskon')->default(0);
            $table->integer('bayar')->default(0);
            $table->integer('kembalian')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
