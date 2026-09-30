<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public const CATEGORIES = ['Makanan & minuman', 'Belanja', 'Transportasi', 'Tagihan', 'Kesehatan', 'Hiburan', 'Gaji', 'Bonus', 'Usaha', 'Transfer', 'Lainnya'];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
            'type' => ['nullable', Rule::in(['income', 'expense'])],
            'search' => ['nullable', 'string', 'max:100'],
        ]);
        $query = $request->user()->transactions();
        if (! empty($filters['month'])) $query->where('occurred_on', 'like', $filters['month'].'%');
        if (! empty($filters['type'])) $query->where('type', $filters['type']);
        if (! empty($filters['search'])) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }
        $transactions = $query->orderByDesc('occurred_on')->orderByDesc('id')->paginate(12)->withQueryString();
        return view('transactions.index', compact('transactions', 'filters'));
    }

    public function create(): View { return view('transactions.form', ['transaction' => new Transaction(), 'categories' => self::CATEGORIES]); }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->transactions()->create($this->validated($request));
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Request $request, Transaction $transaction): View
    {
        $this->authorizeOwner($request, $transaction);
        return view('transactions.form', ['transaction' => $transaction, 'categories' => self::CATEGORIES]);
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorizeOwner($request, $transaction);
        $transaction->update($this->validated($request));
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorizeOwner($request, $transaction);
        $transaction->delete();
        return back()->with('success', 'Transaksi berhasil dihapus.');
    }

    private function authorizeOwner(Request $request, Transaction $transaction): void
    {
        abort_unless($transaction->user_id === $request->user()->id, 404);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'type' => ['required', Rule::in(['income', 'expense'])],
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', Rule::in(self::CATEGORIES)],
            'amount' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'occurred_on' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
