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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->after('customer_id');
            $table->unsignedBigInteger('cluster_id')->nullable()->after('project_id');
            $table->unsignedBigInteger('unit_id')->nullable()->after('cluster_id');
            $table->text('notes')->nullable()->after('status');
            
            $table->index('project_id');
            $table->index('cluster_id');
            $table->index('unit_id');
            
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            $table->foreign('cluster_id')->references('id')->on('clusters')->onDelete('set null');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['cluster_id']);
            $table->dropForeign(['unit_id']);
            
            $table->dropIndex(['project_id']);
            $table->dropIndex(['cluster_id']);
            $table->dropIndex(['unit_id']);
            
            $table->dropColumn(['project_id', 'cluster_id', 'unit_id', 'notes']);
        });
    }
};
