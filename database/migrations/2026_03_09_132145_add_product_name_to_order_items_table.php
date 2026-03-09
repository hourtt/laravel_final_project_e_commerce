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
        Schema::table('order_items', function (Blueprint $table) {
            // Add product_name to store the historical name
            $table->string('product_name')->nullable()->after('product_id');

            // Drop existing foreign key and make product_id nullable
            $table->dropForeign(['product_id']);
            $table->unsignedBigInteger('product_id')->nullable()->change();

            // Re-add foreign key with set null so order items aren't deleted when a product is deleted
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->dropColumn('product_name');
        });
    }
};
