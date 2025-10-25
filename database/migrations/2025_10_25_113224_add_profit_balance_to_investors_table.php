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
        Schema::table('investors', function (Blueprint $table) {
            $table->decimal('profit_balance', 15, 2)->default(0)->after('total_profits');
            $table->decimal('total_withdrawals', 15, 2)->default(0)->after('profit_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->dropColumn(['profit_balance', 'total_withdrawals']);
        });
    }
};
