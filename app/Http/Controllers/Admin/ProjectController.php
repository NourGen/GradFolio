<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a site-wide list of projects.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $projects = Project::with(['portfolio.user'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('portfolio.user', fn($qu) => $qu->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(20);

        return view('admin.projects.index', compact('projects', 'search'));
    }

    /**
     * Delete a project.
     */
    public function destroy(Project $project)
    {
        // Delete cover image
        if ($project->cover_image_path) {
            Storage::disk('r2')->delete($project->cover_image_path);
        }

        // Delete all gallery images (including thumbnails)
        foreach ($project->images as $image) {
            Storage::disk('r2')->delete($image->image_path);
            if ($image->thumbnail_path) {
                Storage::disk('r2')->delete($image->thumbnail_path);
            }
        }

        // Delete the whole project folder
        if ($project->portfolio) {
            $folder = 'project-files/' . $project->portfolio->user_id . '/' . $project->id;
            Storage::disk('r2')->deleteDirectory($folder);
        }

        $project->delete();

        return back()->with('success', 'Project has been deleted by admin.');
    }
}
