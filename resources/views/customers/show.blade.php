@extends('layouts.vertical', ['title' => 'تفاصيل العميل','subTitle' => 'العملاء'])

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">تفاصيل العميل: {{ $customer->name }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">اسم العميل:</label>
                                <p class="mb-0">{{ $customer->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">تاريخ الإضافة:</label>
                                <p class="mb-0">{{ $customer->created_at->format('Y-m-d') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">عدد الفواتير المشتراة:</label>
                                <p class="mb-0 text-primary fw-bold">{{ $customer->getTotalPurchases() }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">إجمالي المشتريات:</label>
                                <p class="mb-0 text-success fw-bold">{{ number_format($customer->getTotalSpent(), 0) }} د.ع</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">الإجراءات</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line me-1"></i> العودة للقائمة
                        </a>
                        <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning">
                            <i class="ri-edit-line me-1"></i> تعديل العميل
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteCustomer({{ $customer->id }}, '{{ $customer->name }}')">
                            <i class="ri-delete-bin-line me-1"></i> حذف العميل
                        </button>
                        <a href="{{ route('invoices.index') }}" class="btn btn-primary">
                            <i class="ri-file-list-line me-1"></i> عرض الفواتير
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($purchases->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">سجل مشتريات العميل</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>رقم الفاتورة</th>
                                        <th>تاريخ البيع</th>
                                        <th>المبلغ بالدولار</th>
                                        <th>سعر الصرف</th>
                                        <th>المبلغ بالدينار</th>
                                        <th>المستخدم</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchases as $purchase)
                                        @if($purchase->sale && $purchase->sale->invoice)
                                            <tr>
                                                <td>{{ $purchase->sale->invoice->invoice_number }}</td>
                                                <td>{{ $purchase->sale->sale_date->format('Y-m-d') }}</td>
                                                <td>{{ number_format($purchase->amount_usd, 0) }} $</td>
                                                <td>{{ number_format($purchase->exchange_rate, 0) }}</td>
                                                <td class="text-success fw-bold">{{ number_format($purchase->amount_iqd, 0) }} د.ع</td>
                                                <td>{{ $purchase->sale->createdBy->name ?? 'غير محدد' }}</td>
                                                <td>
                                                    <a href="{{ route('invoices.show', $purchase->sale->invoice->id) }}" class="btn btn-sm btn-info">
                                                        <i class="ri-eye-line"></i> عرض الفاتورة
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $purchases->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-4">
                        <iconify-icon icon="solar:file-broken" class="fs-48 text-muted"></iconify-icon>
                        <h5 class="mt-3 text-muted">لم يشتر هذا العميل أي فواتير بعد</h5>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

<script src="{{ asset('js/delete-confirm.js') }}"></script>

<script src="{{ asset('js/customer-details.js') }}"></script>
