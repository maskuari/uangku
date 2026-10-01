<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function loginForm(): View { return view('auth.login'); }
    public function registerForm(): View { return view('auth.register'); }

    public function login(Request $request): RedirectResponse
    {
        $request->merge(['email' => strtolower((string) $request->input('email'))]);
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        $this->provisionAdmin($credentials);
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])->onlyInput('email');
        }
        $request->session()->regenerate();
        $user = $request->user();
        if (! $user->isAdmin() && $user->opening_balance === 0 && ! $user->transactions()->exists()) {
            $request->session()->flash('show_balance_setup', true);
        }
        if ($user->isAdmin()) {
            return redirect()->route('admin.index');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function register(Request $request): RedirectResponse
    {
        $request->merge(['email' => strtolower((string) $request->input('email'))]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255', Rule::notIn([config('admin.email')]), 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $user = User::create($data);
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->flash('show_balance_setup', true);
        return redirect()->route('dashboard')->with('success', 'Akun berhasil dibuat. Mulai dengan mengisi saldo awal.');
    }

    private function provisionAdmin(array $credentials): void
    {
        $adminEmail = strtolower((string) config('admin.email'));
        if (strtolower($credentials['email']) !== $adminEmail || User::where('email', $adminEmail)->exists()) {
            return;
        }

        $bootstrapPassword = (string) config('admin.bootstrap_password');
        if ($bootstrapPassword === '' || ! hash_equals($bootstrapPassword, $credentials['password'])) {
            return;
        }

        User::create([
            'name' => 'Admin Uangku',
            'email' => $adminEmail,
            'password' => $credentials['password'],
        ]);
    }
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
