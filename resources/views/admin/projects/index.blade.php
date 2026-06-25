@extends('layouts.app')
@section('title', 'Manage Projects')

@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-header">
            <span class="brand-icon">🎓</span>
            <span class="brand-text">Admin Panel</span>
        </div>
        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link" id="nav-dashboard">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="{{ route('admin.graduates.index') }}" class="admin-nav-link" id="nav-graduates">
                <i class="fas fa-users"></i> Graduates
            </a>
            <a href="{{ route('admin.projects.index') }}" class="admin-nav-link active" id="nav-projects">
                <i class="fas fa-code"></i> Manage Projects
            </a>
            <a href="{{ route('directory') }}" target="_blank" class="admin-nav-link" id="nav-directory">
                <i class="fas fa-globe"></i> View Site
            </a>
        </nav>
    </aside>

    <div class="admin-main">
        <div class="admin-topbar">
            <h1><i class="fas fa-code"></i> Manage Projects</h1>
            <span class="admin-user-badge">👤 {{ auth()->user()->name }}</span>
        </div>

        <!-- Success/Error Notifications -->
        @if(session('success'))
        <div class="alert alert-success" style="background: rgba(16,185,129,0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.2); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.projects.index') }}" class="admin-search-form">
            <div class="search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search projects by title, description, or graduate name..." id="admin-search">
                <button type="submit" class="search-btn">Search</button>
            </div>
        </form>

        <!-- Projects Table -->
        <div class="admin-table-card">
            <table class="admin-table" id="projects-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Preview</th>
                        <th>Project Details</th>
                        <th>Graduate</th>
                        <th>Tech Stack</th>
                        <th>Links</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                    <tr id="project-row-{{ $project->id }}">
                        <td>
                            @if($project->cover_image_path)
                                <img src="{{ $project->coverUrl() }}" alt="{{ $project->title }}" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border);">
                            @else
                                <div style="width: 60px; height: 40px; background: rgba(255,255,255,0.03); border: 1px dashed var(--border); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                                    <i class="fas fa-folder fa-lg"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--text); margin-bottom: 0.25rem;">{{ $project->title }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; max-width: 400px;" title="{{ $project->description }}">
                                {{ $project->description }}
                            </div>
                        </td>
                        <td>
                            @if($project->portfolio && $project->portfolio->user)
                                <a href="{{ route('admin.graduates.show', $project->portfolio->user_id) }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                                    {{ $project->portfolio->user->name }}
                                </a>
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">Unknown Graduate</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; flex-wrap: wrap; gap: 4px; max-width: 300px;">
                                @forelse($project->techStackArray() as $tech)
                                    <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-muted); padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; border: 1px solid var(--border);">
                                        {{ $tech }}
                                    </span>
                                @empty
                                    <span style="color: var(--text-muted); font-size: 0.8rem; font-style: italic;">—</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                @if($project->project_url)
                                    <a href="{{ $project->project_url }}" target="_blank" class="btn-ghost-sm" style="padding: 4px 8px; font-size: 0.75rem;" title="Live Project Link">
                                        <i class="fas fa-external-link-alt"></i> Live
                                    </a>
                                @endif
                                @if($project->github_url)
                                    <a href="{{ $project->github_url }}" target="_blank" class="btn-ghost-sm" style="padding: 4px 8px; font-size: 0.75rem;" title="GitHub Repository">
                                        <i class="fab fa-github"></i> Code
                                    </a>
                                @endif
                                @if(!$project->project_url && !$project->github_url)
                                    <span style="color: var(--text-muted); font-size: 0.8rem; font-style: italic;">No Links</span>
                                @endif
                            </div>
                        </td>
                        <td class="admin-actions">
                            <form method="POST" action="{{ route('admin.projects.destroy', $project->id) }}" style="display:inline"
                                  onsubmit="return confirm('Delete project &quot;{{ $project->title }}&quot;? This is irreversible.')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn-danger-sm" id="del-project-{{ $project->id }}" style="padding: 6px 12px; font-size: 0.8rem;">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="empty-table" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                            <i class="fas fa-folder-open fa-2x" style="display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                            No projects found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrapper" style="padding: 1rem;">
                {{ $projects->appends(['search' => $search])->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endsection
