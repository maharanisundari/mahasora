@extends('layouts.admin')
@section('title','Monitoring Pesanan')
@section('admin-content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-bold">Monitoring Pesanan</h1>
    <a href="{{ route('admin.orders.create') }}" class="bg-amber-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-amber-700">Input Pesanan Offline</a>
</div>

<form method="GET" class="bg-white shadow rounded-xl p-4 mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode / customer / layanan..." class="border rounded-lg px-3 py-2" onchange="this.form.submit()">
    <select name="status" class="border rounded-lg px-3 py-2" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        @foreach(['pending','diproses','selesai','dibatalkan'] as $st)
            <option value="{{ $st }}" @selected(request('status')==$st)>{{ ucfirst($st) }}</option>
        @endforeach
    </select>
    <select name="type" class="border rounded-lg px-3 py-2" onchange="this.form.submit()">
        <option value="">Semua Tipe</option>
        <option value="media_sosial" @selected(request('type')=='media_sosial')>Media Sosial</option>
        <option value="offline" @selected(request('type')=='offline')>Offline</option>
    </select>
    <select name="cancellation" class="border rounded-lg px-3 py-2" onchange="this.form.submit()">
        <option value="">Semua</option>
        <option value="requested" @selected(request('cancellation')=='requested')>Permintaan Batal</option>
        <option value="accepted" @selected(request('cancellation')=='accepted')>Disetujui</option>
        <option value="rejected" @selected(request('cancellation')=='rejected')>Ditolak</option>
    </select>
</form>

<div class="bg-white shadow rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-xs">
                <tr><th class="text-left px-4 py-3 font-semibold">Kode</th><th class="text-left px-4 py-3 font-semibold">Pelanggan</th><th class="text-left px-4 py-3 font-semibold">Layanan</th><th class="text-left px-4 py-3 font-semibold">Total</th><th class="text-left px-4 py-3 font-semibold min-w-[115px]">Tipe</th><th class="text-left px-4 py-3 font-semibold min-w-[90px]">Bayar</th><th class="text-left px-4 py-3 font-semibold">Status</th><th class="text-left px-4 py-3 font-semibold">Batal</th><th class="px-4 py-3 font-semibold">Aksi</th></tr>
            </thead>
            <tbody class="divide-y">
                @forelse($orders as $o)
                    <tr class="text-xs">
                        <td class="px-4 py-3 font-mono font-bold">{{ $o->order_code }}</td>
                        <td class="px-4 py-3">{{ $o->user->name }}<br><span class="text-[11px] text-slate-500">{{ $o->user->email }}</span></td>
                        <td class="px-4 py-3">{{ $o->service->service_name }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($o->total_price,0,',','.') }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap leading-none min-w-[95px] {{ $o->order_type==='media_sosial'?'bg-blue-100 text-blue-700':'bg-amber-100 text-amber-700' }}">{{ str_replace('_',' ', $o->order_type) }}</span></td>
                        <td class="px-4 py-3">
                            <span class="text-xs whitespace-nowrap">{{ str_replace('_',' ',ucfirst($o->payment_method ?? '-')) }}</span><br>
                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold whitespace-nowrap leading-none min-w-[72px] @if($o->payment_status==='lunas') bg-emerald-100 text-emerald-700 @elseif($o->payment_status==='dp_50') bg-blue-100 text-blue-700 @else bg-red-100 text-red-700 @endif">{{ $o->payment_status==='belum_bayar' ? 'Belum DP' : ($o->payment_status==='dp_50' ? 'DP 50%' : 'Lunas') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php $st=$o->latestStatus->status ?? 'pending'; @endphp
                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap leading-none @if($st==='pending') bg-yellow-100 text-yellow-700 @elseif($st==='diproses') bg-blue-100 text-blue-700 @elseif($st==='selesai') bg-emerald-100 text-emerald-700 @else bg-red-100 text-red-700 @endif">{{ ucfirst($st) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($o->cancellation_status === 'requested')
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap leading-none bg-orange-100 text-orange-700">Permintaan Batal</span>
                            @elseif($o->cancellation_status === 'accepted')
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap leading-none bg-green-100 text-green-700">Disetujui</span>
                            @elseif($o->cancellation_status === 'rejected')
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap leading-none bg-red-100 text-red-700">Ditolak</span>
                            @else
                                <span class="text-slate-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center"><a href="{{ route('admin.orders.show',$o) }}" class="text-amber-600 hover:underline text-xs border px-3 py-1 rounded">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center py-8 text-slate-500">Tidak ada pesanan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $orders->links() }}</div>
</div>
@endsection
