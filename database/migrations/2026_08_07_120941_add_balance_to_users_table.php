<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Withdrawable balance for company (screen owner) accounts. Credited with
            // each booking's net_earned (90%) when the campaign's payment is captured;
            // debited when an admin approves a withdraw request.
            $table->decimal('balance', 12, 2)->default(0.00)->after('cr');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
    }
};
