<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdraw_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('users')->cascadeOnDelete();

            $table->decimal('amount', 12, 2);

            // Payout destination, captured per request (owner may use different accounts over time)
            $table->string('bank_name');
            $table->string('account_holder_name');
            $table->string('iban');

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Set by admin on approval: proof-of-transfer screenshot, also serves as the
            // downloadable "invoice" in the owner's payout history.
            $table->string('proof_file')->nullable();

            // Set by admin on rejection
            $table->text('rejection_reason')->nullable();

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdraw_requests');
    }
};
