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
        Schema::create('jurnal', function (Blueprint $table) {
            $table->id();
            $table->dateTime('tgl');
            $table->enum('jenis', ['pemasukan', 'pengeluaran']);
            $table->text('keterangan')->nullable();
            $table->integer('nomimal')->default(0);
            $table->enum('kategori', ['Operasioanl', 'Utiilitas', 'Bahan Baku', 'Penjualan', 'Trankspotasi', 'lainnya'])->nullable();
            $table->enum('role', ['admin', 'manajemen'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal');
    }
};
