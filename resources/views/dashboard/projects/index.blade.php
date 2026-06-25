@extends('layouts.app')
@section('title', 'My Projects')

@section('content')
<div class="dashboard-layout">

    {{-- ── Sidebar ──────────────────────────────────────────────── --}}
    <aside class="dashboard-sidebar" id="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="sidebar-avatar">
                @if($portfolio->profilePictureUrl())
                    <img src="{{ $portfolio->profilePictureUrl() }}" alt="Profile">
                @else
                    <div class="avatar-placeholder">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                @endif
            </div>
            <h3>{{ auth()->user()->name }}</h3>
            <p class="sidebar-headline">{{ $portfolio->headline ?? 'Graduate' }}</p>
            <span class="publish-badge {{ $portfolio->is_published ? 'badge-live' : 'badge-draft' }}">
                {{ $portfolio->is_published ? '🟢 Live' : '⚪ Draft' }}
            </span>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="sidebar-link"><i class="fas fa-home"></i> Dashboard</a>
            <a href="{{ route('dashboard.projects.index') }}" class="sidebar-link active"><i class="fas fa-code"></i> Projects</a>
            <a href="{{ route('dashboard.projects.create') }}" class="sidebar-link"><i class="fas fa-plus-circle"></i> Add Project</a>
            <a href="{{ route('dashboard.analytics') }}" class="sidebar-link"><i class="fas fa-chart-line"></i> Analytics</a>
            @if($portfolio->slug && $portfolio->is_published)
            <a href="{{ route('portfolio.show', $portfolio->slug) }}" target="_blank" class="sidebar-link">
                <i class="fas fa-eye"></i> View Portfolio
            </a>
            @endif
        </nav>

        <a href="{{ route('dashboard.projects.create') }}" class="btn-publish btn-go-live" id="add-project-cta" style="display:flex;align-items:center;gap:.6rem;justify-content:center;text-decoration:none">
            <i class="fas fa-plus"></i> Add New Project
        </a>
    </aside>

    {{-- ── Main Content ─────────────────────────────────────────── --}}
    <main class="dashboard-main">

        {{-- Header --}}
        <div class="projects-hub-header">
            <div>
                <h1 class="projects-hub-title"><i class="fas fa-code"></i> My Projects</h1>
                <p class="projects-hub-subtitle">{{ $projects->count() }} project{{ $projects->count() !== 1 ? 's' : '' }} in your portfolio</p>
            </div>
            <a href="{{ route('dashboard.projects.create') }}" class="btn-primary" id="new-project-btn">
                <i class="fas fa-plus"></i> New Project
            </a>
        </div>

        @if($projects->isEmpty())
        {{-- Empty State --}}
        <div class="dashboard-card projects-empty-state">
            <div class="empty-projects-icon">💻</div>
            <h2>No projects yet</h2>
            <p>Showcase your work by adding your first project. Include a cover image, description, and tech stack to stand out.</p>
            <a href="{{ route('dashboard.projects.create') }}" class="btn-primary" id="first-project-btn">
                <i class="fas fa-plus"></i> Add Your First Project
            </a>
        </div>
        @else
        {{-- Projects Grid --}}
        <div class="projects-hub-grid" id="projects-grid">
            @foreach($projects as $project)
            <div class="project-hub-card" id="proj-card-{{ $project->id }}">

                {{-- Cover Image --}}
                <div class="phc-cover">
                    @if($project->coverUrl())
                        <img src="{{ $project->coverUrl() }}" alt="{{ $project->title }}" loading="lazy">
                    @else
                        <div class="phc-cover-placeholder">
                            <i class="fas fa-code"></i>
                        </div>
                    @endif
                    <div class="phc-cover-overlay">
                        <a href="{{ route('dashboard.projects.edit', $project->id) }}" class="phc-overlay-btn" id="edit-proj-{{ $project->id }}">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>

                {{-- Info --}}
                <div class="phc-body">
                    <h3 class="phc-title">{{ $project->title }}</h3>
                    <p class="phc-desc">{{ Str::limit($project->description, 100) }}</p>

                    @if($project->tech_stack)
                    <div class="phc-tags">
                        @foreach(array_slice($project->techStackArray(), 0, 4) as $tech)
                            <span class="tech-badge">{{ $tech }}</span>
                        @endforeach
                        @if(count($project->techStackArray()) > 4)
                            <span class="tech-badge tech-badge-more">+{{ count($project->techStackArray()) - 4 }}</span>
                        @endif
                    </div>
                    @endif

                    {{-- Stats Row --}}
                    <div class="phc-stats">
                        <span title="Gallery images"><i class="fas fa-images"></i> {{ $project->galleryImages()->count() }}</span>
                        <span title="PDF files"><i class="fas fa-file-pdf"></i> {{ $project->pdfFiles()->count() }}</span>
                        @if($project->project_url)
                            <a href="{{ $project->project_url }}" target="_blank" title="Live demo"><i class="fas fa-external-link-alt"></i> Live</a>
                        @endif
                        @if($project->github_url)
                            <a href="{{ $project->github_url }}" target="_blank" title="GitHub"><i class="fab fa-github"></i> Code</a>
                        @endif
                    </div>
                </div>

                {{-- Actions Footer --}}
                <div class="phc-footer">
                    <a href="{{ route('dashboard.projects.edit', $project->id) }}" class="phc-action-btn phc-edit" id="edit-link-{{ $project->id }}">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form method="POST" action="{{ route('dashboard.projects.destroy', $project->id) }}" class="phc-delete-form">
                        @csrf @method('DELETE')
                        <button type="submit" class="phc-action-btn phc-delete"
                                id="del-proj-{{ $project->id }}"
                                onclick="return confirm('Delete {{ addslashes($project->title) }}? This will remove all images too.')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>

            </div>
            @endforeach
        </div>
        @endif

    </main>
