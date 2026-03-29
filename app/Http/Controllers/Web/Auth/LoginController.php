<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'E-posta veya şifre hatalı.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if (! $user->status) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Hesabınız aktif değil. Lütfen yöneticinize başvurun.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPath($user->role));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectPath(string $role): string
    {
        return match ($role) {
            'coach' => route('coach.dashboard'),
            'manager' => route('manager.dashboard'),
            'player' => route('player.dashboard'),
            default => route('login'),
        };
    }
}
