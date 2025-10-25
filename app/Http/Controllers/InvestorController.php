<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\InvestorTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class InvestorController extends Controller
{
    public function index()
    {
        $investors = Investor::with('transactions')->paginate(20);
        $totalInvestors = Investor::active()->count();
        $totalInvestments = Investor::active()->sum('initial_investment');
        $totalProfits = Investor::active()->sum('total_profits');

        return view('investors.index', compact(
            'investors',
            'totalInvestors',
            'totalInvestments',
            'totalProfits'
        ));
    }

    public function create()
    {
        return view('investors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:investors,name',
            'notes' => 'nullable|string|max:1000',
        ]);

        $investor = Investor::create([
            'name' => $request->name,
            'initial_investment' => 0,
            'current_balance' => 0,
            'total_profits' => 0,
            'profit_balance' => 0,
            'total_withdrawals' => 0,
            'notes' => $request->notes,
            'status' => 'active', // افتراضياً نشط
        ]);

        return redirect()->route('investors.index')->with('success', 'تم إضافة المستثمر بنجاح');
    }

    public function show($id)
    {
        $investor = Investor::with(['transactions' => function($query) {
            $query->orderBy('transaction_date', 'desc');
        }])->findOrFail($id);

        $totalDeposits = $investor->transactions()->deposits()->sum('amount');
        $totalWithdrawals = $investor->total_withdrawals; // استخدام القيمة من قاعدة البيانات مباشرة
        $totalProfits = $investor->transactions()->profits()->sum('amount');

        return view('investors.show', compact(
            'investor',
            'totalDeposits',
            'totalWithdrawals',
            'totalProfits'
        ));
    }

    public function edit($id)
    {
        $investor = Investor::findOrFail($id);
        return view('investors.edit', compact('investor'));
    }

    public function update(Request $request, $id)
    {
        $investor = Investor::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:investors,name,' . $id,
            'notes' => 'nullable|string|max:1000',
        ]);

        $investor->update([
            'name' => $request->name,
            'notes' => $request->notes,
        ]);

        return redirect()->route('investors.show', $id)->with('success', 'تم تحديث بيانات المستثمر بنجاح');
    }

    public function destroy($id)
    {
        $investor = Investor::findOrFail($id);
        $investor->delete();

        return redirect()->route('investors.index')->with('success', 'تم حذف المستثمر بنجاح');
    }

    public function deposit(Request $request)
    {
        $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'transaction_date' => 'nullable|date|before_or_equal:today',
        ]);

        $investor = Investor::findOrFail($request->investor_id);

        try {
            $investor->deposit(
                $request->amount,
                $request->description,
                $request->transaction_date
            );

            // إضافة المعاملة لحساب رأس المال أيضاً
            $capitalAccount = \App\Models\CapitalAccount::first();
            if ($capitalAccount) {
                $description = ($request->description ? $request->description . ' - ' : '') . 'من المستثمر: ' . $investor->name;
                $capitalAccount->deposit(
                    $request->amount,
                    $description,
                    $request->transaction_date
                );
            }

            return redirect()->back()->with('success', 'تم إضافة الإيداع بنجاح للمستثمر: ' . $investor->name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function withdrawProfit(Request $request)
    {
        $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'transaction_date' => 'nullable|date|before_or_equal:today',
        ]);

        $investor = Investor::findOrFail($request->investor_id);

        try {
            $investor->withdrawProfit(
                $request->amount,
                $request->description,
                $request->transaction_date
            );

            // لا نخصم من رأس المال

            return redirect()->back()->with('success', 'تم سحب الربح بنجاح للمستثمر: ' . $investor->name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function addProfit(Request $request)
    {
        $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'transaction_date' => 'nullable|date|before_or_equal:today',
        ]);

        $investor = Investor::findOrFail($request->investor_id);

        try {
            $investor->addProfit(
                $request->amount,
                $request->description,
                $request->transaction_date
            );

            // لا نضيف لرأس المال

            return redirect()->back()->with('success', 'تم إضافة الربح بنجاح للمستثمر: ' . $investor->name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}
