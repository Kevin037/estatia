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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('no')->nullable();
            $table->date('dt');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('supplier_id');
            $table->double('total');
            $table->enum('status', ['pending', 'completed'])->default('completed');
            $table->timestamps();
            
            $table->index('no');
            $table->index('dt');
            $table->index('project_id');
            $table->index('supplier_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
