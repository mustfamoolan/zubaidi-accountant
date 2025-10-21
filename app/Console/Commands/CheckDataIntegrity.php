<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InvoiceSaleItem;
use App\Models\InvoiceSale;
use App\Models\Invoice;

class CheckDataIntegrity extends Command
{
    protected $signature = 'data:check';
    protected $description = 'Check data integrity';

    public function handle()
    {
        $this->info('Checking data integrity...');

        // فحص InvoiceSaleItems بدون sale
        $itemsWithoutSale = InvoiceSaleItem::whereNull('invoice_sale_id')->count();
        $this->info("Items without sale: {$itemsWithoutSale}");

        // فحص InvoiceSales بدون invoice
        $salesWithoutInvoice = InvoiceSale::whereNull('invoice_id')->count();
        $this->info("Sales without invoice: {$salesWithoutInvoice}");

        // فحص InvoiceSaleItems مع sale غير موجود
        $itemsWithInvalidSale = InvoiceSaleItem::whereNotNull('invoice_sale_id')
            ->whereNotExists(function($query) {
                $query->select('id')
                    ->from('invoice_sales')
                    ->whereColumn('invoice_sales.id', 'invoice_sale_items.invoice_sale_id');
            })->count();
        $this->info("Items with invalid sale: {$itemsWithInvalidSale}");

        // فحص InvoiceSales مع invoice غير موجود
        $salesWithInvalidInvoice = InvoiceSale::whereNotNull('invoice_id')
            ->whereNotExists(function($query) {
                $query->select('id')
                    ->from('invoices')
                    ->whereColumn('invoices.id', 'invoice_sales.invoice_id');
            })->count();
        $this->info("Sales with invalid invoice: {$salesWithInvalidInvoice}");

        $this->info('Data integrity check completed.');
    }
}
