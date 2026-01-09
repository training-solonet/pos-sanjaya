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
        Schema::table('shift', function (Blueprint $table) {
            $table->unsignedBigInteger('penjualan_tunai')->default(0)->after('total_penjualan');
            $table->unsignedBigInteger('total_transaksi')->default(0)->after('penjualan_tunai');
            $table->unsignedBigInteger('uang_aktual')->nullable()->after('total_transaksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift', function (Blueprint $table) {
            $table->dropColumn(['penjualan_tunai', 'total_transaksi', 'uang_aktual']);
        });
    }
};
