@extends('layouts.auth')
@section('title', 'Create Account')

@section('auth-content')
<div class="auth-form-header">
    <h2>Join GradFolio</h2>
    <p>Create your free portfolio account</p>
</div>

<form method="POST" action="{{ route('register') }}" class="auth-form" id="register-form">
    @csrf

    <div class="form-group">
        <label for="name">Full Name</label>
        <div class="input-wrapper">
            <i class="fas fa-user input-icon"></i>
            <input type="text" name="name" id="name"
                   value="{{ old('name') }}"
                   placeholder="Sarah Al-Hassan"
                   required autofocus autocomplete="name">
        </div>
        @error('name')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-group">
        <label for="email">Email Address</label>
        <div class="input-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" id="email"
                   value="{{ old('email') }}"
                   placeholder="your@email.com"
                   required autocomplete="email">
        </div>
        @error('email')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password" id="password"
                   placeholder="Min. 8 characters"
                   required autocomplete="new-password">
        </div>
        @error('password')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirm Password</label>
        <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   placeholder="Repeat your password"
                   required autocomplete="new-password">
        </div>
    </div>

    <div class="form-check">
        <input type="checkbox" name="terms" id="terms" required>
        <label for="terms">I agree to the <a href="#">Terms & Conditions</a></label>
    </div>

    <button type="submit" class="btn-primary btn-full" id="register-btn">
        <i class="fas fa-rocket"></i> Create My Portfolio
    </button>
</form>

<div class="auth-switch">
    Already have an account? <a href="{{ route('login') }}">Sign in</a>
</div>
@endsection
