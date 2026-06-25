@extends('layouts.auth')
@section('title', 'Reset Password')

@section('auth-content')
<div class="auth-form-header">
    <h2>Set New Password</h2>
    <p>Choose a strong password for your account.</p>
</div>

<form method="POST" action="{{ route('password.store') }}" class="auth-form" id="reset-form">
    @csrf

    <!-- Hidden token and email -->
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-group">
        <label for="email">Email Address</label>
        <div class="input-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" id="email"
                   value="{{ old('email', $email ?? '') }}"
                   placeholder="your@email.com"
                   required autofocus>
        </div>
        @error('email')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-group">
        <label for="password">New Password</label>
        <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password" id="password"
                   placeholder="Min. 8 characters"
                   required autocomplete="new-password">
        </div>
        @error('password')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirm New Password</label>
        <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   placeholder="Repeat new password"
                   required autocomplete="new-password">
        </div>
    </div>

    <button type="submit" class="btn-primary btn-full" id="reset-submit-btn">
        <i class="fas fa-key"></i> Reset Password
    </button>
</form>
@endsection
