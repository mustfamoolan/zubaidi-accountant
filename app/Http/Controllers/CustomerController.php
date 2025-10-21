<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\InvoiceSaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount('saleItems')
            ->withSum('saleItems', 'amount_iqd')
            ->orderBy('name')
            ->paginate(20);

        return view('customers.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);

        $purchases = InvoiceSaleItem::with(['sale.invoice', 'sale.createdBy'])
            ->where('customer_id', $id)
            ->whereHas('sale') // التأكد من وجود sale
            ->whereHas('sale.invoice') // التأكد من وجود invoice
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customers.show', compact('customer', 'purchases'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $customer->update([
            'name' => $request->name,
        ]);

        return redirect()->route('customers.show', $customer->id)->with('success', 'تم تحديث العميل بنجاح');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        // لا يمكن حذف عميل له مشتريات
        if ($customer->saleItems()->count() > 0) {
            return redirect()->route('customers.index')->with('error', 'لا يمكن حذف عميل له مشتريات');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'تم حذف العميل بنجاح');
    }
}