</div>
@endsection

@section('styles')
<style>
/* ── Projects Hub ───────────────────────────────────────────── */
.projects-hub-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}
.projects-hub-title {
    font-family: var(--font-display);
    font-size: 1.9rem;
    font-weight: 700;
    margin: 0 0 .3rem;
    color: var(--text-primary);
}
.projects-hub-subtitle { color: var(--text-muted); margin: 0; }

/* Empty State */
.projects-empty-state {
    text-align: center;
    padding: 5rem 2rem;
}
.empty-projects-icon { font-size: 4rem; margin-bottom: 1rem; }
.projects-empty-state h2 {
    font-family: var(--font-display);
    font-size: 1.6rem;
    margin-bottom: .75rem;
}
.projects-empty-state p {
    color: var(--text-muted);
    max-width: 420px;
    margin: 0 auto 2rem;
    line-height: 1.6;
}

/* Projects Grid */
.projects-hub-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

/* Project Card */
.project-hub-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform var(--t-fast), box-shadow var(--t-fast), border-color var(--t-fast);
}
.project-hub-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 32px rgba(0,0,0,.25);
    border-color: var(--primary);
}

/* Cover */
.phc-cover {
    position: relative;
    height: 180px;
    overflow: hidden;
    background: var(--surface-alt, rgba(0,0,0,.2));
}
.phc-cover img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .5s ease;
}
.project-hub-card:hover .phc-cover img { transform: scale(1.04); }
.phc-cover-placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    font-size: 3.5rem;
    color: var(--primary);
    opacity: .25;
}
.phc-cover-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity var(--t-fast);
}
.project-hub-card:hover .phc-cover-overlay { opacity: 1; }
.phc-overlay-btn {
    background: var(--primary);
    color: #fff;
    padding: .6rem 1.4rem;
    border-radius: var(--radius-full);
    text-decoration: none;
    font-size: .88rem;
    font-weight: 600;
    letter-spacing: .02em;
    transition: background var(--t-fast);
}
.phc-overlay-btn:hover { background: var(--primary-dark, #a8622a); }

/* Body */
.phc-body { flex: 1; padding: 1.25rem 1.25rem .75rem; }
.phc-title { font-size: 1.05rem; font-weight: 700; margin: 0 0 .4rem; color: var(--text-primary); }
.phc-desc { font-size: .85rem; color: var(--text-muted); margin: 0 0 .9rem; line-height: 1.5; }
.phc-tags { display: flex; flex-wrap: wrap; gap: .35rem; margin-bottom: .9rem; }
.tech-badge {
    font-size: .72rem; font-weight: 600;
    padding: .2rem .6rem;
    background: rgba(196,120,58,.12);
    color: var(--primary);
    border-radius: var(--radius-full);
    border: 1px solid rgba(196,120,58,.2);
}
.tech-badge-more { opacity: .6; }
.phc-stats {
    display: flex;
    align-items: center;
    gap: .9rem;
    font-size: .8rem;
    color: var(--text-muted);
}
.phc-stats a { color: var(--text-muted); text-decoration: none; transition: color var(--t-fast); }
.phc-stats a:hover { color: var(--primary); }
.phc-stats i { margin-right: .25rem; }

/* Footer */
.phc-footer {
    display: flex;
    border-top: 1px solid var(--border);
}
.phc-action-btn {
    flex: 1;
    padding: .75rem;
    font-size: .82rem;
    font-weight: 600;
    text-align: center;
    cursor: pointer;
    border: none;
    background: transparent;
    transition: background var(--t-fast), color var(--t-fast);
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    color: var(--text-secondary);
}
.phc-edit:hover { background: rgba(196,120,58,.1); color: var(--primary); }
.phc-delete { border-left: 1px solid var(--border); }
.phc-delete:hover { background: rgba(248,113,113,.1); color: var(--danger); }
.phc-delete-form { flex: 1; display: flex; }

@media (max-width: 600px) {
    .projects-hub-grid { grid-template-columns: 1fr; }
    .projects-hub-header { flex-direction: column; }
}
</style>
@endsection
