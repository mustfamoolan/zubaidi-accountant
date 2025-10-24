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
        'tax_percentage',
        'total_iqd',
        'status',
        'purchase_date',
        'created_by',
    ];

    protected $casts = [
        'amount_usd' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'amount_iqd' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
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

    public function getSoldAmountUsd()
    {
        return $this->sales()->sum('total_amount_usd');
    }

    public function getAvailableAmountUsd()
    {
        return $this->amount_usd - $this->getSoldAmountUsd();
    }

    public function getAvailableAmountIqd()
    {
        $availableUsd = $this->getAvailableAmountUsd();
        return $availableUsd * $this->exchange_rate;
    }

    public function getSoldPercentage()
    {
        if ($this->amount_usd == 0) return 0;
        return ($this->getSoldAmountUsd() / $this->amount_usd) * 100;
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
