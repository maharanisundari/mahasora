@extends('layouts.admin')
@section('title','Manajemen Layanan')
@section('admin-content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-bold">Manajemen Layanan</h1>
    <a href="{{ route('admin.services.create') }}" class="bg-amber-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-amber-700">+ Tambah Layanan</a>
</div>

<form method="GET" class="bg-white shadow rounded-xl p-4 mb-4 flex gap-3">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari layanan..." class="flex-1 border rounded-lg px-3 py-2">
    <button class="bg-slate-800 text-white px-6 rounded-lg">Cari</button>
</form>

<div class="bg-white shadow rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr><th class="text-left px-4 py-3">ID</th><th class="text-left px-4 py-3">Nama</th><th class="text-left px-4 py-3">Harga</th><th class="text-left px-4 py-3">Deskripsi</th><th class="text-center px-4 py-3">Aksi</th></tr>
            </thead>
            <tbody class="divide-y">
                @forelse($services as $s)
                    <tr>
                        <td class="px-4 py-3">{{ $s->id }}</td>
                        <td class="px-4 py-3 font-semibold flex items-center gap-2">
                            @if($s->image)
                                <img src="{{ asset('storage/'.$s->image) }}" class="w-8 h-8 rounded object-cover">
                            @endif
                            {{ $s->service_name }}
                        </td>
                        <td class="px-4 py-3">Rp {{ number_format($s->price,0,',','.') }}</td>
                        <td class="px-4 py-3 text-slate-500 max-w-xs truncate">{{ $s->description }}</td>
                        <td class="px-4 py-3 text-center flex justify-center gap-2">
                            <a href="{{ route('catalog.show',$s) }}" class="text-slate-500 hover:text-amber-600 text-xs border px-2 py-1 rounded">Lihat</a>
                            <a href="{{ route('admin.services.edit',$s) }}" class="text-blue-600 hover:underline text-xs border px-2 py-1 rounded">Edit</a>
                            <form method="POST" action="{{ route('admin.services.destroy',$s) }}" onsubmit="return confirm('Hapus layanan ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline text-xs border px-2 py-1 rounded">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-8 text-slate-500">Belum ada layanan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $services->links() }}</div>
</div>
@endsection
