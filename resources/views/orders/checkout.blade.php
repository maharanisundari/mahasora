@extends('layouts.app')
@section('title','Checkout')
@section('content')
<div class="max-w-2xl mx-auto bg-white shadow rounded-xl p-6">
    <h1 class="text-xl font-bold mb-4">Form Checkout</h1>
    <div class="border rounded-lg p-4 mb-6 bg-slate-50 flex gap-4">
        @if($service->image)
            <img src="{{ asset('storage/'.$service->image) }}" class="w-20 h-20 rounded-lg object-cover">
        @endif
        <div>
            <h3 class="font-bold">{{ $service->service_name }}</h3>
            <p class="text-sm text-slate-500">{{ Str::limit($service->description,80) }}</p>
            <p class="font-bold text-indigo-600 mt-1">Rp {{ number_format($service->price,0,',','.') }}</p>
        </div>
    </div>
    <form method="POST" action="{{ route('orders.store') }}">
        @csrf
        <input type="hidden" name="service_id" value="{{ $service->id }}">
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-3 text-sm mb-4">
            <p><strong>Pemesan:</strong> {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
            <p><strong>Total Biaya Otomatis:</strong> Rp {{ number_format($service->price,0,',','.') }}</p>
            <p><strong>Status Awal:</strong> <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs">Pending</span> - Kode transaksi akan diterbitkan otomatis (TRX-YYYYMMDD-001)</p>
        </div>
        <div>
            <label class="text-sm font-medium">Catatan (opsional)</label>
            <textarea name="notes" rows="3" placeholder="Contoh: minta revisi 2x, deadline cepat..." class="w-full border rounded-lg px-3 py-2 mt-1">{{ old('notes') }}</textarea>
        </div>
        <button class="w-full mt-6 bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700">Konfirmasi Pesanan &amp; Buat Kode Transaksi</button>
    </form>
    <p class="text-xs text-slate-500 mt-3 text-center">Dengan checkout, sistem menghitung total, menerbitkan kode unik, dan menyimpan ke database.</p>
</div>
@endsection
