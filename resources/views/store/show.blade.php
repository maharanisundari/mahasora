@extends('layouts.app')
@section('title', 'Info Toko - ' . $store->store_name)
@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-violet-600 p-8 text-white flex flex-col md:flex-row gap-6 items-center">
            @if($store->logo)
                <img src="{{ asset('storage/'.$store->logo) }}" alt="Logo {{ $store->store_name }}" class="w-24 h-24 rounded-xl bg-white object-cover border-4 border-white/30">
            @else
                <div class="w-24 h-24 rounded-xl bg-white text-indigo-600 flex items-center justify-center text-3xl font-bold">MS</div>
            @endif
            <div>
                <h1 class="text-3xl font-bold">{{ $store->store_name }}</h1>
                <p class="text-indigo-100 mt-1">TeFa RPL SMKN 1 Katapang — Sistem Pemesanan Layanan</p>
                @auth
                    @if(auth()->user()->role==='admin')
                        <a href="{{ route('admin.store.edit') }}" class="inline-block mt-3 bg-white text-indigo-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-100">Edit Info Toko (Admin)</a>
                    @endif
                @endauth
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <h2 class="font-bold text-lg mb-2">Deskripsi Toko</h2>
                <p class="text-slate-600 whitespace-pre-line leading-relaxed">{{ $store->description ?? 'Belum ada deskripsi.' }}</p>

                <h3 class="font-bold mt-6 mb-2">Alamat</h3>
                <p class="text-slate-600">{{ $store->address ?? '-' }}</p>

                <h3 class="font-bold mt-6 mb-2">Jam Buka</h3>
                <p class="text-slate-600">{{ $store->opening_hours ?? '-' }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-5 border h-fit">
                <h3 class="font-bold mb-3">Kontak & Informasi</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-slate-500 text-xs uppercase">Telepon</p>
                        <p class="font-semibold">{{ $store->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs uppercase">WhatsApp</p>
                        <p class="font-semibold">{{ $store->whatsapp ?? '-' }} @if($store->whatsapp)<a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$store->whatsapp) }}" target="_blank" class="text-emerald-600 hover:underline text-xs">Chat WA →</a>@endif</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs uppercase">Email</p>
                        <p class="font-semibold">{{ $store->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs uppercase">Instagram</p>
                        <p class="font-semibold">{{ $store->instagram ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs uppercase">Facebook</p>
                        <p class="font-semibold">{{ $store->facebook ?? '-' }}</p>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mt-4">Info ini hanya bisa diedit oleh admin. Pembeli hanya dapat melihat.</p>
                @auth
                    @if(auth()->user()->role==='customer')
                        <p class="text-xs bg-amber-50 border border-amber-200 text-amber-800 rounded-lg p-2 mt-3">Anda login sebagai pembeli — tombol edit tidak tersedia.</p>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('catalog.index') }}" class="text-sm text-indigo-600 hover:underline">← Kembali ke Katalog MahaSora</a>
    </div>
</div>
@endsection
