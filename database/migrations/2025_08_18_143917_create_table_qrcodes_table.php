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
        Schema::create('table_qrcodes', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('table_id')
                  ->constrained('tables')
                  ->onDelete('cascade');
                  
            $table->foreignId('reservation_id')
                  ->nullable()
                  ->constrained('table_reservations')
                  ->nullOnDelete();
            
            $table->string('session_code', 50)->unique();
            $table->string('qr_code_url')->nullable();
            $table->string('qr_code_png')->nullable();
            $table->string('qr_code_svg')->nullable();
            $table->json('qr_code_data')->nullable();
            
            $table->string('guest_name')->nullable();
            $table->string('guest_phone', 20)->nullable();
            $table->integer('guest_count')->nullable();
            
            $table->timestamp('started_at');
            $table->timestamp('expires_at');
            $table->timestamp('completed_at')->nullable();
            
            $table->enum('status', ['active', 'completed', 'expired'])->default('active');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['table_id', 'status']);
            $table->index(['session_code']);
            $table->index(['status', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_qrcodes');
    }
};
