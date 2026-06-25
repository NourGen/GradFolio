@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar" id="admin-sidebar">
        <div class="admin-sidebar-header">
            <span class="brand-icon">🎓</span>
            <span class="brand-text">Admin Panel</span>
        </div>
        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link active" id="nav-dashboard">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="{{ route('admin.graduates.index') }}" class="admin-nav-link" id="nav-graduates">
                <i class="fas fa-users"></i> Graduates
            </a>
            <a href="{{ route('admin.projects.index') }}" class="admin-nav-link" id="nav-projects">
                <i class="fas fa-code"></i> Manage Projects
            </a>
            <a href="{{ route('directory') }}" target="_blank" class="admin-nav-link" id="nav-directory">
                <i class="fas fa-globe"></i> View Site
            </a>
        </nav>
    </aside>

    <div class="admin-main">
        <div class="admin-topbar">
            <h1>Admin Dashboard</h1>
            <span class="admin-user-badge">👤 {{ auth()->user()->name }}</span>
        </div>

        <!-- Stats Cards (Grid of 8) -->
        <div class="admin-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div class="admin-stat-card" id="stat-users">
                <div class="admin-stat-icon" style="background:linear-gradient(135deg,#8e2de2,#4a00e0)">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="admin-stat-num">{{ $totalUsers }}</div>
                    <div class="admin-stat-label">Total Users</div>
                </div>
            </div>
            
            <div class="admin-stat-card" id="stat-portfolios">
                <div class="admin-stat-icon" style="background:linear-gradient(135deg,#3a7bd5,#3a6073)">
                    <i class="fas fa-address-book"></i>
                </div>
                <div>
                    <div class="admin-stat-num">{{ $totalPortfolios }}</div>
                    <div class="admin-stat-label">Total Portfolios</div>
                </div>
            </div>

            <div class="admin-stat-card" id="stat-graduates">
                <div class="admin-stat-icon" style="background:linear-gradient(135deg,#667eea,#764ba2)">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <div class="admin-stat-num">{{ $totalGraduates }}</div>
                    <div class="admin-stat-label">Total Graduates</div>
                </div>
            </div>
            
            <div class="admin-stat-card" id="stat-published">
                <div class="admin-stat-icon" style="background:linear-gradient(135deg,#11998e,#38ef7d)">
                    <i class="fas fa-globe-europe"></i>
                </div>
                <div>
                    <div class="admin-stat-num">{{ $publishedCount }}</div>
                    <div class="admin-stat-label">Live Portfolios</div>
                </div>
            </div>

            <div class="admin-stat-card" id="stat-verified">
                <div class="admin-stat-icon" style="background:linear-gradient(135deg,#0052D4,#4364F7)">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="admin-stat-num">{{ $verifiedCount }}</div>
                    <div class="admin-stat-label">Verified Portfolios</div>
                </div>
            </div>

            <div class="admin-stat-card" id="stat-suspended">
                <div class="admin-stat-icon" style="background:linear-gradient(135deg,#e53935,#e35d5b)">
                    <i class="fas fa-user-slash"></i>
                </div>
                <div>
                    <div class="admin-stat-num">{{ $suspendedCount }}</div>
                    <div class="admin-stat-label">Suspended Accounts</div>
                </div>
            </div>

            <div class="admin-stat-card" id="stat-projects">
                <div class="admin-stat-icon" style="background:linear-gradient(135deg,#f7971e,#ffd200)">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div>
                    <div class="admin-stat-num">{{ $totalProjects }}</div>
                    <div class="admin-stat-label">Total Projects</div>
                </div>
            </div>

            <div class="admin-stat-card" id="stat-views">
                <div class="admin-stat-icon" style="background:linear-gradient(135deg,#ee0979,#ff6a00)">
                    <i class="fas fa-eye"></i>
                </div>
                <div>
                    <div class="admin-stat-num">{{ $totalViews }}</div>
                    <div class="admin-stat-label">Total Page Views</div>
                </div>
            </div>
        </div>

        <div class="admin-tables-grid">
            <!-- Recent Graduates -->
            <div class="admin-table-card" id="recent-graduates-card">
                <div class="admin-card-header">
                    <h3><i class="fas fa-user-clock"></i> Recent Graduates</h3>
                    <a href="{{ route('admin.graduates.index') }}" class="view-all-link">View All</a>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentGraduates as $grad)
                        <tr>
                            <td>{{ $grad->name }}</td>
                            <td>{{ $grad->email }}</td>
                            <td>
                                @if($grad->is_suspended)
                                    <span class="badge badge-suspended" style="background:rgba(229,57,53,0.1); color:#e53935; border:1px solid rgba(229,57,53,0.2); padding:2px 8px; border-radius:12px; font-size:0.75rem;">Suspended</span>
                                @elseif($grad->portfolio?->is_published)
                                    <span class="badge badge-live" style="background:rgba(16,185,129,0.1); color:#10b981; border:1px solid rgba(16,185,129,0.2); padding:2px 8px; border-radius:12px; font-size:0.75rem;">Live</span>
                                @else
                                    <span class="badge badge-draft" style="background:rgba(255,255,255,0.05); color:var(--text-muted); border:1px solid var(--border); padding:2px 8px; border-radius:12px; font-size:0.75rem;">Draft</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.graduates.show', $grad->id) }}" class="btn-ghost-sm">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Top Portfolios -->
            <div class="admin-table-card" id="top-portfolios-card">
                <div class="admin-card-header">
                    <h3><i class="fas fa-trophy"></i> Most Viewed Portfolios</h3>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Graduate</th>
                            <th>Headline</th>
                            <th>Views</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topPortfolios as $port)
                        <tr>
                            <td>
                                <a href="{{ route('admin.graduates.show', $port->user_id) }}">
                                    {{ $port->user->name }}
                                </a>
                            </td>
                            <td>{{ $port->headline ?? '—' }}</td>
                            <td><strong>{{ $port->views_count }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
