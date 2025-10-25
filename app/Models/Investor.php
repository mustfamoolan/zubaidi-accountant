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
        'profit_balance',
        'total_withdrawals',
        'notes',
        'status',
    ];

    protected $casts = [
        'initial_investment' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'total_profits' => 'decimal:2',
        'profit_balance' => 'decimal:2',
        'total_withdrawals' => 'decimal:2',
    ];

    public function transactions()
    {
        return $this->hasMany(InvestorTransaction::class);
    }

    public function deposit($amount, $description = null, $transactionDate = null)
    {
        $this->initial_investment += $amount;
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

    public function withdrawProfit($amount, $description = null, $transactionDate = null)
    {
        if ($this->profit_balance < $amount) {
            throw new \Exception('رصيد الأرباح غير كافي للسحب');
        }

        $this->profit_balance -= $amount;
        $this->total_withdrawals += $amount;
        $this->current_balance -= $amount;
        $this->save();

        return $this->transactions()->create([
            'type' => 'profit_withdrawal',
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
            $oldProfitBalance = $this->profit_balance;

            $this->current_balance += $amount;
            $this->total_profits += $amount;
            $this->profit_balance += $amount;
            $this->save();

            // تسجيل إضافة الربح
            $this->transactions()->create([
                'type' => 'profit',
                'amount' => $amount,
                'balance_after' => $this->current_balance,
                'description' => $description,
                'transaction_date' => $transactionDate ?? now()->toDateString(),
                'created_by' => auth()->id(),
            ]);

            // إذا كان هناك دين وتم تسويته، أضف معاملة لتوضيح تسوية الدين
            if ($oldProfitBalance < 0 && $this->profit_balance >= 0) {
                $debtAmount = abs($oldProfitBalance);
                $remainingAmount = $amount - $debtAmount;

                $this->transactions()->create([
                    'type' => 'profit',
                    'amount' => 0, // مبلغ صفر لأن التسوية مُسجلة بالفعل
                    'balance_after' => $this->current_balance,
                    'description' => 'تم تسوية الدين: ' . number_format($debtAmount, 0) . ' د.ع، المتبقي: ' . number_format($remainingAmount, 0) . ' د.ع',
                    'transaction_date' => $transactionDate ?? now()->toDateString(),
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deductSharedExpense($amount, $description, $transactionDate)
    {
        $oldProfitBalance = $this->profit_balance;

        if ($this->profit_balance < $amount) {
            // السماح بالرصيد السالب (دين)
            $this->profit_balance -= $amount;
            $this->current_balance -= $amount;
        } else {
            $this->profit_balance -= $amount;
            $this->current_balance -= $amount;
        }

        // إضافة المبلغ لإجمالي السحوبات
        $this->total_withdrawals += $amount;

        $this->save();

        // تسجيل المعاملة في سجل الحركات
        $transaction = $this->transactions()->create([
            'type' => 'shared_expense',
            'amount' => $amount,
            'balance_after' => $this->current_balance,
            'description' => $description,
            'transaction_date' => $transactionDate,
            'created_by' => auth()->id(),
        ]);

        // إذا أصبح هناك دين، أضف معاملة إضافية لتوضيح الدين
        if ($oldProfitBalance >= 0 && $this->profit_balance < 0) {
            $this->transactions()->create([
                'type' => 'shared_expense',
                'amount' => 0, // مبلغ صفر لأن الدين مُسجل بالفعل
                'balance_after' => $this->current_balance,
                'description' => 'دين على المستثمر: ' . number_format(abs($this->profit_balance), 0) . ' د.ع',
                'transaction_date' => $transactionDate,
                'created_by' => auth()->id(),
            ]);
        }

        return $transaction;
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
