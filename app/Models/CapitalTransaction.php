<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapitalTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'capital_account_id',
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

    public function capitalAccount()
    {
        return $this->belongsTo(CapitalAccount::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    public function scopeWithdrawals($query)
    {
        return $query->where('type', 'withdrawal');
    }

    public function scopeSharedExpenses($query)
    {
        return $query->where('type', 'shared_expense');
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('transaction_date', 'desc')->limit($limit);
    }
}
