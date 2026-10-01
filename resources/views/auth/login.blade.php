@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="auth-heading"><span class="eyebrow">SELAMAT DATANG KEMBALI</span><h2>Masuk ke Uangku</h2><p>Lanjutkan mencatat dan pantau uangmu dari perangkat mana saja.</p></div>
<form method="post" action="{{ route('login') }}" class="form-stack">@csrf
    <div class="field"><label for="email">Alamat email</label><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com" required autocomplete="email" autofocus></div>
    <div class="field"><label for="password">Kata sandi</label><div class="password-input-wrap"><input id="password" name="password" type="password" placeholder="Masukkan kata sandi" required autocomplete="current-password"><button type="button" class="password-toggle" data-password-toggle aria-controls="password" aria-label="Tampilkan kata sandi" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path class="password-eye-slash" d="M3 21 21 3"/></svg></button></div></div>
    <label class="checkbox-row"><input type="checkbox" name="remember" value="1"><span>Ingat saya di perangkat ini</span></label>
    <button class="btn btn-primary btn-block" type="submit">Masuk ke akun <svg><use href="#i-arrow"/></svg></button>
</form>
<p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar gratis</a></p>
@endsection
