<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login', [
            // Preview logins from PortalDemoSeeder — never exposed outside local development.
            'demoLogins' => app()->isLocal()
                ? User::whereIn('email', ['client@dekifit.test', 'coach@dekifit.test'])
                    ->orderBy('is_admin')
                    ->get(['email', 'is_admin'])
                    ->map(fn ($user) => [
                        'label' => $user->is_admin ? 'Coach admin' : 'Client dashboard',
                        'email' => $user->email,
                        'password' => 'password',
                    ])
                : [],
        ]);
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(
            $request->user()->is_admin ? route('admin.orders') : route('portal.dashboard')
        );
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
