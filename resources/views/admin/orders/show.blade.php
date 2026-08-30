@extends('layouts.admin')
@section('title','Detail Pesanan '.$order->order_code)
@section('admin-content')
<a href="{{ route('admin.orders.index') }}" class="text-sm text-slate-500 hover:text-amber-600">← Kembali</a>
<div class="bg-white shadow rounded-xl overflow-hidden mt-4">
    <div class="p-6 border-b flex justify-between">
        <div><h1 class="text-xl font-bold font-mono">{{ $order->order_code }}</h1><p class="text-sm text-slate-500">{{ $order->created_at->format('d M Y H:i') }} • {{ str_replace('_',' ',ucfirst($order->order_type)) }} • Rp {{ number_format($order->total_price,0,',','.') }} • DP Rp {{ number_format($order->total_price*0.5,0,',','.') }}</p><p class="text-xs mt-1">Metode: <strong>{{ str_replace('_',' ',ucfirst($order->payment_method ?? '-')) }}</strong> • <span class="px-2 py-0.5 rounded-full font-bold @if($order->payment_status==='lunas') bg-emerald-100 text-emerald-700 @elseif($order->payment_status==='dp_50') bg-blue-100 text-blue-700 @else bg-red-100 text-red-700 @endif">{{ $order->payment_status==='belum_bayar' ? 'Belum DP' : ($order->payment_status==='dp_50' ? 'DP 50%' : 'Lunas') }}</span></p></div>
        @php $st=$order->latestStatus->status ?? 'pending'; @endphp
        <span class="px-3 py-1 rounded-full text-sm font-bold h-fit
            @if($st==='pending') bg-yellow-100 text-yellow-700
            @elseif($st==='diproses') bg-blue-100 text-blue-700
            @elseif($st==='selesai') bg-emerald-100 text-emerald-700
            @else bg-red-100 text-red-700 @endif">{{ ucfirst($st) }}</span>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div><h3 class="text-sm font-bold text-slate-500 uppercase">Layanan</h3><p class="font-semibold">{{ $order->service->service_name }} — Rp {{ number_format($order->service->price,0,',','.') }}</p><p class="text-sm text-slate-600">{{ $order->service->description }}</p><p class="text-sm mt-2"><strong>Antar:</strong> {{ $order->delivery_type==='antar' ? 'Antar — '.$order->delivery_address.' (Ongkir Rp '.number_format($order->ongkir,0,',','.').')' : 'Ambil di toko' }}</p></div>
        <div><h3 class="text-sm font-bold text-slate-500 uppercase">Pelanggan</h3><p class="font-semibold">{{ $order->user->name }}</p><p class="text-sm">{{ $order->user->email }} • {{ $order->user->phone }}</p><p class="text-sm text-slate-500">{{ $order->user->address }}</p></div>
    </div>
    @if($order->notes)<div class="px-6 pb-4"><p class="text-sm bg-slate-50 border rounded-lg p-3"><strong>Catatan:</strong> {{ $order->notes }}</p></div>@endif

    {{-- Cancellation Request for Admin --}}
    @if($order->cancellation_status === 'requested')
        <div class="px-6 pb-4">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="font-bold text-red-700 mb-2">⚠️ Permintaan Pembatalan dari Pelanggan</h4>
                <p class="text-sm text-red-600 mb-2"><strong>Alasan:</strong> {{ $order->cancellation_reason }}</p>
                <p class="text-xs text-red-500 mb-3">Diajukan: {{ $order->cancellation_requested_at->format('d M Y H:i') }} oleh {{ $order->user->name }}</p>
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('admin.orders.acceptCancellation', $order) }}" onclick="return confirm('Yakin terima pembatalan? Pesanan akan dibatalkan.')">
                        @csrf @method('PATCH')
                        <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm font-medium">Terima & Batalkan Pesanan</button>
                    </form>
                    <form method="POST" action="{{ route('admin.orders.rejectCancellation', $order) }}" onclick="return confirm('Yakin tolak pembatalan? Pesanan akan terus berlangsung.')">
                        @csrf @method('PATCH')
                        <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm font-medium">Tolak Pembatalan</button>
                    </form>
                </div>
            </div>
        </div>
    @elseif($order->cancellation_status === 'accepted')
        <div class="px-6 pb-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="font-bold text-green-700 mb-1">✅ Pembatalan Disetujui</h4>
                <p class="text-sm text-green-600">Pesanan dibatalkan oleh admin.</p>
                <p class="text-xs text-green-500 mt-1">Diproses: {{ $order->cancellation_processed_at->format('d M Y H:i') }} oleh {{ $order->cancellationProcessor->name ?? 'Admin' }}</p>
            </div>
        </div>
    @elseif($order->cancellation_status === 'rejected')
        <div class="px-6 pb-4">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="font-bold text-yellow-700 mb-1">⏭️ Pembatalan Ditolak</h4>
                <p class="text-sm text-yellow-600">Permintaan pembatalan ditolak. Pesanan tetap berlangsung.</p>
                <p class="text-xs text-yellow-500 mt-1">Diproses: {{ $order->cancellation_processed_at->format('d M Y H:i') }} oleh {{ $order->cancellationProcessor->name ?? 'Admin' }}</p>
            </div>
        </div>
    @endif

    <div class="px-6 pb-4">
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm">
            <p class="font-bold text-amber-900">Pembayaran: {{ str_replace('_',' ',ucfirst($order->payment_method ?? '-')) }} — Rp {{ number_format($order->total_price,0,',','.') }} (DP 50% = Rp {{ number_format($order->total_price*0.5,0,',','.') }})</p>
            <p class="text-xs text-red-700 mt-1">Pesanan tidak diproses sedikitpun sebelum DP 50%. Saat ini: <strong>{{ $order->payment_status==='belum_bayar' ? 'Belum DP' : ($order->payment_status==='dp_50' ? 'DP 50% ✓' : 'Lunas ✓') }}</strong></p>
            <form method="POST" action="{{ route('admin.orders.updatePayment',$order) }}" class="flex gap-2 mt-2">
                @csrf @method('PATCH')
                <select name="payment_status" class="border rounded-lg px-3 py-1.5 text-sm flex-1">
                    <option value="belum_bayar" @selected($order->payment_status==='belum_bayar')>Belum Bayar</option>
                    <option value="dp_50" @selected($order->payment_status==='dp_50')>Sudah DP 50%</option>
                    <option value="lunas" @selected($order->payment_status==='lunas')>Lunas</option>
                </select>
                <button class="bg-emerald-600 text-white px-4 rounded-lg text-sm hover:bg-emerald-700">Update Bayar</button>
            </form>
        </div>
    </div>

    <div class="px-6 pb-6">
        <h3 class="font-bold mb-3">Update Status (Pending → Diproses → Selesai)</h3>
        @if($order->payment_status==='belum_bayar')
            <p class="text-xs bg-red-50 border border-red-200 text-red-700 rounded-lg p-2 mb-3">Tidak bisa ke Diproses/Selesai sebelum DP 50% — ubah pembayaran dulu.</p>
        @endif
        <form method="POST" action="{{ route('admin.orders.updateStatus',$order) }}" class="flex gap-2 mb-6">
            @csrf @method('PATCH')
            <select name="status" class="border rounded-lg px-3 py-2 flex-1">
                @foreach(['pending','diproses','selesai','dibatalkan'] as $s)
                    <option value="{{ $s }}" @selected($st===$s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="bg-amber-800 text-white px-6 rounded-lg hover:bg-amber-900">Perbarui</button>
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
