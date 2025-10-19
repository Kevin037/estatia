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
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->date('target_dt');
            $table->date('completed_dt');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('milestone_id');
            $table->string('file')->nullable();
            $table->longText('notes')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('completed');
            $table->timestamps();
            
            $table->index('project_id');
            $table->index('milestone_id');
            $table->index('status');
            $table->index('target_dt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_milestones');
    }
};
