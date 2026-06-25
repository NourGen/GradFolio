<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\Portfolio;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    /**
     * Store a new skill for the authenticated graduate's portfolio.
     */
    public function store(Request $request)
    {
        $portfolio = auth()->user()->portfolio;

        abort_if(! $portfolio, 403);

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:80'],
            'level' => ['required', 'in:beginner,intermediate,advanced,expert'],
        ]);

        // Limit skills to 30 per portfolio
        if ($portfolio->skills()->count() >= 30) {
            return back()->with('error', 'You can add a maximum of 30 skills.');
        }

        $portfolio->skills()->create([
            'name'       => $validated['name'],
            'level'      => $validated['level'],
            'sort_order' => $portfolio->skills()->count(),
        ]);

        return back()->with('success', 'Skill added successfully!');
    }

    /**
     * Update an existing skill.
     */
    public function update(Request $request, Skill $skill)
    {
        $this->authorize('update', $skill->portfolio);

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:80'],
            'level' => ['required', 'in:beginner,intermediate,advanced,expert'],
        ]);

        $skill->update($validated);

        return back()->with('success', 'Skill updated!');
    }

    /**
     * Delete a skill.
     */
    public function destroy(Skill $skill)
    {
        $this->authorize('update', $skill->portfolio);

        $skill->delete();

        return back()->with('success', 'Skill removed.');
    }
}
