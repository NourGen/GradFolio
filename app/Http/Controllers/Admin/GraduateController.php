<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Portfolio;
use App\Notifications\WelcomeGraduateNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GraduateController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $graduates = User::where('role', 'graduate')
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            }))
            ->with('portfolio')
            ->latest()
            ->paginate(20);

        return view('admin.graduates.index', compact('graduates', 'search'));
    }

    public function show(User $user)
    {
        $user->load('portfolio.projects', 'portfolio.skills');
        return view('admin.graduates.show', compact('user'));
    }

    public function create()
    {
        return view('admin.graduates.create');
    }

    /**
     * Admin creates a graduate account.
     * Password is displayed on screen — no email sending (avoids timeout).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        // Generate a secure temporary password
        $temporaryPassword = Str::upper(Str::random(3))
            . Str::lower(Str::random(3))
            . rand(100, 999)
            . '!';

        $user = User::forceCreate([
            'name'                 => $request->name,
            'email'                => $request->email,
            'password'             => Hash::make($temporaryPassword),
            'role'                 => 'graduate',
            'must_change_password' => true,
            'email_verified_at'    => now(),
        ]);

        // Auto-create empty portfolio
        Portfolio::create([
            'user_id'      => $user->id,
            'title'        => $user->name,
            'slug'         => Str::slug($user->name) . '-' . $user->id,
            'is_published' => false,
        ]);

        // Try to send email in background (non-blocking) — ignore if it fails
        try {
            $user->notify(new WelcomeGraduateNotification($temporaryPassword));
        } catch (\Throwable $e) {
            // Email failed — credentials shown on screen instead
            \Log::warning("Welcome email failed for {$user->email}: " . $e->getMessage());
        }

        // Flash credentials to show on screen
        return redirect()->route('admin.graduates.credentials', $user->id)
            ->with([
                'new_graduate_name'     => $user->name,
                'new_graduate_email'    => $user->email,
                'new_graduate_password' => $temporaryPassword,
            ]);
    }

    /**
     * Show the generated credentials page.
     */
    public function credentials(User $user)
    {
        // Only show if we just created this user (session has the password)
        if (! session('new_graduate_password')) {
            return redirect()->route('admin.graduates.index');
        }

        return view('admin.graduates.credentials', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load('portfolio');
        return view('admin.graduates.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'track'           => ['nullable', 'string', 'max:255'],
            'specialization'  => ['nullable', 'string', 'max:255'],
            'graduation_year' => ['nullable', 'integer', 'min:2025', 'max:2035'],
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        $portfolio = $user->portfolio()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'title' => $user->name,
                'slug' => Str::slug($user->name) . '-' . $user->id
            ]
        );

        $portfolio->update([
            'track'           => $request->track,
            'specialization'  => $request->specialization,
            'graduation_year' => $request->graduation_year,
        ]);

        return redirect()->route('admin.graduates.index')
            ->with('success', 'Graduate details updated successfully.');
    }

    public function destroy(User $user)
    {
        abort_if($user->isAdmin(), 403, 'Cannot delete admin accounts.');
        $user->delete();

        return redirect()->route('admin.graduates.index')
            ->with('success', 'Graduate account deleted.');
    }

    public function togglePublish(Portfolio $portfolio)
    {
        $portfolio->update(['is_published' => ! $portfolio->is_published]);
        $status = $portfolio->is_published ? 'published' : 'unpublished';

        return back()->with('success', "Portfolio {$status} successfully.");
    }

    public function toggleSuspension(User $user)
    {
        abort_if($user->isAdmin(), 403, 'Cannot suspend admin accounts.');

        $user->update(['is_suspended' => !$user->is_suspended]);

        // Unpublish portfolio if user is suspended
        if ($user->is_suspended && $user->portfolio) {
            $user->portfolio->update(['is_published' => false]);
        }

        $status = $user->is_suspended ? 'suspended' : 'unsuspended';
        return back()->with('success', "Graduate account has been {$status}.");
    }

    public function toggleVerification(Portfolio $portfolio)
    {
        $portfolio->update(['is_verified' => !$portfolio->is_verified]);
        $status = $portfolio->is_verified ? 'verified' : 'unverified';

        return back()->with('success', "Portfolio status updated to {$status}.");
    }
}
