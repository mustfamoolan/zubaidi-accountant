<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function saleItems()
    {
        return $this->hasMany(InvoiceSaleItem::class, 'customer_id');
    }

    public function invoices()
    {
        return $this->hasManyThrough(
            Invoice::class,
            InvoiceSaleItem::class,
            'customer_id',
            'id',
            'id',
            'invoice_sale_id'
        )->join('invoice_sales', 'invoice_sales.id', '=', 'invoice_sale_items.invoice_sale_id')
         ->join('invoices', 'invoices.id', '=', 'invoice_sales.invoice_id');
    }

    public function getTotalPurchases()
    {
        return $this->saleItems()->count();
    }

    public function getTotalSpent()
    {
        return $this->saleItems()->sum('amount_iqd');
    }
}
