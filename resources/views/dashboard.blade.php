@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="page-heading reveal"><div><span class="eyebrow">DASHBOARD</span><h1>Halo, {{ explode(' ', auth()->user()->name)[0] }} <span class="wave">✳</span></h1><p>Ini ringkasan uangmu hari ini. Tetap semangat mengelolanya!</p></div><a href="{{ route('transactions.create') }}" class="btn btn-primary"><svg><use href="#i-plus"/></svg> Tambah transaksi</a></div>
@if($showBalanceSetup)
<div class="balance-setup-modal" data-balance-modal role="dialog" aria-modal="true" aria-labelledby="balance-setup-title">
    <div class="balance-setup-backdrop"></div>
    <div class="balance-setup-card">
        <span class="balance-setup-icon"><svg><use href="#i-wallet"/></svg></span>
        <span class="eyebrow">LANGKAH PERTAMA</span>
        <h2 id="balance-setup-title">Berapa saldo yang kamu punya sekarang?</h2>
        <p>Masukkan jumlah uangmu saat ini. Setelah itu, Uangku akan otomatis menambah atau menguranginya setiap ada transaksi.</p>
        <form method="post" action="{{ route('settings.balance') }}" class="balance-setup-form">
            @csrf @method('PUT')
            <div class="field"><label for="setup_current_balance">Saldo saat ini (Rp)</label><input id="setup_current_balance" name="current_balance" type="number" inputmode="numeric" min="-999999999999" max="999999999999" placeholder="Contoh: 1000000" required autofocus></div>
            <div class="balance-setup-actions"><button type="button" class="btn btn-outline" data-balance-later>Nanti</button><button type="submit" class="btn btn-primary">Simpan saldo</button></div>
        </form>
    </div>
</div>
@endif
@if($needsBalanceSetup)
<div class="onboarding balance-guide reveal" data-balance-guide role="status"><span class="onboarding-icon"><svg><use href="#i-spark"/></svg></span><div><strong>Asisten Uangku</strong><p>Belum siap mengisi saldo? Tidak apa-apa. Kamu bisa mengisinya kapan saja melalui menu Setelan.</p></div><a href="{{ route('settings.index') }}#current_balance" class="text-link">Buka Setelan <svg><use href="#i-arrow"/></svg></a></div>
@endif
<div class="dashboard-grid">
    <section class="balance-card reveal" aria-label="Saldo saat ini"><div class="balance-decoration one"></div><div class="balance-decoration two"></div><div class="balance-top"><span class="balance-badge"><svg><use href="#i-wallet"/></svg> SALDO SAAT INI</span><span class="balance-dots">•••</span></div><div class="balance-main"><small>Total uang yang kamu punya</small><strong>Rp {{ number_format($balance, 0, ',', '.') }}</strong></div><div class="balance-bottom"><span>Terus jaga arus uangmu tetap sehat</span><span class="balance-mini-chart"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></span></div></section>
    <div class="summary-stack">
        <section class="metric-card reveal"><div class="metric-top"><span class="metric-icon income">↗</span><span class="metric-period">Bulan ini</span></div><p>Pemasukan</p><strong>Rp {{ number_format($monthIncome, 0, ',', '.') }}</strong><span class="metric-note">Dana yang masuk bulan ini</span></section>
        <section class="metric-card reveal"><div class="metric-top"><span class="metric-icon expense">↘</span><span class="metric-period">Bulan ini</span></div><p>Pengeluaran</p><strong>Rp {{ number_format($monthExpense, 0, ',', '.') }}</strong><span class="metric-note">Dana yang keluar bulan ini</span></section>
    </div>
</div>
<div class="insight-strip reveal"><div class="insight-icon">✦</div><div><strong>Pengeluaran hari ini</strong><p>Setiap pengeluaran kecil tetap berarti.</p></div><strong class="insight-amount">Rp {{ number_format($todayExpense, 0, ',', '.') }}</strong></div>
<div class="content-grid">
    <section class="panel reveal"><div class="panel-head"><div><span class="eyebrow">AKTIVITAS</span><h2>Pengeluaran 7 hari</h2></div><span class="panel-subtitle">7 hari terakhir</span></div><div class="chart-area"><div class="chart-gridlines"><span></span><span></span><span></span><span></span></div><div class="bar-chart">@php($maxWeek = max(1, $week->max('amount')))
        @foreach($week as $day)<div class="bar-col"><span class="bar-value">{{ $day['amount'] > 0 ? 'Rp '.number_format($day['amount'], 0, ',', '.') : '' }}</span><div class="bar-track"><div class="bar" style="height: {{ $day['amount'] ? max(8, round($day['amount'] / $maxWeek * 100)) : 3 }}%"></div></div><span class="bar-label">{{ $day['label'] }}</span></div>@endforeach
    </div></div></section>
    <section class="panel budget-panel reveal"><div class="panel-head"><div><span class="eyebrow">RENCANA BULANAN</span><h2>Target pengeluaran</h2></div><span class="target-icon">◎</span></div>@if(auth()->user()->monthly_budget)<div class="budget-amount"><strong>Rp {{ number_format($monthExpense, 0, ',', '.') }}</strong><span> / Rp {{ number_format(auth()->user()->monthly_budget, 0, ',', '.') }}</span></div><div class="progress-track"><div style="width: {{ min(100, $monthExpense / auth()->user()->monthly_budget * 100) }}%" class="{{ $monthExpense > auth()->user()->monthly_budget ? 'over' : '' }}"></div></div><p class="budget-description">{{ $monthExpense > auth()->user()->monthly_budget ? 'Pengeluaran melewati target bulan ini.' : 'Masih ada ruang untuk pengeluaran bulan ini.' }}</p>@else<div class="budget-empty"><div class="budget-empty-illustration">◎</div><strong>Belum ada target bulan ini</strong><p>Tentukan batas pengeluaran agar lebih mudah memantau kebiasaan belanja.</p><a href="{{ route('settings.index') }}" class="text-link">Atur target <svg><use href="#i-arrow"/></svg></a></div>@endif</section>
</div>
<section class="panel transactions-panel reveal"><div class="panel-head"><div><span class="eyebrow">TERBARU</span><h2>Transaksi terakhir</h2></div><a href="{{ route('transactions.index') }}" class="text-link">Lihat semua <svg><use href="#i-arrow"/></svg></a></div>@if($recent->isEmpty())<div class="empty-state"><span class="empty-icon"><svg><use href="#i-arrows"/></svg></span><strong>Belum ada transaksi</strong><p>Mulai catat pemasukan atau pengeluaran pertamamu.</p><a href="{{ route('transactions.create') }}" class="btn btn-primary btn-small">Tambah transaksi</a></div>@else<div class="transaction-list">@foreach($recent as $item)<div class="transaction-row"><span class="transaction-icon {{ $item->type }}">{{ $item->type === 'income' ? '↙' : '↗' }}</span><div class="transaction-title"><strong>{{ $item->title }}</strong><span>{{ $item->category }} · {{ $item->occurred_on->translatedFormat('d M Y') }}</span></div><strong class="transaction-amount {{ $item->type }}">{{ $item->type === 'income' ? '+' : '−' }} Rp {{ number_format($item->amount, 0, ',', '.') }}</strong></div>@endforeach</div>@endif</section>
@endsection
