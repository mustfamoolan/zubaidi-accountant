<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestorTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id',
        'type',
        'amount',
        'balance_after',
        'description',
        'transaction_date',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    public function scopeProfits($query)
    {
        return $query->where('type', 'profit');
    }

    public function scopeProfitWithdrawals($query)
    {
        return $query->where('type', 'profit_withdrawal');
    }

    public function scopeSharedExpenses($query)
    {
        return $query->where('type', 'shared_expense');
    }
}
