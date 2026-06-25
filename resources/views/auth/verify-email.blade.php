@extends('layouts.auth')
@section('title', 'Verify Your Email')

@section('auth-content')
<div class="auth-form-header">
    <div style="font-size:3rem;margin-bottom:1rem;">📧</div>
    <h2>Verify Your Email</h2>
    <p>We sent a verification link to your email address. Please check your inbox (and spam folder).</p>
</div>

@if(session('status') === 'verification-link-sent')
    <div class="alert alert-success" style="margin-bottom:1.5rem">
        <i class="fas fa-check-circle"></i> A new verification link has been sent to your email!
    </div>
@endif

<div style="text-align:center; padding:1rem 0;">
    <form method="POST" action="{{ route('verification.send') }}" style="margin-bottom:1rem">
        @csrf
        <button type="submit" class="btn-primary btn-full" id="resend-verify-btn">
            <i class="fas fa-redo"></i> Resend Verification Email
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-ghost btn-full" id="logout-from-verify-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </form>
</div>
@endsection
