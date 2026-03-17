<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Run the migrations.
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('voucher_code', 50)->nullable()->after('product_name');
            $table->decimal('voucher_discount', 10, 2)->nullable()->after('voucher_code');
        });
    }

    // Reverse the migrations.
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['voucher_code', 'voucher_discount']);
        });
    }
};
