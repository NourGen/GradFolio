@extends('layouts.auth')
@section('title', 'Login')

@section('auth-content')
<div class="auth-form-header">
    <h2>Welcome back</h2>
    <p>Sign in to your GradFolio account</p>
</div>

<form method="POST" action="{{ route('login') }}" class="auth-form" id="login-form">
    @csrf

    <div class="form-group">
        <label for="email">Email Address</label>
        <div class="input-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" id="email"
                   value="{{ old('email') }}"
                   placeholder="your@email.com"
                   required autofocus autocomplete="email">
        </div>
        @error('email')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-group">
        <label for="password">
            Password
            <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
        </label>
        <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password" id="password"
                   placeholder="••••••••"
                   required autocomplete="current-password">
        </div>
        @error('password')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-check">
        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        <label for="remember">Remember me for 30 days</label>
    </div>

    <button type="submit" class="btn-primary btn-full" id="login-btn">
        <i class="fas fa-sign-in-alt"></i> Sign In
    </button>
</form>

<div class="auth-switch">
    Don't have an account? <span style="color:var(--text-muted)">Contact BSA Academy to get access.</span>
</div>
@endsection
