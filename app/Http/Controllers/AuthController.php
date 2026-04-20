<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('login.login');
    }

    public function authenticate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],

        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Redirection selon le rôle
            return match ($user->role) {
                'admin' => redirect()->route('dashboard.admin'),
                'usager' => redirect()->route('dashboard.usager'),
                'agent' => redirect()->route('dashboard.agent'),
            };
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register()
    {
        return view('login.register');
    }

    public function adminCreateAccount()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->view('pages.error.denied-page', [], 403);
        }

        return view('components.creer_all_compte');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed'],
            'role' => ['nullable', 'in:usager,agent,admin'],
        ]);

        $validated['role'] = $validated['role'] ?? 'usager';

        User::create($validated);

        // Si admin connecté → retour dashboard admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.users')->with('success', 'Compte créé avec succès.');
        }

        return redirect()->route('login')->with('success', 'Compte créé avec succès.');
    }

    public function logout(Request $request): \Illuminate\Http\RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
