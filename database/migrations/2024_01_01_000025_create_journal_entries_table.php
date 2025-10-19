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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->string('transaction_name');
            $table->date('dt');
            $table->unsignedBigInteger('account_id');
            $table->double('debit');
            $table->double('credit');
            $table->longText('desc')->nullable();
            $table->unsignedBigInteger('journal_entry_id');
            $table->timestamps();
            
            $table->index('transaction_id');
            $table->index('transaction_name');
            $table->index('dt');
            $table->index('account_id');
            $table->index('journal_entry_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
