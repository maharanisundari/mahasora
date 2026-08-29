@extends('layouts.app')
@section('title','Dashboard')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-stone-800">Dashboard</h1>
    <p class="text-sm text-stone-500">Ringkasan pesanan Anda di MahaSora</p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-amber-800">
        <p class="text-sm text-stone-500">Total Pesanan</p>
        <p class="text-2xl font-bold">{{ $totalPesanan }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-400">
        <p class="text-sm text-stone-500">Pending</p>
        <p class="text-2xl font-bold">{{ $pending }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-400">
        <p class="text-sm text-stone-500">Diproses</p>
        <p class="text-2xl font-bold">{{ $diproses }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-stone-500">Selesai</p>
        <p class="text-2xl font-bold">{{ $selesai }}</p>
        <p class="text-xs text-stone-400 mt-1">Belanja Rp {{ number_format($totalBelanja,0,',','.') }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="p-4 border-b flex justify-between items-center">
        <h3 class="font-bold">Pesanan Terbaru</h3>
        <a href="{{ route('orders.my') }}" class="text-sm text-amber-800 hover:underline">Lihat Pesanan Saya →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-amber-50">
                <tr><th class="text-left px-4 py-2">Kode</th><th class="text-left px-4 py-2">Layanan</th><th class="text-left px-4 py-2">Total</th><th class="text-left px-4 py-2">Status</th><th class="text-left px-4 py-2">Pembayaran</th></tr>
            </thead>
            <tbody class="divide-y">
                @forelse($recentOrders as $o)
                    <tr>
                        <td class="px-4 py-2 font-mono text-xs">{{ $o->order_code }}</td>
                        <td class="px-4 py-2">{{ $o->service->service_name }}</td>
                        <td class="px-4 py-2">Rp {{ number_format($o->total_price,0,',','.') }}</td>
                        <td class="px-4 py-2">{{ ucfirst($o->latestStatus->status ?? 'pending') }}</td>
                        <td class="px-4 py-2 text-xs">{{ $o->payment_status==='dp_50' ? 'DP 50%' : ($o->payment_status==='lunas' ? 'Lunas' : 'Belum DP') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-6 text-stone-500">Belum ada pesanan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <a href="{{ route('catalog.index') }}" class="bg-amber-800 text-white px-6 py-2.5 rounded-lg hover:bg-amber-900">Cari Layanan</a>
    <a href="{{ route('store.show') }}" class="border border-amber-200 px-6 py-2.5 rounded-lg hover:bg-amber-50">Info Toko</a>
</div>
@endsection
