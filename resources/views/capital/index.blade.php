@extends('layouts.vertical', ['title' => 'رأس المال','subTitle' => 'إدارة رأس المال والمستثمرين'])

@section('content')
    <div class="row">
        <!-- بطاقات رأس المال -->
        <div class="col-md-3 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-6">
                            <div class="avatar-md bg-light bg-opacity-50 rounded">
                                <iconify-icon icon="solar:money-bag-broken"
                                              class="fs-32 text-primary avatar-title"></iconify-icon>
                            </div>
                            <p class="text-muted mb-2 mt-3">الرصيد الحالي</p>
                            <h3 class="text-dark fw-bold mb-0">{{ number_format($capitalAccount->current_balance, 0) }} د.ع</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-6">
                            <div class="avatar-md bg-light bg-opacity-50 rounded">
                                <iconify-icon icon="solar:arrow-down-broken"
                                              class="fs-32 text-success avatar-title"></iconify-icon>
                            </div>
                            <p class="text-muted mb-2 mt-3">إجمالي الإيداعات</p>
                            <h3 class="text-dark fw-bold mb-0">{{ number_format($totalDeposits, 0) }} د.ع</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-6">
                            <div class="avatar-md bg-light bg-opacity-50 rounded">
                                <iconify-icon icon="solar:arrow-up-broken"
                                              class="fs-32 text-danger avatar-title"></iconify-icon>
                            </div>
                            <p class="text-muted mb-2 mt-3">إجمالي السحوبات</p>
                            <h3 class="text-dark fw-bold mb-0">{{ number_format($totalWithdrawals, 0) }} د.ع</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-6">
                            <div class="avatar-md bg-light bg-opacity-50 rounded">
                                <iconify-icon icon="solar:users-group-two-rounded-broken"
                                              class="fs-32 text-info avatar-title"></iconify-icon>
                            </div>
                            <p class="text-muted mb-2 mt-3">عدد المستثمرين</p>
                            <h3 class="text-dark fw-bold mb-0">{{ $totalInvestors }}</h3>
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
                    <h5 class="card-title">عمليات رأس المال</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#depositModal">
                            <i class="ri-add-line me-1"></i> إضافة إيداع
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                            <i class="ri-subtract-line me-1"></i> إضافة سحب
                        </button>
                        <a href="{{ route('capital.transactions') }}" class="btn btn-info">
                            <i class="ri-file-list-line me-1"></i> سجل الحركات
                        </a>
                        <a href="{{ route('investors.index') }}" class="btn btn-primary">
                            <i class="ri-group-line me-1"></i> إدارة المستثمرين
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- آخر الحركات -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">آخر حركات رأس المال</h4>
                </div>
                <div class="card-body">
                    @if($recentTransactions->count() > 0)
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
                                    @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                            <td>
                                                @if($transaction->type === 'deposit')
                                                    <span class="badge bg-success">إيداع</span>
                                                @else
                                                    <span class="badge bg-danger">سحب</span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($transaction->amount, 0) }} د.ع</td>
                                            <td>{{ number_format($transaction->balance_after, 0) }} د.ع</td>
                                            <td>{{ $transaction->description ?? '-' }}</td>
                                            <td>{{ $transaction->createdBy->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

    <!-- Modal إضافة إيداع -->
    <div class="modal fade" id="depositModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة إيداع لرأس المال</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('capital.deposit') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">المبلغ</label>
                            <input type="number" step="0.01" class="form-control" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">المستثمر (اختياري)</label>
                            <select class="form-select" name="investor_id">
                                <option value="">بدون مستثمر محدد</option>
                                @foreach(\App\Models\Investor::active()->get() as $investor)
                                    <option value="{{ $investor->id }}">{{ $investor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">تاريخ العملية</label>
                            <input type="date" class="form-control" name="transaction_date" value="{{ now()->toDateString() }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">كلمة المرور للتأكيد <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required placeholder="أدخل كلمة المرور للتأكيد">
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
                    <h5 class="modal-title">إضافة سحب من رأس المال</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('capital.withdraw') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">المبلغ</label>
                            <input type="number" step="0.01" class="form-control" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">المستثمر (اختياري)</label>
                            <select class="form-select" name="investor_id">
                                <option value="">بدون مستثمر محدد</option>
                                @foreach(\App\Models\Investor::active()->get() as $investor)
                                    <option value="{{ $investor->id }}">{{ $investor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">تاريخ العملية</label>
                            <input type="date" class="form-control" name="transaction_date" value="{{ now()->toDateString() }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">كلمة المرور للتأكيد <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required placeholder="أدخل كلمة المرور للتأكيد">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">إضافة السحب</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script src="{{ asset('js/capital.js') }}"></script>
