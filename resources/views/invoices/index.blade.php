@extends('layouts.vertical', ['title' => 'الفواتير','subTitle' => 'إدارة الفواتير'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">قائمة الفواتير</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i> شراء فاتورة جديدة
                        </a>
                        <a href="{{ route('invoices.sold') }}" class="btn btn-success">
                            <i class="ri-shopping-cart-line me-1"></i> الفواتير المباعة
                        </a>
                        <a href="{{ route('customers.index') }}" class="btn btn-info">
                            <i class="ri-group-line me-1"></i> العملاء
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>رقم الفاتورة</th>
                                        <th>المبلغ (USD)</th>
                                        <th>سعر الصرف</th>
                                        <th>المبلغ (IQD)</th>
                                        <th>الضريبة</th>
                                        <th>المجموع</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الشراء</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                        <tr>
                                            <td>{{ $invoice->invoice_number }}</td>
                                            <td>{{ number_format($invoice->amount_usd, 0) }} $</td>
                                            <td>{{ number_format($invoice->exchange_rate, 4) }}</td>
                                            <td>{{ number_format($invoice->amount_iqd, 0) }} د.ع</td>
                                            <td>{{ number_format($invoice->tax_percentage, 0) }}%</td>
                                            <td>{{ number_format($invoice->total_iqd, 0) }} د.ع</td>
                                            <td>
                                                @if($invoice->status === 'available')
                                                    <span class="badge bg-success">متاحة</span>
                                                @elseif($invoice->status === 'sold')
                                                    <span class="badge bg-danger">مباعة</span>
                                                @else
                                                    <span class="badge bg-warning">جزئية</span>
                                                @endif
                                            </td>
                                            <td>{{ $invoice->purchase_date->format('Y-m-d') }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-info" title="عرض التفاصيل">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                    @if($invoice->status !== 'sold')
                                                        <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                                                            <i class="ri-edit-line"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger" title="حذف" onclick="deleteInvoice({{ $invoice->id }}, '{{ $invoice->invoice_number }}')">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                        <a href="{{ route('invoices.sell-form', $invoice->id) }}" class="btn btn-sm btn-success" title="بيع">
                                                            <i class="ri-shopping-cart-line"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $invoices->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <iconify-icon icon="solar:document-text-broken" class="fs-48 text-muted"></iconify-icon>
                            <h5 class="mt-3 text-muted">لا توجد فواتير بعد</h5>
                            <p class="text-muted">ابدأ بشراء أول فاتورة</p>
                            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                                <i class="ri-add-line me-1"></i> شراء فاتورة جديدة
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="{{ asset('js/invoice-list.js') }}"></script>
<script src="{{ asset('js/delete-confirm.js') }}"></script>
