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
        // NOTE: This migration was superseded by Spatie Permission migration
        // Spatie migration (2025_08_25_150913_create_permission_tables) 
        // recreated this table with different structure
        
        // Original custom structure (now obsolete):
        // Schema::create('roles', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name')->unique();
        //     $table->text('description')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
        
        // Current structure (managed by Spatie):
        // - id, name, guard_name, created_at, updated_at
        // This migration is kept for historical reference only
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
