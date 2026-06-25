@extends('layouts.auth')
@section('title', 'Forgot Password')

@section('auth-content')
<div class="auth-form-header">
    <h2>Forgot Password?</h2>
    <p>Enter your email and we'll send you a reset link.</p>
</div>

@if(session('status'))
    <div class="alert alert-success" style="margin-bottom:1.5rem">
        <i class="fas fa-check-circle"></i> {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="auth-form" id="forgot-form">
    @csrf
    <div class="form-group">
        <label for="email">Email Address</label>
        <div class="input-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" id="email"
                   value="{{ old('email') }}"
                   placeholder="your@email.com"
                   required autofocus>
        </div>
        @error('email')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <button type="submit" class="btn-primary btn-full" id="forgot-submit-btn">
        <i class="fas fa-paper-plane"></i> Send Reset Link
    </button>
</form>

<div class="auth-switch">
    <a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Back to Login</a>
</div>
@endsection
