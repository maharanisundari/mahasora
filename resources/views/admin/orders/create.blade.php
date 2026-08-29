@extends('layouts.admin')
@section('title','Input Pesanan Offline')
@section('admin-content')
<div class="max-w-2xl bg-white shadow rounded-xl p-6">
    <h1 class="text-lg font-bold mb-2">Input Pesanan Offline / WhatsApp</h1>
    <p class="text-sm text-slate-500 mb-6">Gunakan untuk transaksi yang diterima via WA/offline. Sistem tetap terbitkan kode TRX dan status Pending.</p>
    <form method="POST" action="{{ route('admin.orders.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Pelanggan *</label>
            <select name="user_id" required class="w-full border rounded-lg px-3 py-2 mt-1">
                <option value="">-- Pilih Pelanggan --</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} — {{ $c->email }} ({{ $c->phone }})</option>
                @endforeach
            </select>
            <p class="text-xs text-slate-500 mt-1">Jika belum terdaftar, <a href="{{ route('admin.customers.create') }}" class="text-indigo-600 underline">input profil pelanggan dulu</a>.</p>
        </div>
        <div>
            <label class="text-sm font-medium">Layanan *</label>
            <select name="service_id" required class="w-full border rounded-lg px-3 py-2 mt-1">
                @foreach($services as $s)
                    <option value="{{ $s->id }}">{{ $s->service_name }} — Rp {{ number_format($s->price,0,',','.') }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Tipe Pesanan</label>
            <select name="order_type" class="w-full border rounded-lg px-3 py-2 mt-1">
                <option value="offline">offline (WA / Offline)</option>
                <option value="online">online</option>
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Catatan</label>
            <textarea name="notes" rows="3" placeholder="Catatan pesanan offline..." class="w-full border rounded-lg px-3 py-2 mt-1"></textarea>
        </div>
        <button class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 w-full font-semibold">Simpan Pesanan & Terbitkan Kode TRX</button>
    </form>
</div>
@endsection
