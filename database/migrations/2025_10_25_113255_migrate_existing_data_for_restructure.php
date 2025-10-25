<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // نقل البيانات الموجودة:
        // 1. حساب profit_balance لكل مستثمر
        // 2. تحديث initial_investment من المعاملات
        // 3. تحديث capital current_balance ليكون فقط الإيداعات

        // تحديث المستثمرين
        $investors = DB::table('investors')->get();

        foreach ($investors as $investor) {
            // حساب profit_balance من المعاملات
            $profitBalance = DB::table('investor_transactions')
                ->where('investor_id', $investor->id)
                ->where('type', 'profit')
                ->sum('amount');

            $profitWithdrawals = DB::table('investor_transactions')
                ->where('investor_id', $investor->id)
                ->where('type', 'withdrawal')
                ->sum('amount');

            $sharedExpenses = DB::table('investor_transactions')
                ->where('investor_id', $investor->id)
                ->where('type', 'shared_expense')
                ->sum('amount');

            $profitBalance = $profitBalance - $profitWithdrawals - $sharedExpenses;

            // تحديث البيانات
            DB::table('investors')
                ->where('id', $investor->id)
                ->update([
                    'profit_balance' => $profitBalance,
                    'total_withdrawals' => $profitWithdrawals,
                ]);
        }

        // تحديث capital current_balance ليكون فقط الإيداعات
        $totalDeposits = DB::table('capital_transactions')
            ->where('type', 'deposit')
            ->sum('amount');

        DB::table('capital_accounts')
            ->update(['current_balance' => $totalDeposits]);

        // تحديث أنواع المعاملات في investor_transactions
        DB::table('investor_transactions')
            ->where('type', 'withdrawal')
            ->update(['type' => 'profit_withdrawal']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // العودة للأنواع الأصلية
        DB::table('investor_transactions')
            ->where('type', 'profit_withdrawal')
            ->update(['type' => 'withdrawal']);

        // إعادة حساب current_balance للمستثمرين
        $investors = DB::table('investors')->get();

        foreach ($investors as $investor) {
            $totalDeposits = DB::table('investor_transactions')
                ->where('investor_id', $investor->id)
                ->where('type', 'deposit')
                ->sum('amount');

            $totalWithdrawals = DB::table('investor_transactions')
                ->where('investor_id', $investor->id)
                ->whereIn('type', ['withdrawal', 'shared_expense'])
                ->sum('amount');

            $totalProfits = DB::table('investor_transactions')
                ->where('investor_id', $investor->id)
                ->where('type', 'profit')
                ->sum('amount');

            $currentBalance = $totalDeposits + $totalProfits - $totalWithdrawals;

            DB::table('investors')
                ->where('id', $investor->id)
                ->update(['current_balance' => $currentBalance]);
        }
    }
};
