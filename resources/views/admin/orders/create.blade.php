@extends('layouts.admin')
@section('title','Input Pesanan Offline')
@section('admin-content')
<div class="max-w-2xl bg-white shadow rounded-xl p-6">
    <h1 class="text-lg font-bold mb-6">Input Pesanan Offline / WhatsApp</h1>
    <form method="POST" action="{{ route('admin.orders.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Pelanggan *</label>
            <select name="user_id" required class="w-full border rounded-lg px-3 py-2 mt-1">
                <option value="">-- Pilih Pelanggan --</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} — {{ $c->email }} ({{ $c->phone }})</option>
                @endforeach
            </select>
            <p class="text-xs text-slate-500 mt-1">Jika belum terdaftar, <a href="{{ route('admin.customers.create') }}" class="text-amber-600 underline">input profil pelanggan dulu</a>.</p>
        </div>
        <div>
            <label class="text-sm font-medium">Layanan *</label>
            <select name="service_id" required class="w-full border rounded-lg px-3 py-2 mt-1">
                @foreach($services as $s)
                    <option value="{{ $s->id }}">{{ $s->service_name }} — Rp {{ number_format($s->price,0,',','.') }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Tipe Pesanan *</label>
            <select name="order_type" required class="w-full border rounded-lg px-3 py-2 mt-1">
                <option value="media_sosial">Media Sosial</option>
                <option value="offline">Offline</option>
            </select>
        </div>
        <div class="p-3 bg-[#FFFBF0] border border-amber-200 rounded-lg">
            <label class="text-sm font-medium">Pengantaran *</label>
            <div class="flex gap-4 mt-2">
                <label class="flex items-center gap-2 text-sm"><input type="radio" name="delivery_type" value="ambil_di_toko" checked onchange="document.getElementById('alamat-antar-admin').style.display='none'"> Ambil di Toko (Gratis)</label>
                <label class="flex items-center gap-2 text-sm"><input type="radio" name="delivery_type" value="antar" onchange="document.getElementById('alamat-antar-admin').style.display='block'"> Antar ke Pembeli (Ongkir Rp 15.000)</label>
            </div>
            <div id="alamat-antar-admin" style="display:none;" class="mt-3">
                <label class="text-sm font-medium">Alamat Antar *</label>
                <textarea name="delivery_address" rows="2" placeholder="Jl. ... Kec. ... Kota ..." class="w-full border rounded-lg px-3 py-2 mt-1"></textarea>
            </div>
        </div>
        <div>
            <label class="text-sm font-medium">Metode Pembayaran *</label>
            <select name="payment_method" required class="w-full border rounded-lg px-3 py-2 mt-1">
                <option value="cash">Cash (Bayar di toko / COD)</option>
                <option value="transfer_bank">Transfer Bank — {{ $store->bank_name ?? 'BCA' }} {{ $store->bank_account_number ?? '1234567890' }} a.n {{ $store->bank_account_name ?? 'MahaSora' }}</option>
                <option value="dana">DANA — {{ $store->whatsapp ?? '081234567890' }} a.n {{ $store->bank_account_name ?? 'MahaSora' }}</option>
                <option value="ovo">OVO — {{ $store->whatsapp ?? '' }}</option>
                <option value="gopay">GoPay — {{ $store->whatsapp ?? '' }}</option>
                <option value="shopeepay">ShopeePay — {{ $store->whatsapp ?? '' }}</option>
                <option value="lainnya">Lainnya</option>
            </select>
            <p class="text-xs text-stone-500 mt-1 whitespace-pre-line">{{ $store->payment_instructions }}</p>
        </div>
        <div>
            <label class="text-sm font-medium">Status Pembayaran *</label>
            <select name="payment_status" required class="w-full border rounded-lg px-3 py-2 mt-1">
                <option value="belum_bayar">Belum Bayar - tidak diproses</option>
                <option value="dp_50">Sudah DP 50% - boleh diproses</option>
                <option value="lunas">Lunas</option>
            </select>
            <p class="text-xs text-red-600 mt-1">Pesanan tidak akan diproses sedikitpun sebelum DP 50%.</p>
        </div>
        <div>
            <label class="text-sm font-medium">Catatan</label>
            <textarea name="notes" rows="3" placeholder="Catatan pesanan offline..." class="w-full border rounded-lg px-3 py-2 mt-1"></textarea>
        </div>
        <button class="bg-amber-800 text-white px-6 py-2 rounded-lg hover:bg-amber-900 w-full font-semibold">Simpan Pesanan & Terbitkan Kode TRX</button>
    </form>
</div>
@endsection
