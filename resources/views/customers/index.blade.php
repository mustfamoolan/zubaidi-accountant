@extends('layouts.vertical', ['title' => 'العملاء','subTitle' => 'إدارة العملاء'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">قائمة العملاء</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('invoices.index') }}" class="btn btn-primary">
                            <i class="ri-arrow-left-line me-1"></i> العودة للفواتير
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($customers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>اسم العميل</th>
                                        <th>عدد الفواتير</th>
                                        <th>إجمالي المشتريات</th>
                                        <th>تاريخ الإضافة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                        <tr>
                                            <td>{{ $customer->name }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $customer->sale_items_count }}</span>
                                            </td>
                                            <td class="text-success fw-bold">
                                                {{ number_format($customer->sale_items_sum_amount_iqd ?? 0, 0) }} د.ع
                                            </td>
                                            <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-sm btn-info" title="عرض التفاصيل">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                                                        <i class="ri-edit-line"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" title="حذف" onclick="deleteCustomer({{ $customer->id }}, '{{ $customer->name }}')">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $customers->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <iconify-icon icon="solar:users-group-rounded-broken" class="fs-48 text-muted"></iconify-icon>
                            <h5 class="mt-3 text-muted">لا يوجد عملاء بعد</h5>
                            <p class="text-muted">سيتم إضافة العملاء عند بيع الفواتير</p>
                            <a href="{{ route('invoices.index') }}" class="btn btn-primary">
                                <i class="ri-arrow-left-line me-1"></i> عرض الفواتير
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="{{ asset('js/delete-confirm.js') }}"></script>

<script src="{{ asset('js/customer-list.js') }}"></script>
