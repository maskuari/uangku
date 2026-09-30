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
        $cutoff = now()->subMonthsNoOverflow(2)->toDateString();
        $oldCount = $request->user()->transactions()->whereDate('occurred_on', '<', $cutoff)->count();
        return view('settings.index', compact('cutoff', 'oldCount'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'opening_balance' => ['required', 'integer', 'min:-999999999999', 'max:999999999999'],
            'monthly_budget' => ['nullable', 'integer', 'min:1', 'max:999999999999'],
        ]);
        $request->user()->update($data);
        return back()->with('success', 'Pengaturan berhasil disimpan.');
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
