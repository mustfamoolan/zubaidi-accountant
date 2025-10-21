<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Investor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'initial_investment',
        'current_balance',
        'total_profits',
        'notes',
        'status',
    ];

    protected $casts = [
        'initial_investment' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'total_profits' => 'decimal:2',
    ];

    public function transactions()
    {
        return $this->hasMany(InvestorTransaction::class);
    }

    public function deposit($amount, $description = null, $transactionDate = null)
    {
        $this->current_balance += $amount;
        $this->save();

        return $this->transactions()->create([
            'type' => 'deposit',
            'amount' => $amount,
            'balance_after' => $this->current_balance,
            'description' => $description,
            'transaction_date' => $transactionDate ?? now()->toDateString(),
            'created_by' => auth()->id(),
        ]);
    }

    public function withdraw($amount, $description = null, $transactionDate = null)
    {
        if ($this->current_balance < $amount) {
            throw new \Exception('الرصيد غير كافي للسحب');
        }

        $this->current_balance -= $amount;
        $this->save();

        return $this->transactions()->create([
            'type' => 'withdrawal',
            'amount' => $amount,
            'balance_after' => $this->current_balance,
            'description' => $description,
            'transaction_date' => $transactionDate ?? now()->toDateString(),
            'created_by' => auth()->id(),
        ]);
    }

    public function addProfit($amount, $description = null, $transactionDate = null)
    {
        DB::beginTransaction();
        try {
            $this->current_balance += $amount;
            $this->total_profits += $amount;
            $this->save();

            $this->transactions()->create([
                'type' => 'profit',
                'amount' => $amount,
                'balance_after' => $this->current_balance,
                'description' => $description,
                'transaction_date' => $transactionDate ?? now()->toDateString(),
                'created_by' => auth()->id(),
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getBalance()
    {
        return $this->current_balance;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
