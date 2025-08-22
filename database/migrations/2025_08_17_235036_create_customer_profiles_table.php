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
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->integer('loyalty_points')->default(0);
            $table->string('photo')->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->enum('preferred_contact', ['email', 'sms', 'push'])->default('push');
            $table->json('dietary_preferences')->nullable();
            $table->timestamp('last_visit')->nullable();
            $table->decimal('total_spent', 10, 2)->default(0.00);
            $table->integer('visit_count')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
