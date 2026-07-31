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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('customer_name')->nullable()->after('user_id');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->string('order_type')->default('dine_in')->after('customer_phone'); // dine_in, takeaway, delivery
            $table->string('table_number')->nullable()->after('order_type');
            $table->string('order_source')->default('pos')->after('table_number'); // pos, customer_app
            $table->string('order_status')->default('completed')->after('order_source'); // pending_payment, paid, processing, ready, completed, cancelled
            $table->text('notes')->nullable()->after('order_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'customer_name',
                'customer_phone',
                'order_type',
                'table_number',
                'order_source',
                'order_status',
                'notes',
            ]);
        });
    }
};
