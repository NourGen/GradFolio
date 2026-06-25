@extends('layouts.app')
@section('title', 'Graduate Directory')

@section('styles')
<style>
/* ── Drawer & Sidebar Styles ── */
.filter-drawer {
    position: fixed;
    top: 0;
    right: -340px;
    width: 340px;
    height: 100vh;
    background: var(--surface-light);
    border-left: 1px solid var(--border);
    box-shadow: -5px 0 25px rgba(0,0,0,0.5);
    z-index: 2000;
    transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
}

.filter-drawer.open {
    right: 0;
}

/* Drawer Overlay */
.filter-drawer-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    z-index: 1999;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.filter-drawer-overlay.open {
    opacity: 1;
    pointer-events: auto;
}

/* Drawer Header */
.filter-drawer-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.filter-drawer-header h3 {
    margin: 0;
    font-size: 1.15rem;
    font-family: var(--font-display);
    color: var(--accent);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-drawer-close {
    background: transparent;
    border: none;
    color: var(--text);
    font-size: 1.75rem;
    cursor: pointer;
    line-height: 1;
    transition: color var(--t-fast);
}

.filter-drawer-close:hover {
    color: var(--primary);
}

/* Drawer Body */
.filter-drawer-body {
    padding: 1.5rem;
    flex: 1;
    overflow-y: auto;
}

.filter-group {
    margin-bottom: 1.5rem;
}

.filter-group label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
}

.filter-control {
    width: 100%;
    padding: 0.75rem 1rem;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    color: var(--text);
    font-size: 0.9rem;
    transition: border-color var(--t-fast);
}

.filter-control:focus {
    border-color: var(--primary);
    outline: none;
}

.filter-drawer-footer {
    padding: 1.5rem;
    border-top: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.btn-filter-reset {
    text-align: center;
    font-size: 0.88rem;
    color: var(--text-muted);
    text-decoration: none;
    transition: color var(--t-fast);
}

.btn-filter-reset:hover {
    color: var(--danger);
}

/* ── Inline Filters Toggle Button ── */
.search-wrapper input {
    padding-right: 175px !important; /* Make room for filters and search button */
}

.filter-toggle-btn {
    position: absolute;
    right: 6.5rem;
    top: 0.45rem;
    bottom: 0.45rem;
    background: rgba(196,120,58,0.08);
    border: 1px solid rgba(196,120,58,0.25);
    color: var(--primary);
    border-radius: var(--radius-full);
    padding: 0 1rem;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    transition: all var(--t-fast);
    z-index: 10;
}

.filter-toggle-btn:hover {
    background: rgba(196,120,58,0.15);
    border-color: var(--primary);
}

.filter-toggle-btn.active {
    background: var(--primary);
    color: #fff;
    border-color: var(--primary);
}

/* ── Meta Badges on Card ── */
.grad-meta-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
    margin-bottom: 0.75rem;
}

.meta-badge {
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.18rem 0.45rem;
    border-radius: 4px;
    background: rgba(255,255,255,0.04);
    border: 1px solid var(--border);
    color: var(--text);
}

body.light-mode .meta-badge {
    background: rgba(0,0,0,0.02);
}

.meta-badge.badge-track {
    background: rgba(196,120,58,0.08);
    border-color: rgba(196,120,58,0.2);
    color: var(--primary);
}

/* Active Filters Header */
.search-results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 0 0.5rem;
}

.search-results-header p {
    margin: 0;
    font-size: 0.92rem;
    color: var(--text-muted);
}

.clear-search-link {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--primary);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

