@extends('layouts.vertical', ['title' => 'تفاصيل المستثمر: ' . $investor->name,'subTitle' => 'إدارة المستثمرين'])

@section('content')
    <div class="row">
        <!-- معلومات المستثمر -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">معلومات المستثمر</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title bg-primary text-white rounded-circle fs-24">
                                {{ substr($investor->name, 0, 1) }}
                            </span>
                        </div>
                        <h5>{{ $investor->name }}</h5>
                        <p class="text-muted">
                            @if($investor->status === 'active')
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-secondary">غير نشط</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">الرصيد الحالي:</label>
                        <p class="mb-0 text-primary fw-bold">{{ number_format($investor->current_balance, 0) }} د.ع</p>
                    </div>

                    @if($investor->notes)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ملاحظات:</label>
                            <p class="mb-0">{{ $investor->notes }}</p>
                        </div>
                    @endif

                    @if(auth()->user()->role === 'admin')
                        <div class="d-grid gap-2">
                            <a href="{{ route('investors.edit', $investor->id) }}" class="btn btn-warning">
                                <i class="ri-edit-line me-1"></i> تعديل البيانات
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- إحصائيات الحركات -->
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="avatar-md bg-light bg-opacity-50 rounded mx-auto mb-3">
                                <iconify-icon icon="solar:wallet-money-broken"
                                              class="fs-32 text-info avatar-title"></iconify-icon>
                            </div>
                            <h5 class="text-info">{{ number_format($investor->current_balance, 0) }} د.ع</h5>
                            <p class="text-muted mb-0">الرصيد المتبقي القابل للسحب</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="avatar-md bg-light bg-opacity-50 rounded mx-auto mb-3">
                                <iconify-icon icon="solar:arrow-down-broken"
                                              class="fs-32 text-success avatar-title"></iconify-icon>
                            </div>
                            <h5 class="text-success">{{ number_format($totalDeposits, 0) }} د.ع</h5>
                            <p class="text-muted mb-0">إجمالي الإيداعات</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="avatar-md bg-light bg-opacity-50 rounded mx-auto mb-3">
                                <iconify-icon icon="solar:arrow-up-broken"
                                              class="fs-32 text-danger avatar-title"></iconify-icon>
                            </div>
                            <h5 class="text-danger">{{ number_format($totalWithdrawals, 0) }} د.ع</h5>
                            <p class="text-muted mb-0">إجمالي سحوبات الأرباح</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="avatar-md bg-light bg-opacity-50 rounded mx-auto mb-3">
                                <iconify-icon icon="solar:chart-broken"
                                              class="fs-32 text-info avatar-title"></iconify-icon>
                            </div>
                            <h5 class="text-info">{{ number_format($totalProfits, 0) }} د.ع</h5>
                            <p class="text-muted mb-0">إجمالي الأرباح</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- أزرار العمليات -->
    @if(auth()->user()->role === 'admin')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">عمليات المستثمر</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#depositModal">
                            <i class="ri-add-line me-1"></i> إضافة إيداع
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                            <i class="ri-subtract-line me-1"></i> سحب
                        </button>
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#profitModal">
                            <i class="ri-trophy-line me-1"></i> إضافة ربح
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- جدول حركات المستثمر -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">حركات المستثمر</h4>
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 text-muted">عرض:</label>
                        <select class="form-select form-select-sm per-page-select" onchange="changePerPage(this.value)">
                            <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ request('per_page', 10) == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        </select>
                        <span class="text-muted small">لكل صفحة</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>النوع</th>
                                        <th>المبلغ</th>
                                        <th>الرصيد بعد العملية</th>
                                        <th>الوصف</th>
                                        <th>المستخدم</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                            <td>
                                                @if($transaction->type === 'deposit')
                                                    <span class="badge bg-success">إيداع</span>
                                                @elseif($transaction->type === 'profit')
                                                    <span class="badge bg-info">ربح</span>
                                                @elseif($transaction->type === 'profit_withdrawal')
                                                    <span class="badge bg-danger">سحب ربح</span>
                                                @elseif($transaction->type === 'shared_expense')
                                                    @if(str_contains($transaction->description, 'دين على المستثمر'))
                                                        <span class="badge bg-warning">دين</span>
                                                    @else
                                                        <span class="badge bg-warning">مصروف مشترك</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->type === 'deposit')
                                                    <span class="text-success fw-bold">+{{ number_format($transaction->amount, 0) }} د.ع</span>
                                                @elseif($transaction->type === 'profit')
                                                    @if($transaction->amount == 0)
                                                        <span class="text-info fw-bold">تسوية</span>
                                                    @else
                                                        <span class="text-info fw-bold">+{{ number_format($transaction->amount, 0) }} د.ع</span>
                                                    @endif
                                                @elseif($transaction->type === 'profit_withdrawal')
                                                    <span class="text-danger fw-bold">-{{ number_format($transaction->amount, 0) }} د.ع</span>
                                                @elseif($transaction->type === 'shared_expense')
                                                    @if($transaction->amount == 0)
                                                        <span class="text-warning fw-bold">دين</span>
                                                    @else
                                                        <span class="text-warning fw-bold">-{{ number_format($transaction->amount, 0) }} د.ع (حصة من مصروف مشترك)</span>
                                                    @endif
                                                @endif
                                            </td>
                                                <td>{{ number_format($transaction->balance_after, 0) }} د.ع</td>
                                            <td>{{ $transaction->description ?? '-' }}</td>
                                            <td>{{ $transaction->createdBy->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                عرض {{ $transactions->firstItem() ?? 0 }} إلى {{ $transactions->lastItem() ?? 0 }} من {{ $transactions->total() }} حركة
                            </div>
                            <div>
                                {{ $transactions->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <iconify-icon icon="solar:document-text-broken" class="fs-48 text-muted"></iconify-icon>
                            <h5 class="mt-3 text-muted">لا توجد حركات بعد</h5>
                            <p class="text-muted">ابدأ بإضافة أول عملية للمستثمر</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modals للعمليات -->
    @if(auth()->user()->role === 'admin')
    <!-- Modal إضافة إيداع -->
    <div class="modal fade" id="depositModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة إيداع للمستثمر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('investors.deposit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="investor_id" value="{{ $investor->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">المبلغ</label>
                            <input type="text" class="form-control" name="amount" required placeholder="أدخل المبلغ">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                            <div class="mb-3">
                                <label class="form-label">تاريخ العملية</label>
                                <input type="date" class="form-control" name="transaction_date" value="{{ now()->toDateString() }}">
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">إضافة الإيداع</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal إضافة سحب -->
    <div class="modal fade" id="withdrawModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">سحب من المستثمر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('investors.withdraw-profit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="investor_id" value="{{ $investor->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">المبلغ</label>
                            <input type="text" class="form-control" name="amount" required
                                   placeholder="الرصيد المتاح: {{ number_format($investor->current_balance, 0) }} د.ع">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                            <div class="mb-3">
                                <label class="form-label">تاريخ العملية</label>
                                <input type="date" class="form-control" name="transaction_date" value="{{ now()->toDateString() }}">
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">سحب</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal إضافة ربح -->
    <div class="modal fade" id="profitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة ربح للمستثمر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('investors.profit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="investor_id" value="{{ $investor->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">مبلغ الربح</label>
                            <input type="text" class="form-control" name="amount" required placeholder="أدخل المبلغ">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="مثال: ربح شهري - ديسمبر 2024"></textarea>
                        </div>
                            <div class="mb-3">
                                <label class="form-label">تاريخ العملية</label>
                                <input type="date" class="form-control" name="transaction_date" value="{{ now()->toDateString() }}">
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-info">إضافة الربح</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection

<style>
/* تنسيق الـ pagination */
.pagination {
    margin: 0;
    gap: 2px;
}

.pagination .page-link {
    border-radius: 6px;
    border: 1px solid #dee2e6;
    color: #495057;
    padding: 8px 12px;
    margin: 0 1px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.pagination .page-link:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
    color: #495057;
}

.pagination .page-item.active .page-link {
    background-color: #4F46E5;
    border-color: #4F46E5;
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

.pagination .page-item:first-child .page-link {
    border-top-left-radius: 6px;
    border-bottom-left-radius: 6px;
}

.pagination .page-item:last-child .page-link {
    border-top-right-radius: 6px;
    border-bottom-right-radius: 6px;
}

/* تنسيق خيارات عدد العناصر المعروضة */
.per-page-select {
    width: 80px !important;
    font-size: 13px;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
}

.per-page-select:focus {
    border-color: #4F46E5;
    box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
}
</style>

<script>
function changePerPage(perPage) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // إعادة تعيين الصفحة إلى الأولى
    window.location.href = url.toString();
}
</script>

<script src="{{ asset('js/investor-details.js') }}"></script>
