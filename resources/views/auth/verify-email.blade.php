@extends('layouts.auth', ['title' => 'Verify Email'])

@section('content')
<div class="col-xl-5">
    <div class="card auth-card">
        <div class="card-body px-3 py-5">
            <div class="mx-auto mb-4 text-center auth-logo">
                <a href="{{ route('second', ['dashboards', 'analytics'])}}" class="logo-dark">
                    <img src="/images/logo-dark.png" height="32" alt="logo dark">
                </a>

                <a href="{{ route('second', ['dashboards', 'analytics'])}}" class="logo-light">
                    <img src="/images/logo-light.png" height="28" alt="logo light">
                </a>
            </div>

            <h2 class="fw-bold text-uppercase text-center fs-18">Verify Email</h2>
            <p class="text-muted text-center mt-1 mb-4">Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.</p>

            <div class="px-4">
                <form action="{{ route('second', ['dashboards', 'analytics'])}}" class="authentication-form">
                    <div class="mb-1 text-center d-grid">
                        <button class="btn btn-danger py-2 fw-medium" type="submit">Resend Verification Email</button>
                    </div>
                </form>
            </div> <!-- end col -->
        </div> <!-- end card-body -->
    </div> <!-- end card -->
    <p class="mb-0 text-center text-white">Back to <a href="{{ route('second', ['auth', 'login'])}}" class="text-reset text-unline-dashed fw-bold ms-1">Sign In</a></p>
</div> <!-- end col -->
@endsection

<script src="{{ asset('js/verify-email.js') }}"></script>
