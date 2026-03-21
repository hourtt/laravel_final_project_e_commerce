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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('address_id')->nullable()->after('user_id')->constrained('addresses')->nullOnDelete();
            
            // Address snapshots
            $table->string('shipping_full_name')->nullable()->after('address_id');
            $table->string('shipping_phone_number')->nullable()->after('shipping_full_name');
            $table->string('shipping_street_address')->nullable()->after('shipping_phone_number');
            $table->string('shipping_city')->nullable()->after('shipping_street_address');
            $table->string('shipping_state')->nullable()->after('shipping_city');
            $table->string('shipping_postal_code')->nullable()->after('shipping_state');
            $table->string('shipping_country')->nullable()->after('shipping_postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->dropColumn([
                'address_id',
                'shipping_full_name',
                'shipping_phone_number',
                'shipping_street_address',
                'shipping_city',
                'shipping_state',
                'shipping_postal_code',
                'shipping_country',
            ]);
        });
    }
};
