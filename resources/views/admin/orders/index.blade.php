@extends('layouts.admin')
@section('title','Monitoring Pesanan')
@section('admin-content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-bold">Monitoring Pesanan</h1>
    <a href="{{ route('admin.orders.create') }}" class="bg-amber-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-amber-700">Input Pesanan Offline</a>
</div>

<form method="GET" class="bg-white shadow rounded-xl p-4 mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode / customer / layanan..." class="border rounded-lg px-3 py-2">
    <select name="status" class="border rounded-lg px-3 py-2">
        <option value="">Semua Status</option>
        @foreach(['pending','diproses','selesai','dibatalkan'] as $st)
            <option value="{{ $st }}" @selected(request('status')==$st)>{{ ucfirst($st) }}</option>
        @endforeach
    </select>
    <select name="type" class="border rounded-lg px-3 py-2">
        <option value="">Semua Tipe</option>
        <option value="online" @selected(request('type')=='online')>online</option>
        <option value="offline" @selected(request('type')=='offline')>offline</option>
    </select>
    <button class="bg-slate-800 text-white rounded-lg">Filter</button>
</form>

<div class="bg-white shadow rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr><th class="text-left px-4 py-3">Kode</th><th class="text-left px-4 py-3">Pelanggan</th><th class="text-left px-4 py-3">Layanan</th><th class="text-left px-4 py-3">Total</th><th class="text-left px-4 py-3">Tipe</th><th class="text-left px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr>
            </thead>
            <tbody class="divide-y">
                @forelse($orders as $o)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs font-bold">{{ $o->order_code }}</td>
                        <td class="px-4 py-3">{{ $o->user->name }}<br><span class="text-xs text-slate-500">{{ $o->user->email }}</span></td>
                        <td class="px-4 py-3">{{ $o->service->service_name }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($o->total_price,0,',','.') }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs {{ $o->order_type==='online'?'bg-blue-100 text-blue-700':'bg-amber-100 text-amber-700' }}">{{ $o->order_type }}</span></td>
                        <td class="px-4 py-3">
                            @php $st=$o->latestStatus->status ?? 'pending'; @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($st==='pending') bg-yellow-100 text-yellow-700
                                @elseif($st==='diproses') bg-blue-100 text-blue-700
                                @elseif($st==='selesai') bg-emerald-100 text-emerald-700
                                @else bg-red-100 text-red-700 @endif">{{ ucfirst($st) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center"><a href="{{ route('admin.orders.show',$o) }}" class="text-amber-600 hover:underline text-xs border px-3 py-1 rounded">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-8 text-slate-500">Tidak ada pesanan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $orders->links() }}</div>
</div>
@endsection
