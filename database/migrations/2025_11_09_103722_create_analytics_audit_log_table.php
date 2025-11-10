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
        Schema::create('analytics_audit_log', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('action', 50)->index(); // calculate, update, discrepancy_detected, auto_fix
            $table->string('reason')->nullable();
            $table->string('severity', 20)->nullable()->index(); // critical, high, medium, low
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('changes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['date', 'action']);
            $table->index(['date', 'severity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_audit_log');
    }
};
