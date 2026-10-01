<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f7fb">
    <title>@yield('title', 'Admin') · Uangku</title>
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
    <link rel="stylesheet" href="{{ asset('assets/app.css') }}?v=8">
    <script src="{{ asset('assets/app.js') }}?v=3" defer></script>
</head>
<body>
<header class="admin-header"><a href="{{ route('admin.index') }}" class="brand"><img src="{{ asset('assets/logo.png') }}" alt="Logo Uangku"><span>uangku<span class="brand-dot">.</span><small>admin panel</small></span></a><div class="admin-header-actions"><span class="admin-email">{{ auth()->user()->email }}</span><button type="button" class="icon-button theme-toggle" data-theme-toggle aria-label="Ganti tema"><svg class="moon" viewBox="0 0 24 24"><path d="M20 15.1A8.2 8.2 0 0 1 8.9 4 8.3 8.3 0 1 0 20 15.1z"/></svg><svg class="sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg></button><form method="post" action="{{ route('logout') }}">@csrf<button type="submit" class="btn btn-outline btn-small">Keluar</button></form></div></header>
<main class="admin-main">
    @if(session('success')) <div class="alert success" role="status">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert error" role="alert">{{ $errors->first() }}</div> @endif
    @yield('content')
</main>
</body>
</html>
