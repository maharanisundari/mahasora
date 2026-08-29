@extends('layouts.admin')
@section('title','Detail Pelanggan')
@section('admin-content')
<div class="bg-white shadow rounded-xl p-6 mb-6">
    <div class="flex gap-4">
        @if($customer->avatar)
            <img src="{{ asset('storage/'.$customer->avatar) }}" class="w-24 h-24 rounded-full object-cover">
        @else
            <div class="w-24 h-24 rounded-full bg-amber-100 flex items-center justify-center text-3xl font-bold text-amber-700">{{ substr($customer->name,0,1) }}</div>
        @endif
        <div>
            <h1 class="text-xl font-bold">{{ $customer->name }} <span class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded-full">{{ $customer->role }}</span></h1>
            <p class="text-sm text-slate-500">{{ $customer->email }} • {{ $customer->phone ?? '-' }}</p>
            <p class="text-sm text-slate-600 mt-1">{{ $customer->address ?? '-' }}</p>
            <p class="text-sm mt-1">{{ $customer->bio ?? '-' }}</p>
            <p class="text-xs mt-2"><span class="px-2 py-1 rounded-full {{ $customer->customer_status==='active'?'bg-emerald-100 text-emerald-700':'bg-red-100 text-red-700' }}">{{ $customer->customer_status }}</span> • Bergabung {{ $customer->created_at->format('d M Y') }} • {{ $customer->orders->count() }} transaksi</p>
        </div>
    </div>
</div>
<div class="bg-white shadow rounded-xl overflow-hidden">
    <div class="p-4 border-b font-bold">Riwayat Transaksi Pelanggan</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="text-left px-4 py-2">Kode</th><th class="text-left px-4 py-2">Layanan</th><th class="text-left px-4 py-2">Total</th><th class="text-left px-4 py-2">Status</th><th class="text-left px-4 py-2">Tanggal</th></tr></thead>
            <tbody class="divide-y">
                @forelse($customer->orders as $o)
                    <tr><td class="px-4 py-2 font-mono text-xs">{{ $o->order_code }}</td><td class="px-4 py-2">{{ $o->service->service_name }}</td><td class="px-4 py-2">Rp {{ number_format($o->total_price,0,',','.') }}</td><td class="px-4 py-2">{{ ucfirst($o->latestStatus->status ?? 'pending') }}</td><td class="px-4 py-2 text-xs">{{ $o->created_at->format('d/m/Y') }}</td></tr>
                @empty
                    <tr><td colspan="5" class="text-center py-6 text-slate-500">Belum ada transaksi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
