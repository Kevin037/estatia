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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('completed');
            $table->unsignedBigInteger('land_id');
            $table->date('dt_start');
            $table->date('dt_end');
            $table->timestamps();
            
            $table->index('name');
            $table->index('status');
            $table->index('land_id');
            $table->index('dt_start');
            $table->index('dt_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
