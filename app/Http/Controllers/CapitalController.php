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
        $recentTransactions = CapitalTransaction::with('createdBy')->recent(10)->get();

        // إحصائيات المستثمرين
        $totalInvestors = Investor::active()->count();
        $totalInvestments = Investor::active()->sum('initial_investment');
        $totalProfits = Investor::active()->sum('total_profits');

        return view('capital.index', compact(
            'capitalAccount',
            'totalDeposits',
            'totalWithdrawals',
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

            // إضافة اسم المستثمر للوصف إذا تم اختياره
            $description = $request->description;
            if ($request->investor_id) {
                $investor = \App\Models\Investor::find($request->investor_id);
                $description = ($description ? $description . ' - ' : '') . 'من المستثمر: ' . $investor->name;
            }

            $capitalAccount->deposit(
                $request->amount,
                $description,
                $request->transaction_date
            );

            $message = 'تم إضافة الإيداع بنجاح';
            if ($request->investor_id) {
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

            // إضافة اسم المستثمر للوصف إذا تم اختياره
            $description = $request->description;
            if ($request->investor_id) {
                $investor = \App\Models\Investor::find($request->investor_id);
                $description = ($description ? $description . ' - ' : '') . 'للمستثمر: ' . $investor->name;
            }

            $capitalAccount->withdraw(
                $request->amount,
                $description,
                $request->transaction_date
            );

            $message = 'تم السحب بنجاح';
            if ($request->investor_id) {
                $message .= ' للمستثمر: ' . $investor->name;
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}
