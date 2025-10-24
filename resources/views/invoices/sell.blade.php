@extends('layouts.vertical', ['title' => 'بيع الفاتورة','subTitle' => 'الفواتير'])

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="invoice-tax-percentage" content="{{ $invoice->tax_percentage }}">
    <meta name="invoice-total" content="{{ $invoice->total_iqd }}">
    <script>
        window.customersData = @json($customers);
    </script>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">بيع الفاتورة: {{ $invoice->invoice_number }}</h4>
                </div>
                <div class="card-body">
                    <!-- تفاصيل الفاتورة الأصلية -->
                    <div class="alert alert-info">
                        <h6>تفاصيل الفاتورة الأصلية:</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>المبلغ بالدولار:</strong> {{ number_format($invoice->amount_usd, 0) }} $
                            </div>
                            <div class="col-md-3">
                                <strong>المبلغ بالدينار:</strong> {{ number_format($invoice->amount_iqd, 0) }} د.ع
                            </div>
                            <div class="col-md-3">
                                <strong>نسبة الضريبة:</strong> {{ number_format($invoice->tax_percentage, 0) }}%
                            </div>
                            <div class="col-md-3">
                                <strong>المجموع:</strong> {{ number_format($invoice->total_iqd, 0) }} د.ع
                            </div>
                        </div>
                        <div class="mt-2">
                            <strong>المبلغ الأصلي:</strong>
                            <span class="text-info fw-bold">{{ number_format($invoice->amount_usd, 0) }} $</span>
                        </div>
                    </div>

                    <form action="{{ route('invoices.sell', $invoice->id) }}" method="POST" id="sellForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">تاريخ البيع <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="sale_date" value="{{ now()->toDateString() }}" required>
                        </div>

                        <!-- العملاء -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label">العملاء <span class="text-danger">*</span></label>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addCustomer()">
                                    <i class="ri-add-line"></i> إضافة عميل
                                </button>
                            </div>
                            <div id="customersContainer">
                                <!-- سيتم إضافة العملاء هنا -->
                            </div>
                        </div>

                        <!-- المجاميع -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">إجمالي المبلغ بالدولار</label>
                                    <input type="text" class="form-control" id="totalAmountUsd" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">إجمالي المبلغ بالدينار</label>
                                    <input type="text" class="form-control" id="totalAmountIqd" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">المجموع مع الضريبة</label>
                                    <input type="text" class="form-control" id="totalWithTax" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">الربح المتوقع</label>
                                    <input type="text" class="form-control" id="expectedProfit" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">كلمة المرور للتأكيد <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required placeholder="أدخل كلمة المرور للتأكيد">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="ri-save-line me-1"></i> حفظ عملية البيع
                            </button>
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">إضافة عميل جديد</h4>
                </div>
                <div class="card-body">
                    <form id="newCustomerForm">
                        <div class="mb-3">
                            <label class="form-label">اسم العميل <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="newCustomerName" required>
                        </div>
                        <button type="button" class="btn btn-primary w-100" onclick="addNewCustomer()">
                            <i class="ri-add-line me-1"></i> إضافة العميل
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/invoice-sell.js') }}"></script>
@endsection
