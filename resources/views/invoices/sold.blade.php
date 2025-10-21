@extends('layouts.vertical', ['title' => 'الفواتير المباعة','subTitle' => 'الفواتير'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">الفواتير المباعة</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('invoices.index') }}" class="btn btn-primary">
                            <i class="ri-arrow-left-line me-1"></i> العودة للفواتير
                        </a>
                        <a href="{{ route('customers.index') }}" class="btn btn-info">
                            <i class="ri-group-line me-1"></i> العملاء
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($sales->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>رقم الفاتورة</th>
                                        <th>تاريخ البيع</th>
                                        <th>إجمالي المبلغ (USD)</th>
                                        <th>إجمالي المبلغ (IQD)</th>
                                        <th>المجموع مع الضريبة</th>
                                        <th>الربح</th>
                                        <th>عدد العملاء</th>
                                        <th>المستخدم</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sales as $sale)
                                        <tr>
                                            <td>{{ $sale->invoice->invoice_number }}</td>
                                            <td>{{ $sale->sale_date->format('Y-m-d') }}</td>
                                            <td>{{ number_format($sale->total_amount_usd, 0) }} $</td>
                                            <td>{{ number_format($sale->total_amount_iqd, 0) }} د.ع</td>
                                            <td>{{ number_format($sale->total_with_tax_iqd, 0) }} د.ع</td>
                                            <td class="text-success fw-bold">{{ number_format($sale->profit_iqd, 0) }} د.ع</td>
                                            <td>
                                                <span class="badge bg-info">{{ $sale->items->count() }}</span>
                                            </td>
                                            <td>{{ $sale->createdBy->name }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="showSaleDetails({{ $sale->id }})">
                                                    <i class="ri-eye-line"></i> تفاصيل العملاء
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $sales->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <iconify-icon icon="solar:shopping-cart-broken" class="fs-48 text-muted"></iconify-icon>
                            <h5 class="mt-3 text-muted">لا توجد فواتير مباعة بعد</h5>
                            <p class="text-muted">ابدأ ببيع الفواتير المتاحة</p>
                            <a href="{{ route('invoices.index') }}" class="btn btn-primary">
                                <i class="ri-arrow-left-line me-1"></i> عرض الفواتير المتاحة
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal تفاصيل العملاء -->
    <div class="modal fade" id="saleDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تفاصيل العملاء</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="saleDetailsContent">
                    <!-- سيتم تحميل المحتوى هنا -->
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/invoice-sold.js') }}"></script>
@endsection