.clear-search-link:hover {
    color: var(--secondary);
}
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-badge">✨ Discover Talented Graduates</div>
        <h1 class="hero-title">Find the Next<br><span class="gradient-text">Generation of Talent</span></h1>
        <p class="hero-subtitle">Browse portfolios from our brightest graduates. Engineers, designers, data scientists, and more.</p>
        
        <!-- Search and Filters Form -->
        <form method="GET" action="{{ route('directory') }}" class="hero-search" id="search-form">
            <!-- Retain active filters when user submits Search -->
            @if($track) <input type="hidden" name="track" value="{{ $track }}"> @endif
            @if($specialization) <input type="hidden" name="specialization" value="{{ $specialization }}"> @endif
            @if($graduation_year) <input type="hidden" name="graduation_year" value="{{ $graduation_year }}"> @endif

            <div class="search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" id="search-input" value="{{ $search ?? '' }}"
                       placeholder="Search by name, professional title, or skills..." autocomplete="off">
                
                <button type="button" class="filter-toggle-btn {{ ($track || $specialization || $graduation_year) ? 'active' : '' }}" onclick="toggleFilterDrawer()">
                    <i class="fas fa-sliders-h"></i> Filters
                    @if($track || $specialization || $graduation_year)
                        <span style="display:inline-block; width:6px; height:6px; background:#fff; border-radius:50%"></span>
                    @endif
                </button>
                <button type="submit" class="search-btn">Search</button>
            </div>
        </form>

        <div class="hero-stats">
            <div class="stat">
                <span class="stat-num">{{ \App\Models\Portfolio::where('is_published', true)->count() }}</span>
                <span class="stat-label">Graduates</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat">
                <span class="stat-num">{{ \App\Models\Project::count() }}</span>
                <span class="stat-label">Projects</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat">
                <span class="stat-num">{{ \App\Models\PortfolioView::count() }}</span>
                <span class="stat-label">Profile Views</span>
            </div>
        </div>
    </div>
    <div class="hero-bg">
        <div class="hero-blob blob-1"></div>
        <div class="hero-blob blob-2"></div>
    </div>
</section>

