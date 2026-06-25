<?php

namespace App\Http\Controllers;

use App\Models\SocialLink;
use Illuminate\Http\Request;

class SocialLinkController extends Controller
{

    public function store(Request $request)
    {
        $data = $request->validate([
            'platform' => 'required|string|max:50',
            'url'      => 'required|string|max:2048',
        ]);

        $portfolio = auth()->user()->portfolio;

        // Prevent duplicate platforms
        $portfolio->socialLinks()->updateOrCreate(
            ['platform' => $data['platform']],
            ['url'      => $data['url']]
        );

        return back()->with('success', ucfirst($data['platform']) . ' link saved!');
    }

    public function destroy(SocialLink $socialLink)
    {
        // Ensure the user owns this social link
        if ($socialLink->portfolio->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $socialLink->delete();

        return back()->with('success', 'Social link removed.');
    }
}
