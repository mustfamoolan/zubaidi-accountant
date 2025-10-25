<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إزالة withdrawal من enum (نبقي فقط deposit و shared_expense)
        DB::statement("ALTER TABLE capital_transactions MODIFY COLUMN type ENUM('deposit', 'shared_expense') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // العودة للأنواع الأصلية
        DB::statement("ALTER TABLE capital_transactions MODIFY COLUMN type ENUM('deposit', 'withdrawal', 'shared_expense') NOT NULL");
    }
};
