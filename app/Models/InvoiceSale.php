<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'sale_date',
        'total_amount_usd',
        'total_amount_iqd',
        'total_with_tax_iqd',
        'profit_iqd',
        'created_by',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total_amount_usd' => 'decimal:2',
        'total_amount_iqd' => 'decimal:2',
        'total_with_tax_iqd' => 'decimal:2',
        'profit_iqd' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceSaleItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function calculateProfit()
    {
        $purchaseAmount = $this->invoice->total_iqd;
        return $this->total_with_tax_iqd - $purchaseAmount;
    }
}
