<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('screen_id')->constrained()->cascadeOnDelete();

            $table->enum('status', [
                'pending_approval',
                'approved',
                'rejected',
                'live',
                'completed',
            ])->default('pending_approval');

            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            // Financial
            $table->decimal('sale_price', 10, 2);   // price_per_day * days
            $table->decimal('commission', 10, 2);    // 9% of sale_price
            $table->decimal('net_earned', 10, 2);    // sale_price - commission

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