<!-- Directory Grid -->
<section class="directory-section">
    <div class="section-container">
        
        <!-- Active Search/Filters Description -->
        @if($search || $track || $specialization || $graduation_year)
            <div class="search-results-header">
                <p>
                    Showing results for:
                    @if($search) search: "<strong>{{ $search }}</strong>"@endif
                    @if($track) @if($search), @endif track: "<strong>{{ $track }}</strong>"@endif
                    @if($specialization) @if($search || $track), @endif specialization: "<strong>{{ $specialization }}</strong>"@endif
                    @if($graduation_year) @if($search || $track || $specialization), @endif year: "<strong>{{ $graduation_year }}</strong>"@endif
                    — {{ $portfolios->total() }} found
                </p>
                <a href="{{ route('directory') }}" class="clear-search-link">Clear all <i class="fas fa-times"></i></a>
            </div>
        @endif

        @if($portfolios->count())
            <div class="graduates-grid" id="graduates-grid">
                @foreach($portfolios as $portfolio)
                    <a href="{{ route('portfolio.show', $portfolio->slug) }}" class="grad-card" id="grad-card-{{ $portfolio->id }}">
                        <div class="grad-card-header">
                            <div class="grad-avatar">
                                @if($portfolio->profile_picture_path)
                                    <img src="{{ asset('storage/' . $portfolio->profile_picture_path) }}"
                                         alt="{{ $portfolio->user->name }}" loading="lazy">
                                @else
                                    <div class="grad-avatar-placeholder">
                                        {{ strtoupper(substr($portfolio->user->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div class="grad-online-dot"></div>
                            </div>
                        </div>
                        <div class="grad-card-body">
                            <h3 class="grad-name">{{ $portfolio->user->name }}</h3>
                            <p class="grad-headline">{{ $portfolio->headline ?? 'BSA Graduate' }}</p>
                            
                            <!-- Badges -->
                            <div class="grad-meta-badges">
                                @if($portfolio->track)
                                    <span class="meta-badge badge-track">{{ $portfolio->track }}</span>
                                @endif
                                @if($portfolio->specialization)
                                    <span class="meta-badge">{{ $portfolio->specialization }}</span>
                                @endif
                                @if($portfolio->graduation_year)
                                    <span class="meta-badge">Class of {{ $portfolio->graduation_year }}</span>
                                @endif
                            </div>

                            @if($portfolio->location)
                                <p class="grad-location"><i class="fas fa-map-marker-alt"></i> {{ $portfolio->location }}</p>
                            @endif
                            @if($portfolio->bio)
                                <p class="grad-bio">{{ Str::limit($portfolio->bio, 90) }}</p>
                            @endif
                        </div>
                        <div class="grad-card-footer">
                            <div class="grad-social">
                                @foreach($portfolio->socialLinks->take(3) as $link)
                                    <span class="social-chip" title="{{ ucfirst($link->platform) }}">
                                        <i class="{{ $link->iconClass() }}"></i>
                                    </span>
                                @endforeach
                            </div>
                            <span class="view-profile">View Profile <i class="fas fa-arrow-right"></i></span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="pagination-wrapper" style="margin-top: 3rem; display: flex; justify-content: center;">
                {{ $portfolios->appends(request()->query())->links('vendor.pagination.custom') }}
            </div>
        @else
            <!-- Empty State -->
            <div style="text-align: center; padding: 5rem 2rem; background: var(--surface-light); border: 1px dashed var(--border); border-radius: var(--radius-lg); margin-top: 1rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🎓</div>
                <h3 style="font-family: var(--font-display); font-size: 1.25rem; color: var(--accent); margin-bottom: 0.5rem;">No graduates found</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem; max-width: 400px; margin-inline: auto;">
                    No portfolios match your active search terms or filters. Try adjusting your selections.
                </p>
                <a href="{{ route('directory') }}" class="btn-primary" style="padding: 0.6rem 1.5rem; font-size: 0.88rem;">
                    Reset Filters
                </a>
            </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
@guest
<section class="cta-section">
    <div class="cta-content">
        <h2>Are you a BSA Graduate?</h2>
        <p>Contact BSA Academy to get your personal portfolio account and start getting discovered by employers.</p>
        <a href="{{ route('login') }}" class="btn-primary btn-large" id="cta-login-btn">
            <i class="fas fa-sign-in-alt"></i> Login to Your Portfolio
        </a>
    </div>
</section>
@endguest

<!-- ── Sliding Filters Drawer ── -->
<div class="filter-drawer-overlay" id="filter-drawer-overlay" onclick="toggleFilterDrawer()"></div>
<div class="filter-drawer" id="filter-drawer">
    <div class="filter-drawer-header">
        <h3><i class="fas fa-sliders-h"></i> Search Filters</h3>
        <button class="filter-drawer-close" onclick="toggleFilterDrawer()">&times;</button>
    </div>
    
    <form method="GET" action="{{ route('directory') }}" class="filter-drawer-body">
        <!-- Retain search input value -->
        @if($search)
            <input type="hidden" name="search" value="{{ $search }}">
        @endif

        <!-- Track select dropdown -->
        <div class="filter-group">
            <label for="track-select">Track</label>
            <select name="track" id="track-select" class="filter-control">
                <option value="">All Tracks</option>
                @foreach($tracks as $t)
                    <option value="{{ $t }}" {{ $track === $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>

        <!-- Specialization input field -->
        <div class="filter-group">
            <label for="specialization-filter">Specialization</label>
            <input type="text" name="specialization" id="specialization-filter" class="filter-control"
                   value="{{ $specialization ?? '' }}" placeholder="e.g. Laravel, React">
        </div>

        <!-- Graduation Year select dropdown -->
        <div class="filter-group">
            <label for="year-select">Graduation Year</label>
            <select name="graduation_year" id="year-select" class="filter-control">
                <option value="">All Years</option>
                @for($y = date('Y') + 4; $y >= 2025; $y--)
                    <option value="{{ $y }}" {{ $graduation_year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <div class="filter-drawer-footer">
            <button type="submit" class="btn-primary" style="width: 100%;">Apply Filters</button>
            @if($track || $specialization || $graduation_year)
                <a href="{{ route('directory') }}" class="btn-filter-reset" style="display:block; text-align:center; margin-top:1rem;">Clear All</a>
            @endif
        </div>
    </form>
</div>

<script>
function toggleFilterDrawer() {
    const drawer = document.getElementById('filter-drawer');
    const overlay = document.getElementById('filter-drawer-overlay');
    if (drawer && overlay) {
        drawer.classList.toggle('open');
        overlay.classList.toggle('open');
    }
}
</script>
@endsection
