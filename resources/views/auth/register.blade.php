@extends('layouts.auth', ['title' => 'Register'])

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

            <h2 class="fw-bold text-uppercase text-center fs-18">Register</h2>
            <p class="text-muted text-center mt-1 mb-4">Enter your email address and password to access admin panel.</p>

            <div class="px-4">
                <form action="{{ route('second', ['dashboards', 'analytics'])}}" class="authentication-form">
                    <div class="mb-3">
                        <label class="form-label" for="example-name">Name</label>
                        <input type="text" id="example-name" name="example-name" class="form-control bg-light bg-opacity-50 border-light py-2" placeholder="Enter your name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="example-email">Email</label>
                        <input type="email" id="example-email" name="example-email" class="form-control bg-light bg-opacity-50 border-light py-2" placeholder="Enter your email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="example-password">Password</label>
                        <input type="password" id="example-password" name="example-password" class="form-control bg-light bg-opacity-50 border-light py-2" placeholder="Enter your password">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="checkbox-signup">
                            <label class="form-check-label" for="checkbox-signup">I accept <a href="javascript:void(0);" class="text-reset text-unline-dashed">Terms and Conditions</a></label>
                        </div>
                    </div>
                    <div class="mb-1 text-center d-grid">
                        <button class="btn btn-danger py-2 fw-medium" type="submit">Register</button>
                    </div>
                </form>
            </div> <!-- end col -->
        </div> <!-- end card-body -->
    </div> <!-- end card -->
    <p class="mb-0 text-center text-white">Already have an account? <a href="{{ route('second', ['auth', 'login'])}}" class="text-reset text-unline-dashed fw-bold ms-1">Sign In</a></p>
</div> <!-- end col -->
@endsection

<script src="{{ asset('js/register.js') }}"></script>
