@extends('layouts.app')
@section('title', 'Manage Graduates')

@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-header">
            <i class="fas fa-shield-alt" style="color:var(--primary)"></i> Admin Panel
        </div>
        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link" id="nav-dashboard"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="{{ route('admin.graduates.index') }}" class="admin-nav-link active" id="nav-graduates"><i class="fas fa-users"></i> Graduates</a>
            <a href="{{ route('admin.graduates.create') }}" class="admin-nav-link" id="nav-add-graduate"><i class="fas fa-user-plus"></i> Add Graduate</a>
            <a href="{{ route('admin.projects.index') }}" class="admin-nav-link" id="nav-projects"><i class="fas fa-code"></i> Manage Projects</a>
            <a href="{{ route('directory') }}" target="_blank" class="admin-nav-link"><i class="fas fa-globe"></i> View Site</a>
        </nav>
    </aside>

    <div class="admin-main">
        <div class="admin-topbar">
            <h1><i class="fas fa-users"></i> Graduates</h1>
            <a href="{{ route('admin.graduates.create') }}" class="btn-primary" id="create-graduate-btn">
                <i class="fas fa-user-plus"></i> Add Graduate
            </a>
        </div>

        <!-- Search -->
        <form method="GET" action="{{ route('admin.graduates.index') }}" class="admin-search-form">
            <div class="search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search graduates by name or email..." id="admin-search">
                <button type="submit" class="search-btn">Search</button>
            </div>
        </form>

        <div class="admin-table-card">
            <table class="admin-table" id="graduates-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Graduate</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($graduates as $grad)
                    <tr id="grad-row-{{ $grad->id }}">
                        <td>{{ $grad->id }}</td>
                        <td>
                            <div class="admin-grad-name">
                                <div class="admin-avatar">{{ strtoupper(substr($grad->name, 0, 2)) }}</div>
                                {{ $grad->name }}
                            </div>
                        </td>
                        <td>{{ $grad->email }}</td>
                        <td>
                            @if($grad->is_suspended)
                                <span class="badge badge-suspended" style="background:rgba(229,57,53,0.1); color:#e53935; border:1px solid rgba(229,57,53,0.2); padding:2px 8px; border-radius:12px; font-size:0.75rem;">🔴 Suspended</span>
                            @else
                                @if($grad->portfolio?->is_published)
                                    <span class="badge badge-live" style="background:rgba(16,185,129,0.1); color:#10b981; border:1px solid rgba(16,185,129,0.2); padding:2px 8px; border-radius:12px; font-size:0.75rem;">🟢 Live</span>
                                @else
                                    <span class="badge badge-draft" style="background:rgba(255,255,255,0.05); color:var(--text-muted); border:1px solid var(--border); padding:2px 8px; border-radius:12px; font-size:0.75rem;">⚪ Draft</span>
                                @endif
                                
                                @if($grad->portfolio?->is_verified)
                                    <span class="badge badge-verified" style="background:rgba(0,82,212,0.1); color:#0052d4; border:1px solid rgba(0,82,212,0.2); padding:2px 8px; border-radius:12px; font-size:0.75rem; margin-left:0.25rem;">✓ Verified</span>
                                @endif
                            @endif
                        </td>
                        <td>{{ $grad->created_at->format('M d, Y') }}</td>
                        <td class="admin-actions">
                            <a href="{{ route('admin.graduates.show', $grad->id) }}" class="btn-ghost-sm" id="view-grad-{{ $grad->id }}">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('admin.graduates.edit', $grad->id) }}" class="btn-ghost-sm" id="edit-grad-{{ $grad->id }}">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            
                            <!-- Publish Toggle (only if not suspended) -->
                            @if($grad->portfolio && !$grad->is_suspended)
                            <form method="POST" action="{{ route('admin.portfolios.toggle', $grad->portfolio->id) }}" style="display:inline">
                                @csrf
                                <button type="submit" class="btn-ghost-sm" id="toggle-grad-{{ $grad->id }}">
                                    {{ $grad->portfolio->is_published ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>
                            @endif

                            <!-- Verification Toggle (only if portfolio exists and not suspended) -->
                            @if($grad->portfolio && !$grad->is_suspended)
                            <form method="POST" action="{{ route('admin.portfolios.toggle-verification', $grad->portfolio->id) }}" style="display:inline">
                                @csrf
                                <button type="submit" class="btn-ghost-sm" id="verify-grad-{{ $grad->id }}" style="color: {{ $grad->portfolio->is_verified ? 'var(--text-muted)' : 'var(--accent)' }}">
                                    <i class="fas {{ $grad->portfolio->is_verified ? 'fa-times-circle' : 'fa-check-circle' }}"></i> {{ $grad->portfolio->is_verified ? 'Unverify' : 'Verify' }}
                                </button>
                            </form>
                            @endif

                            <!-- Suspension Toggle -->
                            <form method="POST" action="{{ route('admin.graduates.toggle-suspension', $grad->id) }}" style="display:inline">
                                @csrf
                                <button type="submit" class="btn-ghost-sm" id="suspend-grad-{{ $grad->id }}" style="color: {{ $grad->is_suspended ? '#10b981' : '#e53935' }}">
                                    <i class="fas {{ $grad->is_suspended ? 'fa-user-check' : 'fa-user-slash' }}"></i> {{ $grad->is_suspended ? 'Unsuspend' : 'Suspend' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.graduates.destroy', $grad->id) }}" style="display:inline"
                                  onsubmit="return confirm('Delete {{ $grad->name }}? This is irreversible.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger-sm" id="del-grad-{{ $grad->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="empty-table">No graduates found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrapper">
                {{ $graduates->appends(['search' => $search])->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endsection
