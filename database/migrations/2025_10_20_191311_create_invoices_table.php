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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->decimal('amount_usd', 15, 2);
            $table->decimal('exchange_rate', 10, 4);
            $table->decimal('amount_iqd', 15, 2);
            $table->decimal('tax_iqd', 15, 2)->default(0);
            $table->decimal('total_iqd', 15, 2);
            $table->enum('status', ['available', 'sold', 'partial'])->default('available');
            $table->date('purchase_date');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
