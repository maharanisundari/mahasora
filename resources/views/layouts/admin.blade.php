@extends('layouts.app')
@section('content')
<div class="flex flex-col lg:flex-row gap-6">
    <aside class="w-full lg:w-64 flex-shrink-0">
        <div class="bg-white rounded-xl shadow p-4 sticky top-20">
            <h3 class="font-bold text-stone-700 mb-4">Admin Panel — MahaSora</h3>
            <nav class="space-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-amber-600 text-white' : 'hover:bg-slate-100' }}">Dashboard</a>
                <a href="{{ route('admin.services.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.services.*') ? 'bg-amber-600 text-white' : 'hover:bg-slate-100' }}">Manajemen Layanan</a>
                <a href="{{ route('admin.customers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.customers.*') ? 'bg-amber-600 text-white' : 'hover:bg-slate-100' }}">Manajemen Pelanggan</a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.orders.index') || request()->routeIs('admin.orders.show') || request()->routeIs('admin.orders.updateStatus') || request()->routeIs('admin.orders.updatePayment') ? 'bg-amber-600 text-white' : 'hover:bg-slate-100' }}">
    Monitoring Pesanan
    @php 
        $pendingOrdersCount = \App\Models\Order::whereHas('latestStatus', fn($q) => $q->where('status', 'pending'))->count();
        $cancellationCount = \App\Models\Order::where('cancellation_status', 'requested')->count();
    @endphp
    @if($pendingOrdersCount > 0 || $cancellationCount > 0)
        <span class="ml-2 px-2 py-0.5 text-xs bg-red-500 text-white rounded-full ring-2 ring-white">{{ $pendingOrdersCount + $cancellationCount }}</span>
    @endif
</a>
                <a href="{{ route('admin.orders.create') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.orders.create') ? 'bg-amber-600 text-white' : 'hover:bg-slate-100' }}">+ Input Pesanan Offline</a>
                <a href="{{ route('admin.store.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.store.*') ? 'bg-amber-600 text-white' : 'hover:bg-slate-100' }}">Info Toko</a>
                <a href="{{ route('admin.history.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.history.*') ? 'bg-amber-600 text-white' : 'hover:bg-slate-100' }}">Riwayat Selesai</a>
                <div class="border-t my-2"></div>
                <a href="{{ route('catalog.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-100">Lihat Katalog</a>
            </nav>
        </div>
    </aside>
    <div class="flex-1 min-w-0">
        @yield('admin-content')
    </div>
</div>
@endsection
