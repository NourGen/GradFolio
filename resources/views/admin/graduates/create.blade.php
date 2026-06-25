@extends('layouts.app')
@section('title', 'Create Graduate Account')

@section('content')
<div class="admin-layout">

    {{-- Admin Sidebar --}}
    <aside class="admin-sidebar">
        <div class="admin-sidebar-header">
            <i class="fas fa-shield-alt" style="color:var(--primary)"></i> Admin Panel
        </div>
        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="{{ route('admin.graduates.index') }}" class="admin-nav-link active"><i class="fas fa-users"></i> Graduates</a>
            <a href="{{ route('admin.graduates.create') }}" class="admin-nav-link"><i class="fas fa-user-plus"></i> Add Graduate</a>
        </nav>
    </aside>

    {{-- Main --}}
    <main class="admin-main">
        <div class="admin-topbar">
            <div>
                <h1 style="font-size:1.5rem;margin-bottom:.2rem">Create Graduate Account</h1>
                <p style="color:var(--text-muted);font-size:.875rem">A welcome email with login credentials will be sent automatically.</p>
            </div>
            <a href="{{ route('admin.graduates.index') }}" class="btn-ghost btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="admin-table-card" style="max-width:560px;padding:2rem">
            <form method="POST" action="{{ route('admin.graduates.store') }}" id="create-graduate-form">
                @csrf

                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="name" id="name"
                               value="{{ old('name') }}"
                               placeholder="e.g. Sarah Al-Hassan"
                               required autofocus>
                    </div>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" id="email"
                               value="{{ old('email') }}"
                               placeholder="graduate@email.com"
                               required>
                    </div>
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="alert alert-success" style="margin:1.25rem 0;font-size:.875rem">
                    <i class="fas fa-info-circle"></i>
                    A <strong>secure temporary password</strong> will be generated and emailed to the graduate.
                    They will be asked to change it on first login.
                </div>

                <button type="submit" class="btn-primary btn-full" id="create-grad-btn">
                    <i class="fas fa-user-plus"></i> Create Account & Send Email
                </button>
            </form>
        </div>
    </main>
</div>
@endsection
