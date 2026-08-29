@extends('layouts.app')
@section('title','Katalog Layanan')
@section('content')
<div class="bg-gradient-to-r from-amber-700 to-orange-800 rounded-2xl p-8 text-white mb-8">
    <h1 class="text-3xl font-bold">MahaSora - Layanan Terbaik</h1>
    <p class="mt-2 text-amber-100 max-w-2xl">Pilih layanan jasa, checkout online, dan pantau progres pesananmu.</p>
    <form method="GET" class="mt-6 flex max-w-xl bg-white/95 backdrop-blur rounded-full p-1.5 shadow-lg gap-2">
        <span class="pl-4 pr-1 flex items-center text-stone-400">🔍</span>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari layanan..." class="flex-1 py-2.5 text-stone-800 focus:outline-none bg-transparent">
        <button class="bg-amber-800 px-7 rounded-full font-semibold hover:bg-amber-900 text-white shrink-0">Cari</button>
    </form>
</div>

<div class="flex justify-between items-center mb-4">
    <h2 class="font-bold text-lg">Daftar Layanan ({{ $services->total() }})</h2>
    <span class="text-sm text-slate-500">Halaman {{ $services->currentPage() }}</span>
</div>

@if($services->isEmpty())
    <div class="bg-white rounded-xl p-12 text-center shadow">
        <p class="text-slate-500">Belum ada layanan. Admin silakan tambah di Dashboard.</p>
        @if(auth()->check() && auth()->user()->role==='admin')
            <a href="{{ route('admin.services.create') }}" class="mt-4 inline-block bg-amber-600 text-white px-6 py-2 rounded-lg">Tambah Layanan</a>
        @endif
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($services as $s)
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col">
                <div class="h-36 bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center overflow-hidden">
                    @if($s->image)
                        <img src="{{ asset('storage/'.$s->image) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-4xl text-slate-400">[Layanan]</span>
                    @endif
                </div>
                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="font-bold line-clamp-1">{{ $s->service_name }}</h3>
                    <p class="text-sm text-slate-500 line-clamp-2 mt-1 flex-1">{{ $s->description ?? 'Tanpa deskripsi' }}</p>
                    <p class="mt-3 font-bold text-amber-800 text-lg">Rp {{ number_format($s->price,0,',','.') }}</p>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('catalog.show',$s) }}" class="flex-1 text-center border rounded-lg py-2 text-sm hover:bg-slate-50">Detail</a>
                        @guest
                            <a href="{{ route('login') }}" class="flex-1 text-center bg-amber-600 text-white rounded-lg py-2 text-sm hover:bg-amber-700">Login untuk Pesan</a>
                        @elseif(auth()->user()->role==='customer')
                            <a href="{{ route('orders.checkout',$s) }}" class="flex-1 text-center bg-amber-600 text-white rounded-lg py-2 text-sm hover:bg-amber-700">Pesan</a>
                        @else
                            <span class="flex-1 text-center bg-slate-100 text-slate-400 rounded-lg py-2 text-sm">Khusus Pembeli</span>
                        @endguest
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $services->links() }}</div>
@endif
@endsection
