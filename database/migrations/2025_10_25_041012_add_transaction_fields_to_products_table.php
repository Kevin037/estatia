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
        Schema::table('products', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('sku')->unique()->after('name');
            $table->string('photo')->nullable()->after('sku');
            $table->decimal('qty', 15, 2)->default(0)->after('price');
            
            // Make type_id nullable since we're using this for transaction products
            $table->unsignedBigInteger('type_id')->nullable()->change();
            
            $table->index('name');
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name', 'sku', 'photo', 'qty']);
            $table->dropIndex(['name']);
            $table->dropIndex(['sku']);
        });
    }
};
