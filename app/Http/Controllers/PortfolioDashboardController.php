<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\PortfolioView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortfolioDashboardController extends Controller
{
    /**
     * Show the main dashboard.
     */
    public function index()
    {
        $user      = auth()->user();
        $portfolio = $user->portfolio()->with(['skills', 'projects.images', 'socialLinks'])->firstOrCreate(
            ['user_id' => $user->id],
            ['title' => $user->name, 'slug' => Str::slug($user->name) . '-' . $user->id]
        );

        return view('dashboard.index', compact('portfolio'));
    }

    // ── Section 1: Personal Information ───────────────────────

    public function updatePersonal(Request $request)
    {
        $portfolio = auth()->user()->portfolio;

        $validated = $request->validate([
            'title'           => ['nullable', 'string', 'max:255'],
            'bio'             => ['nullable', 'string', 'max:2000'],
            'location'        => ['nullable', 'string', 'max:255'],
            'hero_prefix'     => ['nullable', 'string', 'max:255'],
            'hero_suffix'     => ['nullable', 'string', 'max:1000'],
            'track'           => ['nullable', 'string', 'max:255'],
            'specialization'  => ['nullable', 'string', 'max:255'],
            'graduation_year' => ['nullable', 'integer', 'min:2020', 'max:2035'],
            'headline'        => ['nullable', 'array', 'max:3'],
            'headline.*'      => ['nullable', 'string', 'max:100'],
        ]);

        // Join the array into a comma-separated string
        $headlines = array_filter(array_map('trim', $request->input('headline', [])));
        $validated['headline'] = implode(', ', $headlines) ?: null;

        $portfolio->update($validated);

        return back()->with('success', 'Personal information updated!');
    }

    public function uploadProfilePicture(Request $request, \App\Services\ImageOptimizer $optimizer)
    {
        $request->validate([
            'profile_picture' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $portfolio = auth()->user()->portfolio;

        // Delete old picture
        if ($portfolio->profile_picture_path) {
            Storage::disk('r2')->delete($portfolio->profile_picture_path);
        }

        $path = $optimizer->optimizeProfile($request->file('profile_picture'));
        $portfolio->update(['profile_picture_path' => $path]);

        return back()->with('success', 'Profile picture updated!');
    }

    // ── Section 2: Contact Information ────────────────────────

    public function updateContact(Request $request)
    {
        $portfolio = auth()->user()->portfolio;

        $validated = $request->validate([
            'phone'        => ['nullable', 'string', 'max:30'],
            'whatsapp'     => ['nullable', 'string', 'max:30'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'github_url'   => ['nullable', 'url', 'max:255'],
            'behance_url'  => ['nullable', 'url', 'max:255'],
        ]);

        $portfolio->update($validated);

        return back()->with('success', 'Contact information updated!');
    }

    // ── Section 3: CV Upload ──────────────────────────────────

    public function uploadCv(Request $request)
    {
        $request->validate([
            'cv' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $portfolio = auth()->user()->portfolio;

        // Delete old CV
        if ($portfolio->cv_path) {
            Storage::disk('r2_private')->delete($portfolio->cv_path);
        }

        $path = $request->file('cv')->store('cvs', 'r2_private');
        $portfolio->update(['cv_path' => $path]);

        return back()->with('success', 'CV uploaded successfully!');
    }

    // ── Section 4: Publish Toggle ─────────────────────────────

    public function togglePublish()
    {
        $portfolio = auth()->user()->portfolio;
        $portfolio->update(['is_published' => ! $portfolio->is_published]);

        $msg = $portfolio->is_published
            ? '🚀 Your portfolio is now live!'
            : 'Portfolio set to draft.';

        return back()->with('success', $msg);
    }

    // ── Section 5: Portfolio Update (legacy support) ──────────

    public function update(Request $request)
    {
        return $this->updatePersonal($request);
    }

    // ── Analytics ─────────────────────────────────────────────

    public function analytics()
    {
        $portfolio = auth()->user()->portfolio;

        $totalViews   = $portfolio->views()->count();
        $monthlyViews = $portfolio->views()
            ->where('viewed_at', '>=', now()->startOfMonth())
            ->count();

        $viewsByDay = $portfolio->views()
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as count')
            ->where('viewed_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $cvDownloads   = $portfolio->cvDownloads()->count();
        $projectClicks = $portfolio->projectClicks()->count();

        return view('dashboard.analytics', compact('portfolio', 'totalViews', 'monthlyViews', 'viewsByDay', 'cvDownloads', 'projectClicks'));
    }
}
