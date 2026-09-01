<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'badge_id' => 'required|string',
            'date_of_birth' => 'required|date',
            'captcha' => 'required',
        ]);

        // Simple captcha validation (session-based)
        if ($request->captcha !== session('captcha_answer')) {
            return back()->withErrors(['captcha' => 'Captcha is incorrect.'])->withInput();
        }

        $user = User::where('badge_id', $request->badge_id)
            ->whereDate('date_of_birth', $request->date_of_birth)
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return back()->withErrors(['badge_id' => 'Invalid Credential!'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));

        $user->update(['last_login_at' => now()]);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Generate simple math captcha
     */
    public function generateCaptcha(Request $request)
    {
        $a = rand(1, 20);
        $b = rand(1, 20);
        $answer = (string)($a + $b);

        session(['captcha_answer' => $answer]);

        return response()->json([
            'question' => "{$a} + {$b} = ?",
        ]);
    }
}
