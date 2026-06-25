<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function __construct(private ImageOptimizer $optimizer) {}

    // ── Index: Graduate's project management hub ───────────────

    public function index()
    {
        $portfolio = auth()->user()->portfolio;
        abort_if(! $portfolio, 403, 'No portfolio found.');

        $projects = $portfolio->projects()
                              ->with(['images'])
                              ->orderBy('sort_order')
                              ->orderBy('created_at', 'desc')
                              ->get();

        return view('dashboard.projects.index', compact('portfolio', 'projects'));
    }

    // ── Create: Show the create form ──────────────────────────

    public function create()
    {
        $portfolio = auth()->user()->portfolio;
        abort_if(! $portfolio, 403);

        return view('dashboard.projects.create', compact('portfolio'));
    }

    // ── Store: Validate, save, optimize images ────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string|max:5000',
            'tech_stack'    => 'nullable|string|max:500',
            'project_url'   => 'nullable|url|max:2048',
            'github_url'    => 'nullable|url|max:2048',
            'cover_image'   => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'gallery'       => 'nullable|array|max:20',
            'gallery.*'     => 'file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
            'gallery_alt'   => 'nullable|array',
            'gallery_alt.*' => 'nullable|string|max:255',
        ]);

        $portfolio = auth()->user()->portfolio;
        abort_if(! $portfolio, 403);

        $nextSort = ($portfolio->projects()->max('sort_order') ?? 0) + 1;

        $project = $portfolio->projects()->create([
            'title'       => $data['title'],
            'description' => $data['description'],
            'tech_stack'  => $data['tech_stack']  ?? null,
            'project_url' => $data['project_url'] ?? null,
            'github_url'  => $data['github_url']  ?? null,
            'sort_order'  => $nextSort,
        ]);

        // Handle cover image
        if ($request->hasFile('cover_image')) {
            $coverPaths = $this->optimizer->optimizeCover($request->file('cover_image'));
            $project->update(['cover_image_path' => $coverPaths['path']]);
        }

        // Handle gallery images
        if ($request->hasFile('gallery')) {
            $altTexts = $data['gallery_alt'] ?? [];
            foreach ($request->file('gallery') as $index => $file) {
                $this->storeProjectFile(
                    $file,
                    $project,
                    $index + 1,
                    $altTexts[$index] ?? null
                );
            }
        }

        return redirect()
            ->route('dashboard.projects.index')
            ->with('success', '🚀 Project "' . $project->title . '" added successfully!');
    }

    // ── Edit: Show edit form with existing data ────────────────

    public function edit(Project $project)
    {
        $this->authorizeProject($project);

        $project->load('images');

        return view('dashboard.projects.edit', [
            'project'   => $project,
            'portfolio' => auth()->user()->portfolio,
        ]);
    }

    // ── Update: Save edited fields + optional new files ────────

    public function update(Request $request, Project $project)
    {
        $this->authorizeProject($project);

        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string|max:5000',
            'tech_stack'    => 'nullable|string|max:500',
            'project_url'   => 'nullable|url|max:2048',
            'github_url'    => 'nullable|url|max:2048',
            'cover_image'   => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'gallery'       => 'nullable|array|max:20',
            'gallery.*'     => 'file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
            'gallery_alt'   => 'nullable|array',
            'gallery_alt.*' => 'nullable|string|max:255',
        ]);

        $project->update([
            'title'       => $data['title'],
            'description' => $data['description'],
            'tech_stack'  => $data['tech_stack']  ?? null,
            'project_url' => $data['project_url'] ?? null,
            'github_url'  => $data['github_url']  ?? null,
        ]);

        // Replace cover image if a new one was uploaded
        if ($request->hasFile('cover_image')) {
            // Delete old cover
            if ($project->cover_image_path) {
                $this->deleteCoverFiles($project->cover_image_path);
            }

            $coverPaths = $this->optimizer->optimizeCover($request->file('cover_image'));
            $project->update(['cover_image_path' => $coverPaths['path']]);
        }

        // Add new gallery files
        if ($request->hasFile('gallery')) {
            $currentMax = $project->images()->max('sort_order') ?? 0;
            $altTexts   = $data['gallery_alt'] ?? [];

            foreach ($request->file('gallery') as $index => $file) {
                $this->storeProjectFile(
                    $file,
                    $project,
                    $currentMax + $index + 1,
                    $altTexts[$index] ?? null
                );
            }
        }

        return redirect()
            ->route('dashboard.projects.edit', $project->id)
            ->with('success', '✅ Project updated successfully!');
    }

    // ── Destroy: Delete project and all its files ──────────────

    public function destroy(Project $project)
    {
        $this->authorizeProject($project);

        // Delete cover image
        if ($project->cover_image_path) {
            $isGalleryImage = $project->images
                ->pluck('image_path')
                ->contains($project->cover_image_path);

            if (! $isGalleryImage) {
                $this->deleteCoverFiles($project->cover_image_path);
            }
        }

        // Delete all gallery images (including thumbnails)
        foreach ($project->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            if ($image->thumbnail_path) {
                Storage::disk('public')->delete($image->thumbnail_path);
            }
        }

        $project->delete();

        return redirect()
            ->route('dashboard.projects.index')
            ->with('success', 'Project deleted.');
    }

    // ── Add images to an existing project ─────────────────────

    public function addImage(Request $request, Project $project)
    {
        $this->authorizeProject($project);

        $request->validate([
            'images'   => 'required|array|max:20',
            'images.*' => 'file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);

        $currentMax = $project->images()->max('sort_order') ?? 0;

        foreach ($request->file('images') as $index => $file) {
            $this->storeProjectFile($file, $project, $currentMax + $index + 1);
        }

        return back()->with('success', 'Files uploaded successfully!');
    }

    // ── Delete a single gallery image ─────────────────────────

    public function destroyImage(ProjectImage $image)
    {
        $project = $image->project;
        abort_if(auth()->id() !== $project->portfolio->user_id, 403);

        Storage::disk('public')->delete($image->image_path);

        if ($image->thumbnail_path) {
            Storage::disk('public')->delete($image->thumbnail_path);
        }

        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    // ── Set a gallery image as the project cover ───────────────

    public function setCover(ProjectImage $image)
    {
        $project = $image->project;
        $this->authorizeProject($project);

        abort_if($image->isPdf(), 422, 'Cannot set a PDF as cover.');

        // Delete old standalone cover file if it's not a gallery image
        if ($project->cover_image_path && ! $project->images->pluck('image_path')->contains($project->cover_image_path)) {
            $this->deleteCoverFiles($project->cover_image_path);
        }

        $project->update(['cover_image_path' => $image->image_path]);

        return back()->with('success', 'Cover image updated!');
    }

    // ── Remove cover without deleting the underlying file ──────

    public function removeCover(Project $project)
    {
        $this->authorizeProject($project);

        // Only delete if the cover path is not shared with a gallery image
        if ($project->cover_image_path) {
            $isGalleryImage = $project->images
                ->pluck('image_path')
                ->contains($project->cover_image_path);

            if (! $isGalleryImage) {
                $this->deleteCoverFiles($project->cover_image_path);
            }
        }

        $project->update(['cover_image_path' => null]);

        return back()->with('success', 'Cover image removed.');
    }

    // ── Private Helpers ────────────────────────────────────────

    /**
     * Store a single gallery image with optimization.
     */
    private function storeProjectFile(
        $file,
        Project $project,
        int $sortOrder,
        ?string $altText = null
    ): void {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'pdf') {
            $path = $file->store('project-documents', 'public');
            $project->images()->create([
                'image_path'     => $path,
                'thumbnail_path' => null,
                'file_type'      => 'pdf',
                'sort_order'     => $sortOrder,
                'alt_text'       => $altText ?? $project->title,
                'caption'        => $file->getClientOriginalName(),
            ]);
        } else {
            $result = $this->optimizer->optimizeGallery($file);
            $project->images()->create([
                'image_path'     => $result['path'],
                'thumbnail_path' => $result['thumbnail'],
                'file_type'      => 'image',
                'sort_order'     => $sortOrder,
                'alt_text'       => $altText ?? $project->title,
            ]);
        }
    }

    /**
     * Delete both the main cover image file and its thumbnail.
     */
    private function deleteCoverFiles(?string $path): void
    {
        if (!$path) {
            return;
        }

        Storage::disk('public')->delete($path);

        $filename = basename($path);
        Storage::disk('public')->delete('project-covers/thumbnails/' . $filename);
    }

    /**
     * Authorization check: the project must belong to the logged-in user's portfolio.
     */
    private function authorizeProject(Project $project): void
    {
        abort_if(
            auth()->id() !== $project->portfolio->user_id,
            403,
            'You do not own this project.'
        );
    }
}
