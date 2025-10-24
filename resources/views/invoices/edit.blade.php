@extends('layouts.vertical', ['title' => 'تعديل الفاتورة','subTitle' => 'الفواتير'])

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">تعديل الفاتورة: {{ $invoice->invoice_number }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('invoices.update', $invoice->id) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">رقم الفاتورة <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number) }}" required>
                                    @error('invoice_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">تاريخ الشراء <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="purchase_date" value="{{ old('purchase_date', $invoice->purchase_date->format('Y-m-d')) }}" required>
                                    @error('purchase_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">مبلغ الفاتورة بالدولار <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" name="amount_usd" id="amountUsd" value="{{ old('amount_usd', $invoice->amount_usd) }}" required>
                                    @error('amount_usd')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">سعر الصرف <span class="text-danger">*</span></label>
                                    <input type="number" step="0.0001" class="form-control" name="exchange_rate" id="exchangeRate" value="{{ old('exchange_rate', $invoice->exchange_rate) }}" required>
                                    @error('exchange_rate')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">مبلغ الفاتورة بالدينار العراقي</label>
                                    <input type="text" class="form-control" id="amountIqd" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">نسبة الضريبة (%) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" name="tax_percentage" id="taxPercentage" value="{{ old('tax_percentage', $invoice->tax_percentage) }}" required min="0" max="100">
                                    @error('tax_percentage')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">مبلغ الضريبة بالدينار العراقي</label>
                                    <input type="text" class="form-control" id="taxAmountIqd" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">المبلغ الكلي بالدينار العراقي</label>
                                    <input type="text" class="form-control" id="totalIqd" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="ri-save-line me-1"></i> حفظ التعديلات
                            </button>
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            calculateAmounts();

            document.getElementById('amountUsd').addEventListener('input', calculateAmounts);
            document.getElementById('exchangeRate').addEventListener('input', calculateAmounts);
            document.getElementById('taxPercentage').addEventListener('input', calculateAmounts);
        });

        function calculateAmounts() {
            const amountUsd = parseFloat(document.getElementById('amountUsd').value) || 0;
            const exchangeRate = parseFloat(document.getElementById('exchangeRate').value) || 0;
            const taxPercentage = parseFloat(document.getElementById('taxPercentage').value) || 0;

            const amountIqd = amountUsd * exchangeRate;
            const taxAmountIqd = amountIqd * (taxPercentage / 100);
            const totalIqd = amountIqd + taxAmountIqd;

            document.getElementById('amountIqd').value = Math.round(amountIqd);
            document.getElementById('taxAmountIqd').value = Math.round(taxAmountIqd);
            document.getElementById('totalIqd').value = Math.round(totalIqd);
        }
    </script>
@endsection
