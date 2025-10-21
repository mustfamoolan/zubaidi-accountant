<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'amount_usd',
        'exchange_rate',
        'amount_iqd',
        'tax_iqd',
        'total_iqd',
        'status',
        'purchase_date',
        'created_by',
    ];

    protected $casts = [
        'amount_usd' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'amount_iqd' => 'decimal:2',
        'tax_iqd' => 'decimal:2',
        'total_iqd' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sales()
    {
        return $this->hasMany(InvoiceSale::class);
    }

    public function getAvailableAmount()
    {
        $totalSold = $this->sales()->sum('total_amount_usd');
        return $this->amount_usd - $totalSold;
    }

    public function getTotalProfit()
    {
        return $this->sales()->sum('profit_iqd');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }

    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }
}
