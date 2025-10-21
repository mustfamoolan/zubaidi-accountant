@extends('layouts.dashboard', ['title' => 'لوحة التحكم','subTitle' => 'الصفحة الرئيسية'])

@section('content')
    <div class="row">
        <div class="col-md-6 col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-6">
                            <div class="avatar-md bg-light bg-opacity-50 rounded">
                                <iconify-icon icon="solar:document-text-broken"
                                              class="fs-32 text-primary avatar-title"></iconify-icon>
                            </div>
                            <p class="text-muted mb-2 mt-3">الفواتير</p>
                            <h3 class="text-dark fw-bold d-flex align-items-center gap-2 mb-0">إدارة الفواتير</h3>
                        </div> <!-- end col -->
                        <div class="col-6 text-end">
                            <a href="{{ route('invoices.index') }}" class="btn btn-primary btn-sm">عرض الفواتير</a>
                        </div> <!-- end col -->
                    </div> <!-- end row-->
                </div> <!-- end card body -->
            </div> <!-- end card -->
        </div> <!-- end col -->

        <div class="col-md-6 col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-6">
                            <div class="avatar-md bg-light bg-opacity-50 rounded">
                                <iconify-icon icon="solar:money-bag-broken"
                                              class="fs-32 text-success avatar-title"></iconify-icon>
                            </div>
                            <p class="text-muted mb-2 mt-3">رأس المال</p>
                            <h3 class="text-dark fw-bold d-flex align-items-center gap-2 mb-0">إدارة رأس المال</h3>
                        </div> <!-- end col -->
                        <div class="col-6 text-end">
                            <a href="{{ route('capital.index') }}" class="btn btn-success btn-sm">عرض رأس المال</a>
                        </div> <!-- end col -->
                    </div> <!-- end row-->
                </div> <!-- end card body -->
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection

<script src="{{ asset('js/dashboard.js') }}"></script>
