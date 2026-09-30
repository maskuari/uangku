<?php

namespace App\Http\Controllers;

use App\Support\PdfReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    private function reportData(Request $request): array
    {
        $month = $request->validate(['month' => ['nullable', 'date_format:Y-m']])['month'] ?? now()->format('Y-m');
        $date = Carbon::createFromFormat('!Y-m', $month);
        $transactions = $request->user()->transactions()
            ->whereBetween('occurred_on', [$date->copy()->startOfMonth()->toDateString(), $date->copy()->endOfMonth()->toDateString()])
            ->orderBy('occurred_on')->orderBy('id')->get();
        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $days = collect(range(1, $date->daysInMonth))->map(function ($day) use ($date, $transactions) {
            $dateString = $date->copy()->day($day)->toDateString();
            $items = $transactions->filter(fn ($item) => $item->occurred_on->toDateString() === $dateString);
            return ['date' => $dateString, 'income' => $items->where('type', 'income')->sum('amount'), 'expense' => $items->where('type', 'expense')->sum('amount')];
        });
        $categories = $transactions->where('type', 'expense')->groupBy('category')->map->sum('amount')->sortDesc();
        return compact('month', 'date', 'transactions', 'income', 'expense', 'days', 'categories');
    }

    public function index(Request $request): View { return view('reports.index', $this->reportData($request)); }

    public function pdf(Request $request): Response
    {
        $data = $this->reportData($request);
        $pdf = (new PdfReport())->render($request->user()->name, $data);
        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="uangku-rekap-'.$data['month'].'.pdf"',
        ]);
    }
}
