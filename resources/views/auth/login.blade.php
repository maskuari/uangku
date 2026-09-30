@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="auth-heading"><span class="eyebrow">SELAMAT DATANG KEMBALI</span><h2>Masuk ke Uangku</h2><p>Lanjutkan mencatat dan pantau uangmu dari perangkat mana saja.</p></div>
<form method="post" action="{{ route('login') }}" class="form-stack">@csrf
    <div class="field"><label for="email">Alamat email</label><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com" required autocomplete="email" autofocus></div>
    <div class="field"><label for="password">Kata sandi</label><input id="password" name="password" type="password" placeholder="Masukkan kata sandi" required autocomplete="current-password"></div>
    <label class="checkbox-row"><input type="checkbox" name="remember" value="1"><span>Ingat saya di perangkat ini</span></label>
    <button class="btn btn-primary btn-block" type="submit">Masuk ke akun <svg><use href="#i-arrow"/></svg></button>
</form>
<p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar gratis</a></p>
@endsection
