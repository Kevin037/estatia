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
        Schema::create('lands', function (Blueprint $table) {
            $table->id();
            $table->longText('address');
            $table->double('wide');
            $table->double('length');
            $table->longText('location')->nullable();
            $table->longText('desc')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
            
            $table->index('wide');
            $table->index('length');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lands');
    }
};
