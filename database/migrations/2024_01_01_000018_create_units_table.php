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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('no')->nullable();
            $table->double('price');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('cluster_id');
            $table->unsignedBigInteger('sales_id');
            $table->longText('desc')->nullable();
            $table->longText('facilities')->nullable();
            $table->enum('status', ['available', 'reserved', 'sold', 'handed_over'])->default('available');
            $table->timestamps();
            
            $table->index('name');
            $table->index('no');
            $table->index('product_id');
            $table->index('cluster_id');
            $table->index('sales_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
