@extends('layouts.app')
@section('title', 'Daftar')
@section('content')
<div class="auth-heading"><span class="eyebrow">MULAI DARI SINI</span><h2>Buat akun Uangku</h2><p>Catatan keuangan pribadimu, selalu siap saat dibutuhkan.</p></div>
<form method="post" action="{{ route('register') }}" class="form-stack">@csrf
    <div class="field"><label for="name">Nama kamu</label><input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Nama lengkap" required autocomplete="name"></div>
    <div class="field"><label for="email">Alamat email</label><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com" required autocomplete="email"></div>
    <div class="field"><label for="password">Kata sandi</label><div class="password-input-wrap"><input id="password" name="password" type="password" placeholder="Minimal 8 karakter" required autocomplete="new-password"><button type="button" class="password-toggle" data-password-toggle aria-controls="password" aria-label="Tampilkan kata sandi" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path class="password-eye-slash" d="M3 21 21 3"/></svg></button></div></div>
    <div class="field"><label for="password_confirmation">Ulangi kata sandi</label><div class="password-input-wrap"><input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ketik ulang kata sandi" required autocomplete="new-password"><button type="button" class="password-toggle" data-password-toggle aria-controls="password_confirmation" aria-label="Tampilkan konfirmasi kata sandi" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path class="password-eye-slash" d="M3 21 21 3"/></svg></button></div></div>
    <button class="btn btn-primary btn-block" type="submit">Buat akun <svg><use href="#i-arrow"/></svg></button>
</form>
<p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
@endsection
