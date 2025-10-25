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
        // إضافة withdrawal إلى enum
        DB::statement("ALTER TABLE capital_transactions MODIFY COLUMN type ENUM('deposit', 'withdrawal', 'shared_expense') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إزالة withdrawal من enum
        DB::statement("ALTER TABLE capital_transactions MODIFY COLUMN type ENUM('deposit', 'shared_expense') NOT NULL");
    }
};
