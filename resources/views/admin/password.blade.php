@extends('layouts.admin')
@section('title', 'Reset Password')
@section('content')
<div class="admin-heading"><div><span class="eyebrow">KEAMANAN AKUN</span><h1>Reset password pengguna</h1><p>Buat password baru tanpa memerlukan password lama pengguna.</p></div><a href="{{ route('admin.index') }}" class="btn btn-outline">Kembali</a></div>
<section class="panel admin-password-card"><div class="admin-user-summary"><strong>{{ $user->name }}</strong><span>{{ $user->email }}</span></div><form method="post" action="{{ route('admin.password.update', $user) }}" class="form-stack">@csrf @method('PUT')<div class="field"><label for="password">Password baru</label><input id="password" name="password" type="password" minlength="8" required autocomplete="new-password"></div><div class="field"><label for="password_confirmation">Ulangi password baru</label><input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required autocomplete="new-password"></div><button class="btn btn-primary" type="submit">Simpan password baru</button></form></section>
@endsection
