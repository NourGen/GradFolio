<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\PortfolioView;
use App\Models\CvDownload;
use App\Models\Project;
use App\Models\ProjectClick;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicPortfolioController extends Controller
{
    /**
     * Public directory of all published graduates.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $track = $request->get('track');
        $specialization = $request->get('specialization');
        $graduation_year = $request->get('graduation_year');

        $portfolios = Portfolio::with(['user', 'socialLinks', 'skills'])
            ->where('is_published', true)
            ->whereHas('user', fn($q) => $q->where('is_suspended', false))
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('headline', 'like', "%{$search}%")
                      ->orWhere('bio', 'like', "%{$search}%")
                      ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('skills', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($track, function ($query, $track) {
                $query->where('track', $track);
            })
            ->when($specialization, function ($query, $specialization) {
                $query->where('specialization', 'like', "%{$specialization}%");
            })
            ->when($graduation_year, function ($query, $graduation_year) {
                $query->where('graduation_year', $graduation_year);
            })
            ->latest()
            ->paginate(12);

        // Fetch unique tracks and years dynamically for selection dropdowns
        $tracks = Portfolio::where('is_published', true)->whereHas('user', fn($q) => $q->where('is_suspended', false))->whereNotNull('track')->where('track', '!=', '')->distinct()->pluck('track');
        $years = Portfolio::where('is_published', true)->whereHas('user', fn($q) => $q->where('is_suspended', false))->whereNotNull('graduation_year')->distinct()->orderBy('graduation_year', 'desc')->pluck('graduation_year');

        return view('public.directory', compact('portfolios', 'search', 'track', 'specialization', 'graduation_year', 'tracks', 'years'));
    }

    /**
     * View a single graduate's portfolio.
     */
    public function show(Request $request, string $slug)
    {
        $portfolio = Portfolio::with(['user', 'socialLinks', 'projects.images', 'skills'])
            ->where('slug', $slug)
            ->whereHas('user', fn($q) => $q->where('is_suspended', false))
            ->firstOrFail();

        $this->authorize('view', $portfolio);

        // Log the view asynchronously
        $this->recordView($request, $portfolio);

        return view('public.portfolio', compact('portfolio'));
    }

    /**
     * Securely stream a graduate's CV (only for published portfolios or owner).
     */
    public function downloadCv(Request $request, string $slug)
    {
        $portfolio = Portfolio::where('slug', $slug)
            ->whereHas('user', fn($q) => $q->where('is_suspended', false))
            ->firstOrFail();
        $this->authorize('view', $portfolio);

        if (!$portfolio->cv_path || !Storage::disk('private')->exists($portfolio->cv_path)) {
            abort(404, 'CV not found.');
        }

        // Record CV Download
        $this->recordCvDownload($request, $portfolio);

        return Storage::disk('private')->download(
            $portfolio->cv_path,
            $portfolio->user->name . '_CV.pdf'
        );
    }

    /**
     * Record project click (deduplicated per 24h by IP hash).
     */
    public function recordProjectClick(Request $request, Project $project)
    {
        // Deduplicate click per day
        $ipHash = hash('sha256', $request->ip() . date('Y-m-d'));

        $alreadyClicked = ProjectClick::where('project_id', $project->id)
            ->where('ip_hash', $ipHash)
            ->whereDate('clicked_at', today())
            ->exists();

        if (!$alreadyClicked) {
            ProjectClick::create([
                'project_id' => $project->id,
                'ip_hash'    => $ipHash,
                'user_agent' => substr($request->userAgent() ?? '', 0, 512),
                'clicked_at' => now(),
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Record a portfolio view (deduplicated per 24h by IP hash).
     */
    private function recordView(Request $request, Portfolio $portfolio): void
    {
        $ipHash = hash('sha256', $request->ip() . date('Y-m-d'));

        $alreadyViewed = PortfolioView::where('portfolio_id', $portfolio->id)
            ->where('ip_hash', $ipHash)
            ->whereDate('viewed_at', today())
            ->exists();

        if (!$alreadyViewed) {
            PortfolioView::create([
                'portfolio_id' => $portfolio->id,
                'ip_hash'      => $ipHash,
                'user_agent'   => substr($request->userAgent() ?? '', 0, 512),
                'viewed_at'    => now(),
            ]);
        }
    }

    /**
     * Record a CV download (deduplicated per 24h by IP hash).
     */
    private function recordCvDownload(Request $request, Portfolio $portfolio): void
    {
        $ipHash = hash('sha256', $request->ip() . date('Y-m-d'));

        $alreadyDownloaded = CvDownload::where('portfolio_id', $portfolio->id)
            ->where('ip_hash', $ipHash)
            ->whereDate('downloaded_at', today())
            ->exists();

        if (!$alreadyDownloaded) {
            CvDownload::create([
                'portfolio_id'  => $portfolio->id,
                'ip_hash'       => $ipHash,
                'user_agent'    => substr($request->userAgent() ?? '', 0, 512),
                'downloaded_at' => now(),
            ]);
        }
    }
}
