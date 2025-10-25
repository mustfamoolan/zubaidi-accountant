<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapitalAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'opening_balance',
        'current_balance',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    public function transactions()
    {
        return $this->hasMany(CapitalTransaction::class);
    }

    public function deposit($amount, $description = null, $transactionDate = null)
    {
        $this->current_balance += $amount;
        $this->save();

        return $this->transactions()->create([
            'capital_account_id' => $this->id,
            'type' => 'deposit',
            'amount' => $amount,
            'balance_after' => $this->current_balance,
            'description' => $description,
            'transaction_date' => $transactionDate ?? now()->toDateString(),
            'created_by' => auth()->id(),
        ]);
    }

    public function getBalance()
    {
        return $this->current_balance;
    }
}
