@extends('layouts.vertical', ['title' => 'تعديل العميل','subTitle' => 'العملاء'])

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">تعديل العميل: {{ $customer->name }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">اسم العميل <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $customer->name) }}" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="ri-save-line me-1"></i> حفظ التعديلات
                            </button>
                            <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
