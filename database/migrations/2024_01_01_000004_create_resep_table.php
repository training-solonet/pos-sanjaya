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
        Schema::create('resep', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->integer('porsi')->default(1);
            $table->enum('kategori', ['Makanan', 'Minuman', 'Snack', 'Roti dan Pastry', 'Kue dan Dessert'])->nullable();
            $table->integer('waktu_pembuatan')->nullable()->comment('dalam menit');
            $table->text('langkah')->nullable();
            $table->text('catatan')->nullable();
            $table->integer('harga_jual')->default(0);
            $table->integer('margin')->default(0);
            $table->enum('status', ['draft', 'aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep');
    }
};
