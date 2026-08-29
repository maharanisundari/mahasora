@extends('layouts.admin')
@section('title','Dashboard Admin')
@section('admin-content')
<h1 class="text-xl font-bold mb-6">Ringkasan Informasi</h1>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-amber-500">
        <p class="text-sm text-slate-500">Total Pesanan</p>
        <p class="text-2xl font-bold">{{ $totalOrders }}</p>
        <p class="text-xs text-slate-400 mt-1">Semua transaksi</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-slate-500">Total Pendapatan</p>
        <p class="text-2xl font-bold">Rp {{ number_format($totalRevenue,0,',','.') }}</p>
        <p class="text-xs text-slate-400 mt-1">Dari pesanan selesai</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-amber-500">
        <p class="text-sm text-slate-500">Jumlah Pelanggan</p>
        <p class="text-2xl font-bold">{{ $totalCustomers }}</p>
        <p class="text-xs text-slate-400 mt-1">Role customer</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-amber-500">
        <p class="text-sm text-slate-500">Total Layanan</p>
        <p class="text-2xl font-bold">{{ $totalServices }}</p>
        <p class="text-xs text-slate-400 mt-1">Jasa tersedia</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow p-5 lg:col-span-2">
        <h3 class="font-bold mb-4">Grafik Pesanan 7 Hari Terakhir</h3>
        <div class="space-y-2">
            @php $max = max($data) > 0 ? max($data) : 1; @endphp
            @foreach($labels as $i => $label)
                <div class="flex items-center gap-3 text-sm">
                    <span class="w-16 text-slate-500">{{ $label }}</span>
                    <div class="flex-1 bg-slate-100 rounded-full h-5 overflow-hidden">
                        <div class="bg-amber-600 h-5 flex items-center justify-end pr-2 text-white text-xs font-semibold" style="width: {{ ($data[$i]/$max)*100 }}%">
                            {{ $data[$i] }}
                        </div>
                    </div>
                    <span class="w-8 text-right text-xs text-slate-500">{{ $data[$i] }} pesanan</span>
                </div>
            @endforeach
        </div>
        <p class="text-xs text-slate-400 mt-3">Visualisasi murni Tailwind CSS (tanpa Chart.js) - data dari query database</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <h3 class="font-bold mb-4">Rekap Status</h3>
        <div class="space-y-3">
            @php
                $statusConfig = [
                    'pending' => ['label'=>'Pending','color'=>'bg-yellow-400','bg'=>'bg-yellow-100','text'=>'text-yellow-700'],
                    'diproses' => ['label'=>'Diproses','color'=>'bg-blue-400','bg'=>'bg-blue-100','text'=>'text-blue-700'],
                    'selesai' => ['label'=>'Selesai','color'=>'bg-emerald-400','bg'=>'bg-emerald-100','text'=>'text-emerald-700'],
                    'dibatalkan' => ['label'=>'Dibatalkan','color'=>'bg-red-400','bg'=>'bg-red-100','text'=>'text-red-700'],
                ];
                $totalStatus = array_sum($statusCounts->toArray()) ?: 1;
            @endphp
            @foreach($statusConfig as $k=>$cfg)
                @php $cnt = $statusCounts[$k] ?? 0; $pct = round($cnt / $totalStatus * 100); @endphp
                <div>
                    <div class="flex justify-between items-center text-sm mb-1">
                        <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full {{ $cfg['color'] }}"></span> {{ $cfg['label'] }}</span>
                        <span class="font-bold">{{ $cnt }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="{{ $cfg['color'] }} h-2 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 p-3 bg-slate-50 rounded-lg text-xs text-slate-500">
            Total: {{ array_sum($statusCounts->toArray()) }} pesanan dengan status terkini
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="p-4 border-b flex justify-between items-center">
        <h3 class="font-bold">Pesanan Terbaru</h3>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-amber-600 hover:underline">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr><th class="text-left px-4 py-2">Kode</th><th class="text-left px-4 py-2">Pelanggan</th><th class="text-left px-4 py-2">Layanan</th><th class="text-left px-4 py-2">Status</th><th class="text-left px-4 py-2">Total</th></tr>
            </thead>
            <tbody class="divide-y">
                @forelse($recentOrders as $o)
                    <tr>
                        <td class="px-4 py-2 font-mono text-xs">{{ $o->order_code }}</td>
                        <td class="px-4 py-2">{{ $o->user->name }}</td>
                        <td class="px-4 py-2">{{ $o->service->service_name }}</td>
                        <td class="px-4 py-2">{{ ucfirst($o->latestStatus->status ?? 'pending') }}</td>
                        <td class="px-4 py-2">Rp {{ number_format($o->total_price,0,',','.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-6 text-slate-500">Belum ada pesanan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
