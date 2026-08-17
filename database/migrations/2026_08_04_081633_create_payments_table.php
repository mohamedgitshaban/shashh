<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();

            $table->string('tap_charge_id')->nullable()->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('SAR');

            // pending -> initiated by us before redirecting to Tap
            // initiated -> charge created at Tap, awaiting customer action
            // captured  -> payment succeeded
            // failed / cancelled / declined -> payment did not succeed
            $table->enum('status', [
                'pending',
                'initiated',
                'captured',
                'failed',
                'cancelled',
                'declined',
            ])->default('pending');

            $table->string('payment_url')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
