@extends('layouts.auth')
@section('title', 'Set New Password')

@section('auth-content')
<div class="auth-form-header">
    <div style="font-size:2.5rem;margin-bottom:1rem;">🔐</div>
    <h2>Set Your Password</h2>
    <p>Your account was created by BSA Academy. Please set a new personal password to continue.</p>
</div>

<div class="alert alert-error" style="margin-bottom:1.5rem;font-size:.875rem">
    <i class="fas fa-exclamation-triangle"></i>
    You must change your temporary password before accessing your dashboard.
</div>

<form method="POST" action="{{ route('password.change.update') }}" class="auth-form" id="force-password-form">
    @csrf
    <div class="form-group">
        <label for="password">New Password</label>
        <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password" id="password"
                   placeholder="Min. 8 chars, uppercase + numbers"
                   required autocomplete="new-password" autofocus>
        </div>
        @error('password')<span class="form-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="password_confirmation">Confirm Password</label>
        <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   placeholder="Repeat new password"
                   required autocomplete="new-password">
        </div>
    </div>
    <button type="submit" class="btn-primary btn-full" id="set-password-btn">
        <i class="fas fa-shield-alt"></i> Set My Password & Continue
    </button>
</form>

<div class="auth-switch">
    <form method="POST" action="{{ route('logout') }}" style="display:inline">
        @csrf
        <button type="submit" style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:.875rem">
            Logout instead
        </button>
    </form>
</div>
@endsection
