@extends('layouts.vertical', ['title' => 'تفاصيل الفاتورة','subTitle' => 'الفواتير'])

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">تفاصيل الفاتورة: {{ $invoice->invoice_number }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">رقم الفاتورة:</label>
                                <p class="mb-0">{{ $invoice->invoice_number }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">تاريخ الشراء:</label>
                                <p class="mb-0">{{ $invoice->purchase_date->format('Y-m-d') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">المبلغ بالدولار:</label>
                                <p class="mb-0">{{ number_format($invoice->amount_usd, 0) }} $</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">سعر الصرف:</label>
                                <p class="mb-0">{{ number_format($invoice->exchange_rate, 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">المبلغ بالدينار:</label>
                                <p class="mb-0">{{ number_format($invoice->amount_iqd, 0) }} د.ع</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">نسبة الضريبة:</label>
                                <p class="mb-0">{{ number_format($invoice->tax_percentage, 1) }}%</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">مبلغ الضريبة بالدينار العراقي:</label>
                                <p class="mb-0 text-warning fw-bold">{{ number_format($invoice->amount_iqd * ($invoice->tax_percentage / 100), 0) }} د.ع</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">المبلغ الكلي مع الضريبة:</label>
                                <p class="mb-0 text-primary fw-bold">{{ number_format($invoice->total_iqd, 0) }} د.ع</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">المبلغ المباع:</label>
                                <p class="mb-0">{{ number_format($invoice->getSoldAmountUsd(), 0) }} $</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">المبلغ المتبقي:</label>
                                <p class="mb-0">{{ number_format($invoice->getAvailableAmountUsd(), 0) }} $</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">المبلغ الكلي:</label>
                                <p class="mb-0 text-primary fw-bold">{{ number_format($invoice->total_iqd, 0) }} د.ع</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">الحالة:</label>
                                <p class="mb-0">
                                    @if($invoice->status === 'available')
                                        <span class="badge bg-success">متاحة</span>
                                    @elseif($invoice->status === 'sold')
                                        <span class="badge bg-danger">مباعة</span>
                                    @else
                                        <span class="badge bg-warning">جزئية</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($invoice->status !== 'available')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">المبلغ المتاح للبيع:</label>
                            <p class="mb-0 text-info fw-bold">{{ number_format($invoice->getAvailableAmount(), 0) }} $</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">إجمالي الربح:</label>
                            <p class="mb-0 text-success fw-bold">{{ number_format($invoice->getTotalProfit(), 0) }} د.ع</p>
                        </div>
                    @endif
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
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line me-1"></i> العودة للقائمة
                        </a>
                        @if($invoice->status !== 'sold')
                            <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-warning">
                                <i class="ri-edit-line me-1"></i> تعديل الفاتورة
                            </a>
                            <button type="button" class="btn btn-danger" onclick="deleteInvoice({{ $invoice->id }}, '{{ $invoice->invoice_number }}')">
                                <i class="ri-delete-bin-line me-1"></i> حذف الفاتورة
                            </button>
                            <a href="{{ route('invoices.sell-form', $invoice->id) }}" class="btn btn-success">
                                <i class="ri-shopping-cart-line me-1"></i> بيع الفاتورة
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($invoice->sales->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">عمليات البيع</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>تاريخ البيع</th>
                                        <th>إجمالي المبلغ (USD)</th>
                                        <th>إجمالي المبلغ (IQD)</th>
                                        <th>المجموع مع الضريبة</th>
                                        <th>الربح</th>
                                        <th>المستخدم</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->sales as $sale)
                                        <tr>
                                            <td>{{ $sale->sale_date->format('Y-m-d') }}</td>
                                            <td>{{ number_format($sale->total_amount_usd, 0) }} $</td>
                                            <td>{{ number_format($sale->total_amount_iqd, 0) }} د.ع</td>
                                            <td>{{ number_format($sale->total_with_tax_iqd, 0) }} د.ع</td>
                                            <td class="text-success fw-bold">{{ number_format($sale->profit_iqd, 0) }} د.ع</td>
                                            <td>{{ $sale->createdBy->name }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="showSaleDetails({{ $sale->id }})">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal تفاصيل عملية البيع -->
    <div class="modal fade" id="saleDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تفاصيل عملية البيع</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="saleDetailsContent">
                    <!-- سيتم تحميل المحتوى هنا -->
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/invoice-details.js') }}"></script>
    <script src="{{ asset('js/delete-confirm.js') }}"></script>
@endsection
