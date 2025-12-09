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
        //
        Schema::create('shift', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->dateTime('mulai');
            $table->dateTime('selesai')->nullable();
            $table->integer('durasi')->default(0);
            $table->unsignedBigInteger('modal')->default(0);
            $table->unsignedBigInteger('total_penjualan')->default(0);
            $table->integer('selisih')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('shift');
    }
};
