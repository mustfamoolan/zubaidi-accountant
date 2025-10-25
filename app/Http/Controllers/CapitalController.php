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
    public function index(Request $request)
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
        $perPage = $request->get('per_page', 10);
        $recentTransactions = CapitalTransaction::with('createdBy')
            ->orderBy('transaction_date', 'desc')
            ->paginate($perPage);

        // إحصائيات المستثمرين
        $totalInvestors = Investor::active()->count();
        $totalInvestments = Investor::active()->sum('initial_investment');
        $totalProfits = Investor::active()->sum('total_profits');
        $totalProfitBalance = Investor::active()->sum('profit_balance');

        return view('capital.index', compact(
            'capitalAccount',
            'totalDeposits',
            'totalWithdrawals',
            'totalSharedExpenses',
            'recentTransactions',
            'totalInvestors',
            'totalInvestments',
            'totalProfits',
            'totalProfitBalance'
        ));
    }

    public function transactions(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $transactions = CapitalTransaction::with('createdBy')
            ->orderBy('transaction_date', 'desc')
            ->paginate($perPage);

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
            'description' => 'required|string|max:500',
            'transaction_date' => 'nullable|date|before_or_equal:today',
        ]);

        try {
            $capitalAccount = CapitalAccount::first();
            if (!$capitalAccount) {
                return redirect()->back()->with('error', 'لا يوجد حساب رأس مال');
            }

            // التحقق من وجود رصيد كافي
            if ($capitalAccount->current_balance < $request->amount) {
                return redirect()->back()->with('error', 'الرصيد غير كافي للسحب. الرصيد الحالي: ' . number_format((float)$capitalAccount->current_balance, 0) . ' د.ع');
            }

            $capitalAccount->withdraw(
                $request->amount,
                $request->description,
                $request->transaction_date
            );

            return redirect()->back()->with('success', 'تم السحب من رأس المال بنجاح');
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

            $activeInvestors = Investor::active()->get();
            if ($activeInvestors->count() == 0) {
                return redirect()->back()->with('error', 'لا يوجد مستثمرون نشطون');
            }

            $description = $request->description ?? 'مصروف مشترك';
            $transactionDate = $request->transaction_date ?? now()->toDateString();

            // قسمة المصروف بالتساوي على جميع المستثمرين
            $amountPerInvestor = $request->amount / $activeInvestors->count();

            DB::beginTransaction();
            try {
                // خصم من كل مستثمر
                foreach ($activeInvestors as $investor) {
                    $investor->deductSharedExpense(
                        $amountPerInvestor,
                        $description . ' (حصة ' . $investor->name . ')',
                        $transactionDate
                    );
                }

                // تسجيل في capital_transactions (للإحصائيات فقط، لا يؤثر على الرصيد)
                CapitalTransaction::create([
                    'capital_account_id' => $capitalAccount->id,
                    'type' => 'shared_expense',
                    'amount' => $request->amount,
                    'balance_after' => $capitalAccount->current_balance, // يبقى كما هو
                    'description' => $description,
                    'transaction_date' => $transactionDate,
                    'created_by' => auth()->id(),
                ]);

                DB::commit();
                return redirect()->route('capital.index')->with('success', 'تم تسجيل المصروف المشترك وتوزيعه على المستثمرين بنجاح');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}
