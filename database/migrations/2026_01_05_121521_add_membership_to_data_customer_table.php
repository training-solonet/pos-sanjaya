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
        Schema::table('data_customer', function (Blueprint $table) {
            $table->string('kode_member', 20)->unique()->nullable()->after('nama');
            $table->integer('total_poin')->default(0)->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_customer', function (Blueprint $table) {
            $table->dropColumn(['kode_member', 'total_poin']);
        });
    }
};
