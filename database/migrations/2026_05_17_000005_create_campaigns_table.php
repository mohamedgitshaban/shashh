<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();

            // Section 1 – Objective
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('objective');
            $table->date('date_from');
            $table->date('date_to');

            // Section 4 – Artwork
            $table->string('artwork')->nullable();
            $table->boolean('needs_admin_artwork')->default(false);
            $table->decimal('artwork_fee', 10, 2)->default(0.00);

            // Running total of impressions (incremented daily while live)
            $table->unsignedBigInteger('total_impressions')->default(0);

            $table->enum('status', [
                'pending_approval',
                'approved',
                'live',
                'completed',
                'rejected',
            ])->default('pending_approval');

            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
