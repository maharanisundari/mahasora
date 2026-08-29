@extends('layouts.admin')
@section('title','Manajemen Pelanggan')
@section('admin-content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-bold">Manajemen Pelanggan</h1>
    <a href="{{ route('admin.customers.create') }}" class="bg-amber-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-amber-700">+ Tambah Pelanggan Manual</a>
</div>
<form method="GET" class="bg-white shadow rounded-xl p-4 mb-4 flex gap-3">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / email / phone..." class="flex-1 border rounded-lg px-3 py-2">
    <button class="bg-slate-800 text-white px-6 rounded-lg">Cari</button>
</form>
<div class="bg-white shadow rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr><th class="text-left px-4 py-3">Nama</th><th class="text-left px-4 py-3">Email / Phone</th><th class="text-left px-4 py-3">Alamat</th><th class="text-left px-4 py-3">Pesanan</th><th class="text-left px-4 py-3">Status</th><th class="text-center px-4 py-3">Aksi</th></tr>
            </thead>
            <tbody class="divide-y">
                @forelse($customers as $c)
                    <tr>
                        <td class="px-4 py-3 flex items-center gap-2">
                            @if($c->avatar)
                                <img src="{{ asset('storage/'.$c->avatar) }}" class="w-8 h-8 rounded-full object-cover">
                            @else
                                <span class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center font-bold text-xs">{{ substr($c->name,0,1) }}</span>
                            @endif
                            <div>
                                <p class="font-semibold">{{ $c->name }}</p>
                                <p class="text-xs text-slate-500 line-clamp-1">{{ $c->bio ?? '-' }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3"><p>{{ $c->email }}</p><p class="text-xs text-slate-500">{{ $c->phone ?? '-' }}</p></td>
                        <td class="px-4 py-3 text-xs max-w-[150px] truncate">{{ $c->address ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">{{ $c->orders_count }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs {{ $c->customer_status==='active'?'bg-emerald-100 text-emerald-700':'bg-red-100 text-red-700' }}">{{ $c->customer_status }}</span></td>
                        <td class="px-4 py-3 text-center flex justify-center gap-1">
                            <a href="{{ route('admin.customers.show',$c) }}" class="border px-2 py-1 rounded text-xs">Detail</a>
                            <a href="{{ route('admin.customers.edit',$c) }}" class="border px-2 py-1 rounded text-xs text-blue-600">Edit</a>
                            <form method="POST" action="{{ route('admin.customers.destroy',$c) }}" onsubmit="return confirm('Hapus pelanggan?')">
                                @csrf @method('DELETE')
                                <button class="border px-2 py-1 rounded text-xs text-red-600">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-8 text-slate-500">Tidak ada pelanggan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $customers->links() }}</div>
</div>
@endsection
