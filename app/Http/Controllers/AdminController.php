<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);
        $search = trim($filters['search'] ?? '');
        $users = User::query()
            ->select(['id', 'name', 'email', 'created_at'])
            ->where('email', '!=', config('admin.email'))
            ->when($search !== '', function ($query) use ($search) {
                $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $search);
                $query->where(function ($query) use ($escaped) {
                    $query->where('name', 'like', "%{$escaped}%")
                        ->orWhere('email', 'like', "%{$escaped}%");
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.index', compact('users', 'search'));
    }

    public function editPassword(User $user): View
    {
        $this->ensureManagedUser($user);

        return view('admin.password', compact('user'));
    }

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $this->ensureManagedUser($user);
        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $user->update(['password' => $data['password']]);

        return redirect()->route('admin.index')->with('success', 'Password '.$user->email.' berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->ensureManagedUser($user);
        $email = $user->email;
        $user->delete();

        return back()->with('success', 'Akun '.$email.' berhasil dihapus.');
    }

    private function ensureManagedUser(User $user): void
    {
        abort_if($user->isAdmin(), 403);
    }
}
