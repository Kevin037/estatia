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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('no')->nullable();
            $table->date('dt');
            $table->unsignedBigInteger('customer_id');
            $table->double('total');
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamps();
            
            $table->index('no');
            $table->index('dt');
            $table->index('customer_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
