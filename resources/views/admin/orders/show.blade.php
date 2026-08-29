@extends('layouts.admin')
@section('title','Detail Pesanan '.$order->order_code)
@section('admin-content')
<a href="{{ route('admin.orders.index') }}" class="text-sm text-slate-500 hover:text-indigo-600">← Kembali</a>
<div class="bg-white shadow rounded-xl overflow-hidden mt-4">
    <div class="p-6 border-b flex justify-between">
        <div><h1 class="text-xl font-bold font-mono">{{ $order->order_code }}</h1><p class="text-sm text-slate-500">{{ $order->created_at->format('d M Y H:i') }} • {{ $order->order_type }} • Rp {{ number_format($order->total_price,0,',','.') }}</p></div>
        @php $st=$order->latestStatus->status ?? 'pending'; @endphp
        <span class="px-3 py-1 rounded-full text-sm font-bold h-fit
            @if($st==='pending') bg-yellow-100 text-yellow-700
            @elseif($st==='diproses') bg-blue-100 text-blue-700
            @elseif($st==='selesai') bg-emerald-100 text-emerald-700
            @else bg-red-100 text-red-700 @endif">{{ ucfirst($st) }}</span>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div><h3 class="text-sm font-bold text-slate-500 uppercase">Layanan</h3><p class="font-semibold">{{ $order->service->service_name }} — Rp {{ number_format($order->service->price,0,',','.') }}</p><p class="text-sm text-slate-600">{{ $order->service->description }}</p></div>
        <div><h3 class="text-sm font-bold text-slate-500 uppercase">Pelanggan</h3><p class="font-semibold">{{ $order->user->name }}</p><p class="text-sm">{{ $order->user->email }} • {{ $order->user->phone }}</p><p class="text-sm text-slate-500">{{ $order->user->address }}</p></div>
    </div>
    @if($order->notes)<div class="px-6 pb-4"><p class="text-sm bg-slate-50 border rounded-lg p-3"><strong>Catatan:</strong> {{ $order->notes }}</p></div>@endif

    <div class="px-6 pb-6">
        <h3 class="font-bold mb-3">Update Status (Pending → Diproses → Selesai)</h3>
        <form method="POST" action="{{ route('admin.orders.updateStatus',$order) }}" class="flex gap-2 mb-6">
            @csrf @method('PATCH')
            <select name="status" class="border rounded-lg px-3 py-2 flex-1">
                @foreach(['pending','diproses','selesai','dibatalkan'] as $s)
                    <option value="{{ $s }}" @selected($st===$s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="bg-indigo-600 text-white px-6 rounded-lg hover:bg-indigo-700">Perbarui</button>
        </form>

        <h3 class="font-bold mb-3">Riwayat Progres</h3>
        <div class="relative border-l-2 border-slate-200 ml-3 space-y-4">
            @foreach($order->statuses as $s)
                <div class="ml-6 relative">
                    <span class="absolute -left-8 w-4 h-4 rounded-full border-2
                        @if($s->status==='pending') bg-yellow-400 border-yellow-600
                        @elseif($s->status==='diproses') bg-blue-400 border-blue-600
                        @elseif($s->status==='selesai') bg-emerald-400 border-emerald-600
                        @else bg-red-400 border-red-600 @endif"></span>
                    <p class="font-semibold text-sm">{{ ucfirst($s->status) }} <span class="text-xs text-slate-500">oleh {{ $s->updater->name ?? 'System' }}</span></p>
                    <p class="text-xs text-slate-500">{{ $s->created_at->format('d M Y H:i') }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
