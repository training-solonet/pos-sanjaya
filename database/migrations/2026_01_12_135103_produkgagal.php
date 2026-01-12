<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk_gagal', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->integer('jumlah_gagal');
            $table->text('keterangan')->nullable();
            $table->date('tanggal_gagal');
            $table->string('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_produk_gagal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_gagal_id')->constrained('produk_gagal')->onDelete('cascade');
            $table->foreignId('bahan_baku_id')->constrained('bahan_baku')->onDelete('cascade');
            $table->decimal('jumlah_digunakan', 10, 3);
            $table->string('satuan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_produk_gagal');
        Schema::dropIfExists('produk_gagal');
    }
};
