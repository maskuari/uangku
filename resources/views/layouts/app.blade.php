<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f7fb">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Uangku') · Uangku</title>
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/uangku-icon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/uangku-icon-192.png') }}">
    <script>
        (() => {
            const origin = window.location.origin;
            const manifest = {
                id: origin + '/',
                name: 'Uangku',
                short_name: 'Uangku',
                description: 'Pencatatan keuangan pribadi.',
                start_url: origin + '/',
                scope: origin + '/',
                display: 'standalone',
                background_color: '#f6f7fb',
                theme_color: '#5b45ee',
                icons: [
                    { src: origin + '/assets/uangku-icon-192.png', sizes: '192x192', type: 'image/png', purpose: 'any' },
                    { src: origin + '/assets/uangku-icon-512.png', sizes: '512x512', type: 'image/png', purpose: 'any' }
                ]
            };
            const link = document.createElement('link');
            link.rel = 'manifest';
            link.href = URL.createObjectURL(new Blob([JSON.stringify(manifest)], { type: 'application/manifest+json' }));
            document.head.appendChild(link);
        })();
    </script>
    <script>document.documentElement.classList.add('js');document.documentElement.dataset.theme = localStorage.getItem('uangku-theme') || 'light';</script>
    <link rel="stylesheet" href="{{ asset('assets/app.css') }}?v=9">
    <script src="{{ asset('assets/app.js') }}?v=4" defer></script>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" class="svg-sprite" aria-hidden="true">
    <symbol id="i-grid" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></symbol>
    <symbol id="i-arrows" viewBox="0 0 24 24"><path d="M7 17V4m0 0-4 4m4-4 4 4M17 7v13m0 0-4-4m4 4 4-4"/></symbol>
    <symbol id="i-chart" viewBox="0 0 24 24"><path d="M3 3v18h18M7 16l4-5 3 2 5-7"/></symbol>
    <symbol id="i-settings" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.8 1.8 0 0 0 .36 2l-1.7 1.7a1.8 1.8 0 0 0-2-.36l-.5.2a1.8 1.8 0 0 0-1.1 1.7V21h-2.4v-.76a1.8 1.8 0 0 0-1.1-1.7l-.5-.2a1.8 1.8 0 0 0-2 .36l-1.7-1.7a1.8 1.8 0 0 0 .36-2l-.2-.5a1.8 1.8 0 0 0-1.7-1.1H4v-2.4h.76a1.8 1.8 0 0 0 1.7-1.1l.2-.5a1.8 1.8 0 0 0-.36-2L8 5.7a1.8 1.8 0 0 0 2 .36l.5-.2a1.8 1.8 0 0 0 1.1-1.7V3H14v.76a1.8 1.8 0 0 0 1.1 1.7l.5.2a1.8 1.8 0 0 0 2-.36l1.7 1.7a1.8 1.8 0 0 0-.36 2l.2.5a1.8 1.8 0 0 0 1.7 1.1H21V13h-.76a1.8 1.8 0 0 0-1.7 1.1z"/></symbol>
    <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
    <symbol id="i-moon" viewBox="0 0 24 24"><path d="M20 15.1A8.2 8.2 0 0 1 8.9 4 8.3 8.3 0 1 0 20 15.1z"/></symbol>
    <symbol id="i-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></symbol>
    <symbol id="i-download" viewBox="0 0 24 24"><path d="M12 3v12m0 0-4-4m4 4 4-4M4 17v3h16v-3"/></symbol>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14m0 0-5-5m5 5-5 5"/></symbol>
    <symbol id="i-wallet" viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="14" rx="3"/><path d="M3 9V6a3 3 0 0 1 3-3h12M16 13h5"/><circle cx="16" cy="13" r=".5"/></symbol>
    <symbol id="i-logout" viewBox="0 0 24 24"><path d="M10 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h5m4-4 4-4-4-4m4 4H9"/></symbol>
    <symbol id="i-edit" viewBox="0 0 24 24"><path d="m4 16-.7 4.7L8 20l11-11-4-4L4 16zM13 7l4 4"/></symbol>
    <symbol id="i-trash" viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3m3 0-1 13H7L6 7m4 4v5m4-5v5"/></symbol>
    <symbol id="i-spark" viewBox="0 0 24 24"><path d="m12 2 1.8 6.2L20 10l-6.2 1.8L12 18l-1.8-6.2L4 10l6.2-1.8L12 2zm7 14 .8 2.2L22 19l-2.2.8L19 22l-.8-2.2L16 19l2.2-.8L19 16z"/></symbol>
