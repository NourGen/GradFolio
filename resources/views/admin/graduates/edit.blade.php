@extends('layouts.app')
@section('title', 'Edit Graduate Account')

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
                <h1 style="font-size:1.5rem;margin-bottom:.2rem">Edit Graduate Account</h1>
                <p style="color:var(--text-muted);font-size:.875rem">Update graduate profile information, academic track, and filters.</p>
            </div>
            <a href="{{ route('admin.graduates.index') }}" class="btn-ghost btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="admin-table-card" style="max-width:560px;padding:2rem">
            <form method="POST" action="{{ route('admin.graduates.update', $user->id) }}" id="edit-graduate-form">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="name" id="name"
                               value="{{ old('name', $user->name) }}"
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
                               value="{{ old('email', $user->email) }}"
                               placeholder="graduate@email.com"
                               required>
                    </div>
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="track">Track</label>
                    <div class="input-wrapper">
                        <i class="fas fa-graduation-cap input-icon"></i>
                        <input type="text" name="track" id="track"
                               value="{{ old('track', $user->portfolio?->track) }}"
                               placeholder="e.g. Full-Stack Development">
                    </div>
                    @error('track')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="specialization">Specialization</label>
                    <div class="input-wrapper">
                        <i class="fas fa-code input-icon"></i>
                        <input type="text" name="specialization" id="specialization"
                               value="{{ old('specialization', $user->portfolio?->specialization) }}"
                               placeholder="e.g. Laravel & Vue, React">
                    </div>
                    @error('specialization')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="graduation_year">Graduation Year</label>
                    <select name="graduation_year" id="graduation_year" style="width: 100%; padding: 0.75rem; background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius-md); color: var(--text);">
                        <option value="">— Select Year —</option>
                        @for($y = date('Y') + 4; $y >= 2025; $y--)
                            <option value="{{ $y }}" {{ old('graduation_year', $user->portfolio?->graduation_year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    @error('graduation_year')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="btn-primary btn-full" id="edit-grad-btn" style="margin-top: 1.5rem;">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>
    </main>
</div>
@endsection
