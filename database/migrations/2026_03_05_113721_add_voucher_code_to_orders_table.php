<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Store the voucher code used (null = no voucher applied)
            $table->string('voucher_code', 50)->nullable()->after('total_price');
            // Store the discount amount for easy display without recalculation
            $table->decimal('voucher_discount', 10, 2)->nullable()->after('voucher_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['voucher_code', 'voucher_discount']);
        });
    }
};
