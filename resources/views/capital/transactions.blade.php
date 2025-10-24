@extends('layouts.vertical', ['title' => 'سجل حركات رأس المال','subTitle' => 'تفاصيل الحركات'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">سجل حركات رأس المال</h4>
                    <a href="{{ route('capital.index') }}" class="btn btn-primary">
                        <i class="ri-arrow-left-line me-1"></i> العودة لرأس المال
                    </a>
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
                                        <th>الوصف</th>
                                        <th>الرصيد بعد العملية</th>
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
                                                @elseif($transaction->type === 'withdrawal')
                                                    <span class="badge bg-danger">سحب</span>
                                                @elseif($transaction->type === 'shared_expense')
                                                    <span class="badge bg-warning">مصروف مشترك</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->type === 'deposit')
                                                    <span class="text-success fw-bold">+{{ number_format($transaction->amount, 0) }} د.ع</span>
                                                @elseif($transaction->type === 'withdrawal')
                                                    <span class="text-danger fw-bold">-{{ number_format($transaction->amount, 0) }} د.ع</span>
                                                @elseif($transaction->type === 'shared_expense')
                                                    <span class="text-warning fw-bold">-{{ number_format($transaction->amount, 0) }} د.ع</span>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->description ?? '-' }}</td>
                                            <td>{{ number_format($transaction->balance_after, 0) }} د.ع</td>
                                            <td>{{ $transaction->createdBy->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

<script src="{{ asset('js/capital-transactions.js') }}"></script>
