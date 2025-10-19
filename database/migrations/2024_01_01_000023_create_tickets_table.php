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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('no')->nullable();
            $table->string('title');
            $table->unsignedBigInteger('order_id');
            $table->longText('desc');
            $table->date('dt');
            $table->string('photo')->nullable();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamps();
            
            $table->index('no');
            $table->index('title');
            $table->index('order_id');
            $table->index('status');
            $table->index('dt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