</svg>
@auth
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="brand"><img src="{{ asset('assets/logo.png') }}" alt="Logo Uangku"><span>uangku<span class="brand-dot">.</span><small>personal finance</small></span></a>
        <div class="sidebar-label">MENU UTAMA</div>
        <nav class="side-nav" aria-label="Navigasi utama">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><svg><use href="#i-grid"/></svg>Dashboard</a>
            <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') ? 'active' : '' }}"><svg><use href="#i-arrows"/></svg>Transaksi</a>
            <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}"><svg><use href="#i-chart"/></svg>Rekap</a>
            <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}"><svg><use href="#i-settings"/></svg>Pengaturan</a>
        </nav>
        <div class="sidebar-bottom">
            <div class="sidebar-tip"><span class="tip-icon"><svg><use href="#i-spark"/></svg></span><strong>Uang lebih tertata.</strong><p>Catat sedikit demi sedikit, lihat gambaran besarnya.</p></div>
            <form method="post" action="{{ route('logout') }}">@csrf<button type="submit" class="logout"><svg><use href="#i-logout"/></svg>Keluar akun</button></form>
        </div>
    </aside>
    <div class="main-wrap">
        <header class="topbar">
            <button type="button" class="icon-button mobile-menu" id="menu-toggle" aria-label="Buka menu"><span>☰</span></button>
            <div class="topbar-date">{{ now()->translatedFormat('l, d F Y') }}</div>
            <div class="topbar-actions">
                <button type="button" class="icon-button theme-toggle" data-theme-toggle aria-label="Ganti tema"><svg class="moon"><use href="#i-moon"/></svg><svg class="sun"><use href="#i-sun"/></svg></button>
                <div class="avatar" title="{{ auth()->user()->name }}">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</div>
            </div>
        </header>
        <main class="content">
            @if(session('success')) <div class="alert success" role="status">{{ session('success') }}</div> @endif
            @if($errors->any()) <div class="alert error" role="alert">{{ $errors->first() }}</div> @endif
            @yield('content')
        </main>
    </div>
</div>
<nav class="mobile-nav" aria-label="Navigasi seluler">
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><svg><use href="#i-grid"/></svg><span>Beranda</span></a>
    <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') ? 'active' : '' }}"><svg><use href="#i-arrows"/></svg><span>Transaksi</span></a>
    <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}"><svg><use href="#i-chart"/></svg><span>Rekap</span></a>
    <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}"><svg><use href="#i-settings"/></svg><span>Setelan</span></a>
</nav>
@else
<main class="auth-shell">
    <div class="auth-art"><div class="auth-glow"></div><div class="auth-art-content"><a href="{{ route('login') }}" class="brand auth-brand"><img src="{{ asset('assets/logo.png') }}" alt="Logo Uangku"><span>uangku<span class="brand-dot">.</span></span></a><div class="auth-visual"><div class="visual-orbit orbit-one"></div><div class="visual-orbit orbit-two"></div><div class="visual-card"><div class="visual-card-top"><span>Saldo yang tertata</span><span>✦</span></div><strong>Rp 12.850.000</strong><div class="visual-bars"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div></div><span class="float-pill float-in">↗ Pemasukan tercatat</span><span class="float-pill float-out">↘ Pengeluaran terpantau</span></div><h1>Lebih tenang<br>saat mengatur uang.</h1><p>Satu tempat untuk mencatat, memahami, dan merencanakan keuanganmu.</p></div></div>
    <div class="auth-main"><div class="auth-top"><button type="button" class="icon-button theme-toggle" data-theme-toggle aria-label="Ganti tema"><svg class="moon"><use href="#i-moon"/></svg><svg class="sun"><use href="#i-sun"/></svg></button></div><div class="auth-panel">@if($errors->any()) <div class="alert error" role="alert">{{ $errors->first() }}</div> @endif @yield('content')</div><div class="auth-footer">© {{ now()->year }} Uangku. Dibuat untuk hari yang lebih tertata.</div></div>
</main>
@endauth
</body>
</html>
