@extends('layouts.app')
@section('title','Detail Pesanan '.$order->order_code)
@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('orders.my') }}" class="text-sm text-slate-500 hover:text-amber-600">← Kembali</a>
    <div class="bg-white shadow rounded-xl overflow-hidden mt-4">
        <div class="p-6 border-b">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-xl font-bold font-mono">{{ $order->order_code }}</h1>
                    <p class="text-sm text-slate-500">{{ $order->created_at->format('d M Y H:i') }} • {{ $order->order_type }} • {{ str_replace('_',' ',ucfirst($order->payment_method ?? '-')) }}</p>
                    <p class="text-xs mt-1">
                        <span class="px-2 py-0.5 rounded-full font-bold @if($order->payment_status==='lunas') bg-emerald-100 text-emerald-700 @elseif($order->payment_status==='dp_50') bg-blue-100 text-blue-700 @else bg-red-100 text-red-700 @endif">
                            {{ $order->payment_status==='belum_bayar' ? 'Belum DP - tidak diproses' : ($order->payment_status==='dp_50' ? 'DP 50% - boleh diproses' : 'Lunas') }}
                        </span>
                        <span class="text-slate-400">DP: Rp {{ number_format($order->total_price*0.5,0,',','.') }}</span>
                    </p>
                </div>
                @php $st=$order->latestStatus->status ?? 'pending'; @endphp
                <span class="px-3 py-1 rounded-full text-sm font-bold h-fit
                    @if($st==='pending') bg-yellow-100 text-yellow-700
                    @elseif($st==='diproses') bg-blue-100 text-blue-700
                    @elseif($st==='selesai') bg-emerald-100 text-emerald-700
                    @else bg-red-100 text-red-700 @endif">{{ ucfirst($st) }}</span>
            </div>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-bold text-sm text-slate-500 uppercase">Layanan</h3>
                <p class="font-semibold">{{ $order->service->service_name }}</p>
                <p class="text-sm text-slate-600">{{ $order->service->description }}</p>
                <p class="font-bold text-amber-600 mt-2">Rp {{ number_format($order->total_price,0,',','.') }}</p>
            </div>
            <div>
                <h3 class="font-bold text-sm text-slate-500 uppercase">Pelanggan</h3>
                <p class="font-semibold">{{ $order->user->name }}</p>
                <p class="text-sm">{{ $order->user->email }} • {{ $order->user->phone }}</p>
                <p class="text-sm text-slate-500">{{ $order->user->address }}</p>
            </div>
        </div>
        @if($order->notes)
            <div class="px-6 pb-4"><p class="text-sm bg-slate-50 border rounded-lg p-3"><strong>Catatan:</strong> {{ $order->notes }}</p></div>
        @endif

        <div class="px-6 pb-4">
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm">
                <p><strong>Metode:</strong> {{ str_replace('_',' ',ucfirst($order->payment_method ?? '-')) }} — <strong>DP 50%:</strong> Rp {{ number_format($order->total_price*0.5,0,',','.') }} — Total: Rp {{ number_format($order->total_price,0,',','.') }} @if($order->ongkir>0) (Ongkir Rp {{ number_format($order->ongkir,0,',','.') }}) @endif</p>
                <p class="mt-1"><strong>Antar:</strong> {{ $order->delivery_type==='antar' ? 'Antar ke pembeli — '.$order->delivery_address.' (Ongkir Rp '.number_format($order->ongkir,0,',','.').')' : 'Ambil di toko' }}</p>
                @if(isset($storeInfo))
                    <p class="text-xs whitespace-pre-line mt-2">{{ $storeInfo->payment_instructions }}</p>
                    @if($order->payment_status!=='lunas')
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$storeInfo->whatsapp) }}?text={{ urlencode('Halo MahaSora, saya '.$order->user->name.' order '.$order->order_code.' sudah '.($order->payment_status==='dp_50' ? 'DP 50%' : 'bayar, mohon konfirmasi')) }}" target="_blank" class="inline-block mt-2 bg-emerald-600 text-white px-4 py-2 rounded-lg text-xs hover:bg-emerald-700">Konfirmasi WA ke Admin →</a>
                        <span class="text-xs text-stone-500 ml-2">Setelah DP/lunas, admin akan ubah status pembayaran.</span>
                    @endif
                @endif
            </div>
        </div>
        <div class="px-6 pb-6">
            <h3 class="font-bold mb-3">Riwayat Status</h3>
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
</div>
@endsection
