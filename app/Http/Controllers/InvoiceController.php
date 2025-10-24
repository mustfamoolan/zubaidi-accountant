<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\InvoiceSale;
use App\Models\InvoiceSaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('createdBy')
            ->orderBy('purchase_date', 'desc')
            ->paginate(20);

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        return view('invoices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'amount_usd' => 'required|numeric|min:0.01',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'purchase_date' => 'required|date',
            'password' => 'required|string',
        ]);

        // التحقق من كلمة المرور
        if (!Hash::check($request->password, auth()->user()->password)) {
            return redirect()->back()->with('error', 'كلمة المرور غير صحيحة');
        }

        DB::beginTransaction();
        try {
            $amountIqd = $request->amount_usd * $request->exchange_rate;
            $taxAmount = $amountIqd * ($request->tax_percentage / 100);
            $totalIqd = $amountIqd + $taxAmount;

            $invoice = Invoice::create([
                'invoice_number' => $request->invoice_number,
                'amount_usd' => $request->amount_usd,
                'exchange_rate' => $request->exchange_rate,
                'amount_iqd' => $amountIqd,
                'tax_percentage' => $request->tax_percentage,
                'total_iqd' => $totalIqd,
                'status' => 'available',
                'purchase_date' => $request->purchase_date,
                'created_by' => auth()->id(),
            ]);

            DB::commit();
            return redirect()->route('invoices.index')->with('success', 'تم إضافة الفاتورة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $invoice = Invoice::with(['createdBy', 'sales.items.customer'])->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }

    public function sellForm($id)
    {
        $invoice = Invoice::findOrFail($id);
        $customers = Customer::orderBy('name')->get();

        return view('invoices.sell', compact('invoice', 'customers'));
    }

    public function sell(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $request->validate([
            'customers' => 'required|array|min:1',
            'customers.*.customer_id' => 'required|exists:customers,id',
            'customers.*.amount_usd' => 'required|numeric|min:0.01',
            'customers.*.exchange_rate' => 'required|numeric|min:0.0001',
            'sale_date' => 'required|date',
            'password' => 'required|string',
        ]);

        // التحقق من كلمة المرور
        if (!Hash::check($request->password, auth()->user()->password)) {
            return redirect()->back()->with('error', 'كلمة المرور غير صحيحة');
        }

        // لا نتحقق من المبلغ المباع - يمكن البيع بأي مبلغ
        $totalAmountUsd = collect($request->customers)->sum('amount_usd');

        DB::beginTransaction();
        try {
            $totalAmountIqd = 0;
            $totalWithTaxIqd = 0;

            // حساب المجاميع
            foreach ($request->customers as $customerData) {
                $amountIqd = $customerData['amount_usd'] * $customerData['exchange_rate'];
                $totalAmountIqd += $amountIqd;
            }

            $totalWithTaxIqd = $totalAmountIqd + ($totalAmountIqd * ($invoice->tax_percentage / 100));
            // الربح = إجمالي المبلغ المباع - المبلغ الأصلي للفاتورة
            $profitIqd = $totalWithTaxIqd - $invoice->total_iqd;

            // إنشاء عملية البيع
            $sale = InvoiceSale::create([
                'invoice_id' => $invoice->id,
                'sale_date' => $request->sale_date,
                'total_amount_usd' => $totalAmountUsd,
                'total_amount_iqd' => $totalAmountIqd,
                'total_with_tax_iqd' => $totalWithTaxIqd,
                'profit_iqd' => $profitIqd,
                'created_by' => auth()->id(),
            ]);

            // إنشاء تفاصيل البيع لكل عميل
            foreach ($request->customers as $customerData) {
                $amountIqd = $customerData['amount_usd'] * $customerData['exchange_rate'];

                InvoiceSaleItem::create([
                    'invoice_sale_id' => $sale->id,
                    'customer_id' => $customerData['customer_id'],
                    'amount_usd' => $customerData['amount_usd'],
                    'exchange_rate' => $customerData['exchange_rate'],
                    'amount_iqd' => $amountIqd,
                ]);
            }

            // تحديث حالة الفاتورة بناءً على المبلغ المباع
            if ($totalAmountUsd >= $invoice->amount_usd) {
                $invoice->status = 'sold';
            } else {
                $invoice->status = 'partial';
            }
            $invoice->save();

            DB::commit();
            return redirect()->route('invoices.show', $invoice->id)->with('success', 'تم بيع الفاتورة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function sold()
    {
        $sales = InvoiceSale::with(['invoice', 'items.customer', 'createdBy'])
            ->orderBy('sale_date', 'desc')
            ->paginate(20);

        return view('invoices.sold', compact('sales'));
    }

    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);

        // لا يمكن تعديل فاتورة مباعة بالكامل
        if ($invoice->status === 'sold') {
            return redirect()->route('invoices.index')->with('error', 'لا يمكن تعديل فاتورة مباعة بالكامل');
        }

        return view('invoices.edit', compact('invoice'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // لا يمكن تعديل فاتورة مباعة بالكامل
        if ($invoice->status === 'sold') {
            return redirect()->route('invoices.index')->with('error', 'لا يمكن تعديل فاتورة مباعة بالكامل');
        }

        $request->validate([
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number,' . $id,
            'amount_usd' => 'required|numeric|min:0.01',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'purchase_date' => 'required|date',
            'password' => 'required|string',
        ]);

        // التحقق من كلمة المرور
        if (!Hash::check($request->password, auth()->user()->password)) {
            return redirect()->back()->with('error', 'كلمة المرور غير صحيحة');
        }

        // حساب المبلغ بالدينار العراقي
        $amountIqd = $request->amount_usd * $request->exchange_rate;
        $taxAmount = $amountIqd * ($request->tax_percentage / 100);
        $totalIqd = $amountIqd + $taxAmount;

        $invoice->update([
            'invoice_number' => $request->invoice_number,
            'amount_usd' => $request->amount_usd,
            'exchange_rate' => $request->exchange_rate,
            'amount_iqd' => $amountIqd,
            'tax_percentage' => $request->tax_percentage,
            'total_iqd' => $totalIqd,
            'purchase_date' => $request->purchase_date,
        ]);

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'تم تحديث الفاتورة بنجاح');
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        // لا يمكن حذف فاتورة لها مبيعات
        if ($invoice->sales()->count() > 0) {
            return redirect()->route('invoices.index')->with('error', 'لا يمكن حذف فاتورة لها مبيعات');
        }

        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'تم حذف الفاتورة بنجاح');
    }

    public function getSaleCustomers($saleId)
    {
        $sale = InvoiceSale::with(['invoice', 'items.customer'])->findOrFail($saleId);

        $customers = $sale->items->map(function($item) {
            return [
                'name' => $item->customer->name,
                'amount_usd' => $item->amount_usd,
                'exchange_rate' => $item->exchange_rate,
                'amount_iqd' => $item->amount_iqd,
            ];
        });

        return response()->json([
            'success' => true,
            'sale' => [
                'invoice_number' => $sale->invoice->invoice_number,
                'sale_date' => $sale->sale_date->format('Y-m-d'),
                'total_amount_usd' => $sale->total_amount_usd,
                'total_amount_iqd' => $sale->total_amount_iqd,
                'total_with_tax_iqd' => $sale->total_with_tax_iqd,
                'profit_iqd' => $sale->profit_iqd,
            ],
            'customers' => $customers
        ]);
    }
}
