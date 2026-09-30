@extends('layouts.app')
@section('title', 'Daftar')
@section('content')
<div class="auth-heading"><span class="eyebrow">MULAI DARI SINI</span><h2>Buat akun Uangku</h2><p>Catatan keuangan pribadimu, selalu siap saat dibutuhkan.</p></div>
<form method="post" action="{{ route('register') }}" class="form-stack">@csrf
    <div class="field"><label for="name">Nama kamu</label><input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Nama lengkap" required autocomplete="name"></div>
    <div class="field"><label for="email">Alamat email</label><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com" required autocomplete="email"></div>
    <div class="field"><label for="password">Kata sandi</label><input id="password" name="password" type="password" placeholder="Minimal 8 karakter" required autocomplete="new-password"></div>
    <div class="field"><label for="password_confirmation">Ulangi kata sandi</label><input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ketik ulang kata sandi" required autocomplete="new-password"></div>
    <button class="btn btn-primary btn-block" type="submit">Buat akun <svg><use href="#i-arrow"/></svg></button>
</form>
<p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
@endsection
