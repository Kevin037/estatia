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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('no')->nullable();
            $table->unsignedBigInteger('invoice_id');
            $table->string('bank_account_id')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_type')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->enum('payment_type', ['cash', 'transfer'])->default('transfer');
            $table->timestamps();
            
            $table->index('no');
            $table->index('invoice_id');
            $table->index('payment_type');
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
