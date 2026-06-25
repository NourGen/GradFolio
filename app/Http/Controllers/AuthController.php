<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // ── Register ───────────────────────────────────────────────

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'terms'    => ['accepted'],
        ], [
            'terms.accepted' => 'You must accept the Terms & Conditions.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password, // auto-hashed via cast
            'role'     => 'graduate',
        ]);

        // Auto-create empty portfolio
        Portfolio::create([
            'user_id'      => $user->id,
            'title'        => $user->name,
            'slug'         => Str::slug($user->name) . '-' . $user->id,
            'is_published' => false,
        ]);

        // Trigger email verification event
        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Welcome to GradFolio! Please verify your email to unlock all features.');
    }

    // ── Login ──────────────────────────────────────────────────

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records.']);
    }

    // ── Logout ─────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }
}
