<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileCompletionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (in_array($user->role, ['admin', 'ta']) || $user->profile_completed) {
            return redirect()->route('projects.index');
        }

        return view('profile.complete', compact('user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (in_array($user->role, ['admin', 'ta']) || $user->profile_completed) {
            return redirect('/');
        }

        $request->validate([
            'gender' => 'required|in:male,female',
        ]);

        $user->update([
            'gender' => $request->gender,
            'profile_completed' => true,
        ]);

        return redirect()->route('projects.index')->with('success', 'Profile updated successfully!');
    }
}
