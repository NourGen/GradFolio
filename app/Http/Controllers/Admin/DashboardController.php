<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Portfolio;
use App\Models\PortfolioView;
use App\Models\Project;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers        = User::count();
        $totalPortfolios   = Portfolio::count();
        $totalGraduates    = User::where('role', 'graduate')->count();
        $publishedCount    = Portfolio::where('is_published', true)->count();
        $unpublishedCount  = Portfolio::where('is_published', false)->count();
        $suspendedCount    = User::where('role', 'graduate')->where('is_suspended', true)->count();
        $verifiedCount     = Portfolio::where('is_verified', true)->count();
        $totalProjects     = Project::count();
        $totalViews        = PortfolioView::count();

        $recentGraduates = User::with('portfolio')
            ->where('role', 'graduate')
            ->latest()
            ->take(5)
            ->get();

        $topPortfolios = Portfolio::with('user')
            ->where('is_published', true)
            ->withCount('views')
            ->orderByDesc('views_count')
            ->take(5)
            ->get();

        $dailyViews = PortfolioView::selectRaw('DATE(viewed_at) as date, COUNT(*) as count')
            ->where('viewed_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalPortfolios', 'totalGraduates', 'publishedCount',
            'unpublishedCount', 'suspendedCount', 'verifiedCount', 'totalProjects',
            'totalViews', 'recentGraduates', 'topPortfolios', 'dailyViews'
        ));
    }
}
