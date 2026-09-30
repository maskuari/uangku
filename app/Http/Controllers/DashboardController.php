<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        $income = (int) $user->transactions()->where('type', 'income')->sum('amount');
        $expense = (int) $user->transactions()->where('type', 'expense')->sum('amount');
        $balance = $user->opening_balance + $income - $expense;
        $todayExpense = (int) $user->transactions()->where('type', 'expense')->whereDate('occurred_on', $today)->sum('amount');
        $monthIncome = (int) $user->transactions()->where('type', 'income')->whereBetween('occurred_on', [$monthStart, $monthEnd])->sum('amount');
        $monthExpense = (int) $user->transactions()->where('type', 'expense')->whereBetween('occurred_on', [$monthStart, $monthEnd])->sum('amount');
        $weekTransactions = $user->transactions()->where('type', 'expense')
            ->whereBetween('occurred_on', [now()->subDays(6)->toDateString(), $today])->get();
        $week = collect(range(6, 0))->map(function ($daysAgo) use ($weekTransactions) {
            $date = now()->subDays($daysAgo);
            return [
                'label' => $date->translatedFormat('D'),
                'date' => $date->toDateString(),
                'amount' => $weekTransactions->filter(fn ($item) => $item->occurred_on->toDateString() === $date->toDateString())->sum('amount'),
            ];
        });
        $recent = $user->transactions()->orderByDesc('occurred_on')->orderByDesc('id')->limit(6)->get();
        return view('dashboard', compact('balance', 'todayExpense', 'monthIncome', 'monthExpense', 'week', 'recent'));
    }
}
