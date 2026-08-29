@extends('layouts.app')
@section('title', $service->service_name)
@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('catalog.index') }}" class="text-sm text-slate-500 hover:text-indigo-600">← Kembali ke Katalog</a>
    <div class="bg-white rounded-xl shadow overflow-hidden mt-4">
        <div class="h-64 bg-slate-100 flex items-center justify-center overflow-hidden">
            @if($service->image)
                <img src="{{ asset('storage/'.$service->image) }}" class="w-full h-full object-cover">
            @else
                <span class="text-6xl text-slate-400">[Gambar]</span>
            @endif
        </div>
        <div class="p-6">
            <h1 class="text-2xl font-bold">{{ $service->service_name }}</h1>
            <p class="text-indigo-600 font-bold text-xl mt-2">Rp {{ number_format($service->price,0,',','.') }}</p>
            <div class="prose prose-sm max-w-none mt-4 text-slate-600 whitespace-pre-line">{{ $service->description }}</div>
            <div class="mt-8 flex gap-3">
                @auth
                    <a href="{{ route('orders.checkout',$service) }}" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700">Pesan Sekarang</a>
                @else
                    <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700">Login untuk Memesan</a>
                @endauth
                <a href="{{ route('catalog.index') }}" class="border px-6 py-3 rounded-lg hover:bg-slate-50">Katalog Lainnya</a>
            </div>
        </div>
    </div>
</div>
@endsection
