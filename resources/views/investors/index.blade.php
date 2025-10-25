@extends('layouts.vertical', ['title' => 'المستثمرين','subTitle' => 'إدارة المستثمرين'])

@section('content')
    <div class="row">
        <!-- إحصائيات المستثمرين -->
        <div class="col-md-4 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-6">
                            <div class="avatar-md bg-light bg-opacity-50 rounded">
                                <iconify-icon icon="solar:users-group-two-rounded-broken"
                                              class="fs-32 text-primary avatar-title"></iconify-icon>
                            </div>
                            <p class="text-muted mb-2 mt-3">عدد المستثمرين</p>
                            <h3 class="text-dark fw-bold mb-0">{{ $totalInvestors }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-6">
                            <div class="avatar-md bg-light bg-opacity-50 rounded">
                                <iconify-icon icon="solar:money-bag-broken"
                                              class="fs-32 text-success avatar-title"></iconify-icon>
                            </div>
                            <p class="text-muted mb-2 mt-3">إجمالي الاستثمارات</p>
                            <h3 class="text-dark fw-bold mb-0">{{ number_format($totalInvestments, 0) }} د.ع</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-6">
                            <div class="avatar-md bg-light bg-opacity-50 rounded">
                                <iconify-icon icon="solar:chart-broken"
                                              class="fs-32 text-info avatar-title"></iconify-icon>
                            </div>
                            <p class="text-muted mb-2 mt-3">إجمالي الأرباح</p>
                            <h3 class="text-dark fw-bold mb-0">{{ number_format($totalProfits, 0) }} د.ع</h3>
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
                    <h5 class="card-title">عمليات المستثمرين</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('investors.create') }}" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i> إضافة مستثمر جديد
                        </a>
                        <a href="{{ route('capital.index') }}" class="btn btn-info">
                            <i class="ri-money-dollar-circle-line me-1"></i> رأس المال
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- جدول المستثمرين -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">قائمة المستثمرين</h4>
                </div>
                <div class="card-body">
                    @if($investors->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>الاسم</th>
                                            <th>الرصيد الحالي</th>
                                            <th>إجمالي الأرباح</th>
                                            <th>الرصيد المتاح للسحب</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                <tbody>
                                    @foreach($investors as $investor)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        <span class="avatar-title bg-primary text-white rounded-circle">
                                                            {{ substr($investor->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $investor->name }}</h6>
                                                        @if($investor->notes)
                                                            <small class="text-muted">{{ Str::limit($investor->notes, 30) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ number_format($investor->current_balance, 0) }} د.ع</td>
                                            <td>{{ number_format($investor->total_profits, 0) }} د.ع</td>
                                            <td>
                                                @if($investor->profit_balance >= 0)
                                                    <span class="text-success fw-bold">{{ number_format($investor->profit_balance, 0) }} د.ع</span>
                                                @else
                                                    <span class="text-danger fw-bold">دين: {{ number_format(abs($investor->profit_balance), 0) }} د.ع</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($investor->status === 'active')
                                                    <span class="badge bg-success">نشط</span>
                                                @else
                                                    <span class="badge bg-secondary">غير نشط</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('investors.show', $investor->id) }}" class="btn btn-sm btn-info">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                    @if(auth()->user()->role === 'admin')
                                                        <a href="{{ route('investors.edit', $investor->id) }}" class="btn btn-sm btn-warning">
                                                            <i class="ri-edit-line"></i>
                                                        </a>
                                                        <form action="{{ route('investors.destroy', $investor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستثمر؟')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $investors->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <iconify-icon icon="solar:users-group-two-rounded-broken" class="fs-48 text-muted"></iconify-icon>
                            <h5 class="mt-3 text-muted">لا يوجد مستثمرين بعد</h5>
                            <p class="text-muted">ابدأ بإضافة أول مستثمر</p>
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('investors.create') }}" class="btn btn-primary">
                                    <i class="ri-add-line me-1"></i> إضافة مستثمر جديد
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="{{ asset('js/investors.js') }}"></script>
