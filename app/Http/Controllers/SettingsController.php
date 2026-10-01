<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $cutoff = now()->subMonthsNoOverflow(2)->toDateString();
        $oldCount = $user->transactions()->whereDate('occurred_on', '<', $cutoff)->count();
        $income = (int) $user->transactions()->where('type', 'income')->sum('amount');
        $expense = (int) $user->transactions()->where('type', 'expense')->sum('amount');
        $currentBalance = $user->opening_balance + $income - $expense;
        return view('settings.index', compact('cutoff', 'oldCount', 'currentBalance'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'current_balance' => ['required', 'integer', 'min:-999999999999', 'max:999999999999'],
            'monthly_budget' => ['nullable', 'integer', 'min:1', 'max:999999999999'],
        ]);

        DB::transaction(function () use ($request, $data) {
            $user = $request->user()->newQuery()->lockForUpdate()->findOrFail($request->user()->id);
            $income = (int) $user->transactions()->where('type', 'income')->sum('amount');
            $expense = (int) $user->transactions()->where('type', 'expense')->sum('amount');
            $user->update([
                'name' => $data['name'],
                'monthly_budget' => $data['monthly_budget'],
                'opening_balance' => $data['current_balance'] - $income + $expense,
            ]);
        });

        $request->user()->refresh();
        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function balance(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_balance' => ['required', 'integer', 'min:-999999999999', 'max:999999999999'],
        ]);

        DB::transaction(function () use ($request, $data) {
            $user = $request->user()->newQuery()->lockForUpdate()->findOrFail($request->user()->id);
            $income = (int) $user->transactions()->where('type', 'income')->sum('amount');
            $expense = (int) $user->transactions()->where('type', 'expense')->sum('amount');
            $user->opening_balance = $data['current_balance'] - $income + $expense;
            $user->save();
        });

        $request->user()->refresh();
        return redirect()->route('dashboard')->with('success', 'Saldo berhasil disimpan. Kamu siap mulai mencatat keuangan.');
    }

    public function password(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $request->user()->update(['password' => $data['password']]);
        return back()->with('success', 'Kata sandi berhasil diubah.');
    }

    public function prune(Request $request): RedirectResponse
    {
        $request->validate(['confirmation' => ['required', 'in:HAPUS']]);
        $cutoff = now()->subMonthsNoOverflow(2)->toDateString();
        $count = DB::transaction(function () use ($request, $cutoff) {
            $user = $request->user()->newQuery()->lockForUpdate()->findOrFail($request->user()->id);
            $old = $user->transactions()->whereDate('occurred_on', '<', $cutoff);
            $income = (int) (clone $old)->where('type', 'income')->sum('amount');
            $expense = (int) (clone $old)->where('type', 'expense')->sum('amount');
            $count = (clone $old)->count();
            $user->opening_balance += $income - $expense;
            $user->save();
            $old->delete();
            return $count;
        });
        $request->user()->refresh();
        return back()->with('success', $count.' transaksi lama dihapus. Saldo saat ini tetap terjaga.');
    }
}
