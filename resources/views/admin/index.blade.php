@extends('layouts.admin')
@section('title', 'Kelola Pengguna')
@section('content')
<div class="admin-heading"><div><span class="eyebrow">ADMIN UANGKU</span><h1>Kelola akun pengguna</h1><p>Kelola akses akun tanpa melihat saldo atau transaksi pribadi pengguna.</p></div><span class="admin-count">{{ $users->total() }} pengguna</span></div>
<section class="panel admin-panel">
    <form method="get" action="{{ route('admin.index') }}" class="admin-search"><div class="search-field"><span>⌕</span><input name="search" value="{{ $search }}" placeholder="Cari nama atau email" aria-label="Cari pengguna"></div><button class="btn btn-primary btn-small" type="submit">Cari</button>@if($search)<a href="{{ route('admin.index') }}" class="btn btn-outline btn-small">Reset</a>@endif</form>
    @if($users->isEmpty())
        <div class="empty-state"><strong>Pengguna tidak ditemukan</strong><p>Belum ada akun yang sesuai dengan pencarian.</p></div>
    @else
        <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Pengguna</th><th>Password</th><th>Dibuat</th><th>Aksi</th></tr></thead><tbody>@foreach($users as $user)<tr><td><strong>{{ $user->name }}</strong><span>{{ $user->email }}</span></td><td><span class="secure-badge">● Terenkripsi</span></td><td>{{ $user->created_at->translatedFormat('d M Y') }}</td><td><div class="admin-actions"><a href="{{ route('admin.password.edit', $user) }}" class="btn btn-outline btn-small">Reset password</a><form method="post" action="{{ route('admin.users.destroy', $user) }}" data-confirm="Hapus akun {{ $user->email }} beserta seluruh datanya? Tindakan ini tidak dapat dibatalkan.">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-small">Hapus</button></form></div></td></tr>@endforeach</tbody></table></div>
        <div class="pagination-wrap">{{ $users->links() }}</div>
    @endif
</section>
@endsection
