<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            if (Auth::check()) {
                // Link account
                /** @var \App\Models\User $user */
                $user = Auth::user();
                
                // Check if this google account is already linked to someone else
                $existingUser = User::where('google_id', $googleUser->getId())
                                     ->where('id', '!=', $user->id)
                                     ->first();
                if ($existingUser) {
                    return redirect()->route('profile.show')->with('error', 'This Google account is already linked to another user.');
                }
                
                $user->google_id = $googleUser->getId();
                $user->save();

                return redirect()->route('profile.show')->with('success', 'Google account linked successfully.');
            } else {
                // Login
                $user = User::where('google_id', $googleUser->getId())->first();

                if ($user) {
                    Auth::login($user);
                    return redirect()->intended('/dashboard');
                } else {
                    return redirect()->route('login')->with('error', 'No account linked to this Google account. Please login with your credentials and link your account from your profile first.');
                }
            }
        } catch (\Exception $e) {
            $redirectRoute = Auth::check() ? 'profile.show' : 'login';
            return redirect()->route($redirectRoute)->with('error', 'An error occurred during Google authentication. Please try again.');
        }
    }
}
