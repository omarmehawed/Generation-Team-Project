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
        // 👇 ضفنا stateless هنا
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        // 👇 عملنا كومنت للـ try و catch عشان نفضح الإيرور
        // try {
            
            // 👇 ضفنا stateless هنا كمان
            $googleUser = Socialite::driver('google')->stateless()->user();

            if (Auth::check()) {
                // Link account
                /** @var \App\Models\User $user */
                $user = Auth::user();
                
                $existingUser = User::where('google_id', $googleUser->getId())
                                     ->where('id', '!=', $user->id)
                                     ->first();
                if ($existingUser) {
                    return redirect()->route('profile.show')->with('error', 'This Google account is already linked to another user.');
                }
                
                $user->google_id = $googleUser->getId();
                $user->google_email = $googleUser->getEmail();
                $user->save();

                return redirect()->route('profile.show')->with('success', 'Google account linked successfully.');
            } else {
                // Login
                $user = User::where('google_id', $googleUser->getId())->first();

                if ($user) {
                    Auth::login($user);
                    return redirect()->intended('/dashboard');
                } else {
                    // 🚨 لو الإيميل مش مربوط في الداتابيز، كودك طبيعي هيرجعك هنا!
                    return redirect()->route('login')->with('error', 'No account linked to this Google account. Please login with your credentials and link your account from your profile first.');
                }
            }
            
        // } catch (\Exception $e) {
        //     $redirectRoute = Auth::check() ? 'profile.show' : 'login';
        //     return redirect()->route($redirectRoute)->with('error', 'An error occurred: ' . $e->getMessage());
        // }
    }

    public function unlinkGoogle(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user && $user->google_id) {
            $user->google_id = null;
            $user->google_email = null;
            $user->save();

            return redirect()->route('profile.show')->with('success', 'Google account unlinked successfully.');
        }

        return redirect()->route('profile.show')->with('error', 'No Google account is currently linked.');
    }
}