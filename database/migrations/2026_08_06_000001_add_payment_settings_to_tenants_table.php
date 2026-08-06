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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('bank_info')->nullable()->after('phone');
            $table->string('ewallet_info')->nullable()->after('bank_info');
            $table->string('qris_info')->nullable()->after('ewallet_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['bank_info', 'ewallet_info', 'qris_info']);
        });
    }
};
