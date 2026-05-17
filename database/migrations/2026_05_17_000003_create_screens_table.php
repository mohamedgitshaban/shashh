<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('users')->cascadeOnDelete();

            // Screen details
            $table->string('name');
            $table->string('screen_type');
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->unsignedInteger('daily_impressions')->nullable();
            $table->text('description')->nullable();

            // Pricing
            $table->decimal('price_per_day', 10, 2);
            $table->unsignedSmallInteger('min_booking_days')->default(1);
            $table->unsignedSmallInteger('rotation_duration')->default(15); // seconds per slot

            // Schedule
            $table->json('active_days')->nullable();   // ["Sun","Mon","Tue",...]
            $table->time('display_from')->nullable();
            $table->time('display_to')->nullable();
            $table->boolean('is_247')->default(false);
            $table->text('blackout_dates')->nullable();

            // Location
            $table->string('street_address');
            $table->string('landmark')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();

            // Media
            $table->json('photos')->nullable();
            $table->string('cr_document')->nullable();
            $table->string('municipality_permit')->nullable();

            // Review
            $table->enum('approval_status', ['in_review', 'approved', 'rejected'])->default('in_review');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screens');
    }
};
