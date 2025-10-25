@extends('layouts.vertical', ['title' => 'سجل حركات رأس المال','subTitle' => 'تفاصيل الحركات'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">سجل حركات رأس المال</h4>
                    <div class="d-flex gap-2">
                        <button onclick="window.print()" class="btn btn-primary d-print-none">
                            <i class="ri-printer-line me-1"></i> طباعة
                        </button>
                        <a href="{{ route('capital.index') }}" class="btn btn-secondary d-print-none">
                            <i class="ri-arrow-left-line me-1"></i> العودة لرأس المال
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- رأس التقرير للطباعة فقط -->
                    <div class="print-only print-header">
                        <h1>Zubaidi Accountant - نظام المحاسبة</h1>
                        <h2>سجل حركات رأس المال</h2>
                        <div class="print-info">
                            <p>تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</p>
                            <p>طبع بواسطة: {{ auth()->user()->name }}</p>
                        </div>
                    </div>

                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>النوع</th>
                                        <th>المبلغ</th>
                                        <th>الوصف</th>
                                        <th>الرصيد بعد العملية</th>
                                        <th class="d-print-none">المستخدم</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                            <td>
                                                @if($transaction->type === 'deposit')
                                                    <span class="badge bg-success">إيداع</span>
                                                @elseif($transaction->type === 'shared_expense')
                                                    <span class="badge bg-warning">مصروف مشترك</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->type === 'deposit')
                                                    <span class="text-success fw-bold">+{{ number_format($transaction->amount, 0) }} د.ع</span>
                                                @elseif($transaction->type === 'shared_expense')
                                                    <span class="text-warning fw-bold">{{ number_format($transaction->amount, 0) }} د.ع</span>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->description ?? '-' }}</td>
                                            <td>{{ number_format($transaction->balance_after, 0) }} د.ع</td>
                                            <td class="d-print-none">{{ $transaction->createdBy->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- ذيل التقرير للطباعة فقط -->
                        <div class="print-only print-footer">
                            <p>Zubaidi Accountant © {{ date('Y') }} - جميع الحقوق محفوظة</p>
                        </div>

                        <div class="mt-3">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <iconify-icon icon="solar:document-text-broken" class="fs-48 text-muted"></iconify-icon>
                            <h5 class="mt-3 text-muted">لا توجد حركات بعد</h5>
                            <p class="text-muted">ابدأ بإضافة أول إيداع لرأس المال</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
@media print {
    /* إخفاء العناصر غير المطلوبة */
    .d-print-none,
    .sidebar,
    .topbar,
    .footer,
    .pagination,
    nav,
    .btn {
        display: none !important;
    }

    /* إعدادات الصفحة */
    @page {
        size: A4;
        margin: 2cm;
    }

    body {
        margin: 0;
        padding: 0;
        background: white;
        color: black;
        font-size: 11pt;
    }

    /* تنسيق الصفحة */
    .container-fluid,
    .row,
    .col-12 {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
    }

    .card-body {
        padding: 0 !important;
    }

    /* رأس التقرير */
    .print-header {
        text-align: center;
        margin-bottom: 30px;
        border-bottom: 2px solid #333;
        padding-bottom: 15px;
    }

    .print-header h1 {
        font-size: 18pt;
        margin: 0;
        color: #333;
    }

    .print-header h2 {
        font-size: 14pt;
        margin: 10px 0 5px;
        color: #666;
    }

    .print-header .print-info {
        font-size: 10pt;
        color: #999;
        margin-top: 5px;
    }

    /* تنسيق الجدول */
    .table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin: 0 !important;
        page-break-inside: auto;
    }

    .table thead {
        display: table-header-group;
    }

    .table thead th {
        background-color: #f8f9fa !important;
        color: #000 !important;
        font-weight: bold !important;
        border: 1px solid #dee2e6 !important;
        padding: 8px !important;
        font-size: 10pt !important;
        text-align: center !important;
    }

    .table tbody tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    .table tbody td {
        border: 1px solid #dee2e6 !important;
        padding: 6px 8px !important;
        font-size: 10pt !important;
        color: #000 !important;
    }

    /* ألوان للطباعة */
    .badge {
        border: 1px solid #000 !important;
        color: #000 !important;
        background: white !important;
        padding: 2px 6px !important;
        font-size: 9pt !important;
    }

    .text-success {
        color: #000 !important;
        font-weight: bold !important;
    }

    .text-danger {
        color: #000 !important;
        font-weight: bold !important;
    }

    .text-warning {
        color: #000 !important;
        font-weight: bold !important;
    }

    /* ذيل الصفحة */
    .print-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 9pt;
        color: #666;
        padding: 10px 0;
        border-top: 1px solid #ddd;
    }

    /* رقم الصفحة */
    .page-number:after {
        content: counter(page);
    }

    /* كسر الصفحة */
    .page-break {
        page-break-after: always;
    }
}

/* إخفاء عناصر الطباعة في العرض العادي */
.print-only {
    display: none;
}

@media print {
    .print-only {
        display: block;
    }
}
</style>

<script src="{{ asset('js/capital-transactions.js') }}"></script>
