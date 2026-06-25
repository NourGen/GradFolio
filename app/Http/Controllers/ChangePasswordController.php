<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{
    public function show()
    {
        return view('dashboard.change-password');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        // If it's a forced change (admin-created account), skip current password check
        $rules = [
            'password'              => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];

        if (! $user->must_change_password) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $request->validate($rules);

        $user->update([
            'password'             => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')
            ->with('success', '🔐 Password changed successfully!');
    }
}
