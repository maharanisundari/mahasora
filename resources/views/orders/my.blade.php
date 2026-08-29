@extends('layouts.app')
@section('title','Pesanan Saya')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-bold">Pesanan Saya</h1>
    <a href="{{ route('catalog.index') }}" class="text-sm bg-white border px-4 py-2 rounded-lg">Cari Layanan Lagi</a>
</div>

<form method="GET" class="bg-white rounded-xl shadow p-4 mb-6 flex flex-col sm:flex-row gap-3">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode / layanan..." class="flex-1 border rounded-lg px-3 py-2" onchange="this.form.submit()" onkeydown="if(event.key==='Enter') this.form.submit()">
    <select name="status" class="border rounded-lg px-3 py-2" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        @foreach(['pending','diproses','selesai','dibatalkan'] as $st)
            <option value="{{ $st }}" @selected(request('status')==$st)>{{ ucfirst($st) }}</option>
        @endforeach
    </select>
</form>

<div class="bg-white shadow rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-xs">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold">Kode</th>
                    <th class="text-left px-4 py-3 font-semibold">Layanan</th>
                    <th class="text-left px-4 py-3 font-semibold">Total</th>
                    <th class="text-left px-4 py-3 font-semibold min-w-[115px]">Tipe</th>
                    <th class="text-left px-4 py-3 font-semibold min-w-[90px]">Bayar</th>
                    <th class="text-left px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($orders as $o)
                    <tr class="hover:bg-slate-50 text-xs">
                        <td class="px-4 py-3 font-mono font-bold">{{ $o->order_code }}</td>
                        <td class="px-4 py-3">{{ $o->service->service_name }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($o->total_price,0,',','.') }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap leading-none min-w-[95px] {{ $o->order_type==='media_sosial'?'bg-blue-100 text-blue-700':'bg-amber-100 text-amber-700' }}">{{ str_replace('_',' ', $o->order_type) }}</span></td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-medium whitespace-nowrap">{{ str_replace('_',' ',ucfirst($o->payment_method ?? '-')) }}</span><br>
                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold whitespace-nowrap leading-none min-w-[72px] @if($o->payment_status==='lunas') bg-emerald-100 text-emerald-700 @elseif($o->payment_status==='dp_50') bg-blue-100 text-blue-700 @else bg-red-100 text-red-700 @endif">{{ $o->payment_status==='belum_bayar' ? 'Belum DP' : ($o->payment_status==='dp_50' ? 'DP 50%' : 'Lunas') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php $st=$o->latestStatus->status ?? 'pending'; @endphp
                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap leading-none @if($st==='pending') bg-yellow-100 text-yellow-700 @elseif($st==='diproses') bg-blue-100 text-blue-700 @elseif($st==='selesai') bg-emerald-100 text-emerald-700 @else bg-red-100 text-red-700 @endif">{{ ucfirst($st) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center"><a href="{{ route('orders.myShow',$o) }}" class="text-amber-600 hover:underline border border-amber-200 px-3 py-1 rounded-full text-xs">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-12 text-slate-500">Belum ada pesanan. Mulai dari katalog!</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $orders->links() }}</div>
</div>
@endsection
