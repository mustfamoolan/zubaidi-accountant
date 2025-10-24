<?php

namespace App\Http\Controllers;

use App\Models\CapitalAccount;
use App\Models\CapitalTransaction;
use App\Models\Investor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CapitalController extends Controller
{
    public function index()
    {
        // إنشاء حساب رأس المال إذا لم يكن موجوداً
        $capitalAccount = CapitalAccount::first();
        if (!$capitalAccount) {
            $capitalAccount = CapitalAccount::create([
                'opening_balance' => 0,
                'current_balance' => 0,
            ]);
        }

        // إحصائيات رأس المال
        $totalDeposits = CapitalTransaction::deposits()->sum('amount');
        $totalWithdrawals = CapitalTransaction::withdrawals()->sum('amount');
        $totalSharedExpenses = CapitalTransaction::sharedExpenses()->sum('amount');
        $recentTransactions = CapitalTransaction::with('createdBy')->recent(10)->get();

        // إحصائيات المستثمرين
        $totalInvestors = Investor::active()->count();
        $totalInvestments = Investor::active()->sum('initial_investment');
        $totalProfits = Investor::active()->sum('total_profits');

        return view('capital.index', compact(
            'capitalAccount',
            'totalDeposits',
            'totalWithdrawals',
            'totalSharedExpenses',
            'recentTransactions',
            'totalInvestors',
            'totalInvestments',
            'totalProfits'
        ));
    }

    public function transactions()
    {
        $transactions = CapitalTransaction::with('createdBy')
            ->orderBy('transaction_date', 'desc')
            ->paginate(20);

        return view('capital.transactions', compact('transactions'));
    }

    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'investor_id' => 'nullable|exists:investors,id',
            'description' => 'nullable|string|max:500',
            'transaction_date' => 'nullable|date|before_or_equal:today',
        ]);

        try {
            $capitalAccount = CapitalAccount::first();
            if (!$capitalAccount) {
                $capitalAccount = CapitalAccount::create([
                    'opening_balance' => 0,
                    'current_balance' => 0,
                ]);
            }

            $description = $request->description;
            $investor = null;

            if ($request->investor_id) {
                $investor = Investor::find($request->investor_id);
                $description = ($description ? $description . ' - ' : '') . 'من المستثمر: ' . $investor->name;
            }

            $capitalAccount->deposit(
                $request->amount,
                $description,
                $request->transaction_date
            );

            // إضافة المعاملة لحساب المستثمر أيضاً
            if ($investor) {
                $investor->deposit(
                    $request->amount,
                    $request->description,
                    $request->transaction_date
                );
            }

            $message = 'تم إضافة الإيداع بنجاح';
            if ($investor) {
                $message .= ' من المستثمر: ' . $investor->name;
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'investor_id' => 'nullable|exists:investors,id',
            'description' => 'nullable|string|max:500',
            'transaction_date' => 'nullable|date|before_or_equal:today',
        ]);

        try {
            $capitalAccount = CapitalAccount::first();
            if (!$capitalAccount) {
                return redirect()->back()->with('error', 'لا يوجد حساب رأس مال');
            }

            $description = $request->description;
            $investor = null;

            if ($request->investor_id) {
                $investor = Investor::find($request->investor_id);
                $description = ($description ? $description . ' - ' : '') . 'للمستثمر: ' . $investor->name;
            }

            $capitalAccount->withdraw(
                $request->amount,
                $description,
                $request->transaction_date
            );

            // إضافة المعاملة لحساب المستثمر أيضاً
            if ($investor) {
                $investor->withdraw(
                    $request->amount,
                    $request->description,
                    $request->transaction_date
                );
            }

            $message = 'تم السحب بنجاح';
            if ($investor) {
                $message .= ' للمستثمر: ' . $investor->name;
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function sharedExpense(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'transaction_date' => 'nullable|date|before_or_equal:today',
        ]);

        try {
            $capitalAccount = CapitalAccount::first();
            if (!$capitalAccount) {
                return redirect()->back()->with('error', 'لا يوجد حساب رأس مال');
            }

            if ($capitalAccount->current_balance < $request->amount) {
                return redirect()->back()->with('error', 'الرصيد الحالي غير كافٍ');
            }

            $description = $request->description ?? 'مصروف مشترك';
            $transactionDate = $request->transaction_date ?? now()->toDateString();

            $capitalAccount->current_balance -= $request->amount;
            $capitalAccount->save();

            CapitalTransaction::create([
                'capital_account_id' => $capitalAccount->id,
                'type' => 'shared_expense',
                'amount' => $request->amount,
                'balance_after' => $capitalAccount->current_balance,
                'description' => $description,
                'transaction_date' => $transactionDate,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('capital.index')->with('success', 'تم تسجيل المصروف المشترك بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}
