<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class EmailVerificationController extends Controller
{
    /**
     * Show the email verification notice.
     */
    public function notice()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        return view('auth.verify-email');
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = auth()->user();

        // Ensure the URL is valid and belongs to the authenticated user
        if ($user->getKey() != $id) {
            abort(403);
        }

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('success', 'Email already verified!');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('dashboard')
            ->with('success', '✅ Your email has been verified! Welcome to GradFolio.');
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
