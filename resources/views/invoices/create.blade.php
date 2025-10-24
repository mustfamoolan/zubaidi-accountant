@extends('layouts.vertical', ['title' => 'شراء فاتورة جديدة','subTitle' => 'الفواتير'])

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">شراء فاتورة جديدة</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('invoices.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">رقم الفاتورة <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('invoice_number') is-invalid @enderror"
                                           name="invoice_number" value="{{ old('invoice_number') }}" required>
                                    @error('invoice_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">تاريخ الشراء <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('purchase_date') is-invalid @enderror"
                                           name="purchase_date" value="{{ old('purchase_date', now()->toDateString()) }}" required>
                                    @error('purchase_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">مبلغ الفاتورة بالدولار <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('amount_usd') is-invalid @enderror"
                                           name="amount_usd" value="{{ old('amount_usd') }}" required>
                                    @error('amount_usd')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">سعر الصرف <span class="text-danger">*</span></label>
                                    <input type="number" step="0.0001" class="form-control @error('exchange_rate') is-invalid @enderror"
                                           name="exchange_rate" value="{{ old('exchange_rate') }}" required>
                                    @error('exchange_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">المبلغ بالدينار العراقي</label>
                                    <input type="text" class="form-control" id="amount_iqd" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">نسبة الضريبة (%) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('tax_percentage') is-invalid @enderror"
                                           name="tax_percentage" value="{{ old('tax_percentage', 0) }}" required min="0" max="100">
                                    @error('tax_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">مبلغ الضريبة بالدينار العراقي</label>
                                    <input type="text" class="form-control" id="tax_amount_iqd" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">المبلغ الكلي مع الضريبة</label>
                                    <input type="text" class="form-control" id="total_iqd" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> حفظ الفاتورة
                            </button>
                            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/invoice-create.js') }}"></script>
@endsection
