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
        Schema::create('invoice_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_sale_id')->constrained('invoice_sales');
            $table->foreignId('customer_id')->constrained('customers');
            $table->decimal('amount_usd', 15, 2);
            $table->decimal('exchange_rate', 10, 4);
            $table->decimal('amount_iqd', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_sale_items');
    }
};
