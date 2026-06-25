@extends('layouts.app')
@section('title', $user->name . ' — Admin View')

@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-header"><span class="brand-icon">🎓</span><span class="brand-text">Admin Panel</span></div>
        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('admin.graduates.index') }}" class="admin-nav-link active"><i class="fas fa-users"></i> Graduates</a>
        </nav>
    </aside>

    <div class="admin-main">
        <div class="admin-topbar">
            <a href="{{ route('admin.graduates.index') }}" class="btn-ghost"><i class="fas fa-arrow-left"></i> Back</a>
            <h1>{{ $user->name }}</h1>
        </div>

        <div class="admin-profile-grid">
            <!-- Profile Summary -->
            <div class="admin-table-card">
                <div class="admin-card-header"><h3>Graduate Info</h3></div>
                <dl class="info-list">
                    <dt>Name</dt><dd>{{ $user->name }}</dd>
                    <dt>Email</dt><dd>{{ $user->email }}</dd>
                    <dt>Joined</dt><dd>{{ $user->created_at->format('M d, Y') }}</dd>
                    <dt>Headline</dt><dd>{{ $user->portfolio?->headline ?? '—' }}</dd>
                    <dt>Location</dt><dd>{{ $user->portfolio?->location ?? '—' }}</dd>
                    <dt>Status</dt><dd>
                        @if($user->portfolio?->is_published)
                            <span class="badge badge-live">🟢 Live</span>
                        @else
                            <span class="badge badge-draft">⚪ Draft</span>
                        @endif
                    </dd>
                    <dt>Total Views</dt><dd>{{ $user->portfolio?->totalViews() ?? 0 }}</dd>
                </dl>

                <div class="admin-actions-row">
                    @if($user->portfolio)
                    <form method="POST" action="{{ route('admin.portfolios.toggle', $user->portfolio->id) }}">
                        @csrf
                        <button class="btn-primary">
                            {{ $user->portfolio->is_published ? 'Unpublish' : 'Publish' }} Portfolio
                        </button>
                    </form>
                    @if($user->portfolio->slug)
                    <a href="{{ route('portfolio.show', $user->portfolio->slug) }}" target="_blank" class="btn-secondary">
                        <i class="fas fa-eye"></i> View Portfolio
                    </a>
                    @endif
                    @endif

                    <form method="POST" action="{{ route('admin.graduates.destroy', $user->id) }}"
                          onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button class="btn-danger-sm" style="padding:.6rem 1rem">
                            <i class="fas fa-trash"></i> Delete Account
                        </button>
                    </form>
                </div>
            </div>

            <!-- Projects -->
            <div class="admin-table-card">
                <div class="admin-card-header"><h3>Projects ({{ $user->portfolio?->projects->count() ?? 0 }})</h3></div>
                @if($user->portfolio?->projects->count())
                <ul class="project-admin-list">
                    @foreach($user->portfolio->projects as $project)
                    <li class="project-admin-item">
                        <strong>{{ $project->title }}</strong>
                        <span class="tech-tag-sm">{{ $project->images->count() }} images</span>
                        @if($project->project_url)<a href="{{ $project->project_url }}" target="_blank" class="project-link">Live</a>@endif
                    </li>
                    @endforeach
                </ul>
                @else
                    <p class="empty-table">No projects yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
