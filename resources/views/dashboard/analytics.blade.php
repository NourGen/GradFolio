@extends('layouts.app')
@section('title', 'My Analytics')

@section('content')
<div class="analytics-page">
    <div class="analytics-header">
        <h1><i class="fas fa-chart-line"></i> Portfolio Analytics</h1>
        <p>Track how many people are viewing your portfolio.</p>
        <a href="{{ route('dashboard') }}" class="btn-ghost"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>

    <div class="analytics-cards">
        <div class="analytics-stat-card" id="total-views-card">
            <div class="stat-card-icon"><i class="fas fa-eye"></i></div>
            <div class="stat-card-info">
                <span class="stat-big-num">{{ $totalViews }}</span>
                <span class="stat-big-label">Total Views</span>
            </div>
        </div>
        <div class="analytics-stat-card" id="monthly-views-card">
            <div class="stat-card-icon" style="background:linear-gradient(135deg,#00b09b,#96c93d)"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-card-info">
                <span class="stat-big-num">{{ $monthlyViews }}</span>
                <span class="stat-big-label">Views This Month</span>
            </div>
        </div>
        <div class="analytics-stat-card" id="cv-downloads-card">
            <div class="stat-card-icon" style="background:linear-gradient(135deg,#ff9966,#ff5e62)"><i class="fas fa-file-download"></i></div>
            <div class="stat-card-info">
                <span class="stat-big-num">{{ $cvDownloads }}</span>
                <span class="stat-big-label">CV Downloads</span>
            </div>
        </div>
        <div class="analytics-stat-card" id="project-clicks-card">
            <div class="stat-card-icon" style="background:linear-gradient(135deg,#f7971e,#ffd200)"><i class="fas fa-mouse-pointer"></i></div>
            <div class="stat-card-info">
                <span class="stat-big-num">{{ $projectClicks }}</span>
                <span class="stat-big-label">Project Clicks</span>
            </div>
        </div>
    </div>

    <!-- Chart: Views last 30 days -->
    <div class="analytics-chart-card" id="views-chart-card">
        <h3>Views — Last 30 Days</h3>
        @if($viewsByDay->count())
            <div class="bar-chart" id="bar-chart">
                @php $maxViews = $viewsByDay->max('count') ?: 1; @endphp
                @foreach($viewsByDay as $day)
                    <div class="bar-wrap" title="{{ $day->date }}: {{ $day->count }} views">
                        <div class="bar-fill" style="height: {{ round(($day->count / $maxViews) * 100) }}%"></div>
                        <span class="bar-label">{{ \Carbon\Carbon::parse($day->date)->format('M d') }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-chart">
                <i class="fas fa-chart-bar"></i>
                <p>No view data yet. Share your portfolio to get started!</p>
            </div>
        @endif
    </div>

    @if($portfolio->is_published && $portfolio->slug)
    <div class="share-card" id="share-card">
        <h3><i class="fas fa-share"></i> Share Your Portfolio</h3>
        <div class="share-url-row">
            <input type="text" id="portfolio-url" value="{{ route('portfolio.show', $portfolio->slug) }}" readonly>
            <button class="btn-primary" id="copy-url-btn" onclick="copyUrl()">
                <i class="fas fa-copy"></i> Copy Link
            </button>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function copyUrl() {
    const input = document.getElementById('portfolio-url');
    input.select();
    document.execCommand('copy');
    const btn = document.getElementById('copy-url-btn');
    btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
    setTimeout(() => btn.innerHTML = '<i class="fas fa-copy"></i> Copy Link', 2000);
}
</script>
@endsection
