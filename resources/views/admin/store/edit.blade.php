@extends('layouts.admin')
@section('title','Edit Info Toko')
@section('admin-content')
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-bold">Edit Info Toko — MahaSora</h1>
            <p class="text-sm text-slate-500">Hanya admin yang bisa mengedit. Pembeli & guest hanya lihat di <a href="{{ route('store.show') }}" class="text-amber-600 underline">/toko</a>.</p>
        </div>
        <a href="{{ route('store.show') }}" class="text-sm border px-4 py-2 rounded-lg hover:bg-slate-50">Lihat sebagai Pembeli</a>
    </div>

    <form method="POST" action="{{ route('admin.store.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="text-sm font-medium">Logo Toko <span class="text-slate-400">(PNG/JPG/SVG/WebP max 2MB)</span></label>
            <div class="flex items-center gap-4 mt-2">
                @if($store->logo)
                    <img src="{{ asset('storage/'.$store->logo) }}" class="w-20 h-20 rounded-xl object-cover border">
                    <span class="text-xs text-slate-500">Logo saat ini — upload baru untuk mengganti</span>
                @else
                    <div class="w-20 h-20 rounded-xl bg-amber-100 flex items-center justify-center font-bold text-amber-700">MS</div>
                    <span class="text-xs text-slate-500">Belum ada logo — silakan upload</span>
                @endif
            </div>
            <input type="file" name="logo" accept="image/*" class="w-full border rounded-lg px-3 py-2 mt-3">
            <p class="text-xs text-slate-400 mt-1">Logo akan tampil di navbar & footer & halaman Info Toko.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium">Nama Toko *</label>
                <input type="text" name="store_name" value="{{ old('store_name', $store->store_name) }}" required class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">Jam Buka</label>
                <input type="text" name="opening_hours" value="{{ old('opening_hours', $store->opening_hours) }}" placeholder="Senin - Sabtu, 08:00 - 17:00" class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
        </div>

        <div>
            <label class="text-sm font-medium">Deskripsi Toko</label>
            <textarea name="description" rows="4" placeholder="Ceritakan tentang MahaSora..." class="w-full border rounded-lg px-3 py-2 mt-1">{{ old('description', $store->description) }}</textarea>
        </div>

        <div>
            <label class="text-sm font-medium">Alamat Lengkap</label>
            <textarea name="address" rows="2" class="w-full border rounded-lg px-3 py-2 mt-1">{{ old('address', $store->address) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium">Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $store->phone) }}" class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">WhatsApp</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $store->whatsapp) }}" placeholder="081234567890" class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
        </div>

        <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email', $store->email) }}" class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium">Instagram</label>
                <input type="text" name="instagram" value="{{ old('instagram', $store->instagram) }}" placeholder="@mahasora.id" class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">Facebook</label>
                <input type="text" name="facebook" value="{{ old('facebook', $store->facebook) }}" placeholder="MahaSora Official" class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
        </div>

        <div class="border-t pt-5">
            <h3 class="font-bold mb-3">Info Pembayaran (ditampilkan saat checkout)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Nama Bank</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $store->bank_name) }}" placeholder="BCA / BRI / BNI" class="w-full border rounded-lg px-3 py-2 mt-1">
                </div>
                <div>
                    <label class="text-sm font-medium">No. Rekening</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $store->bank_account_number) }}" placeholder="1234567890" class="w-full border rounded-lg px-3 py-2 mt-1">
                </div>
            </div>
            <div class="mt-4">
                <label class="text-sm font-medium">Atas Nama Rekening</label>
                <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $store->bank_account_name) }}" placeholder="MahaSora Official" class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
            <div class="mt-4">
                <label class="text-sm font-medium">Instruksi Pembayaran</label>
                <textarea name="payment_instructions" rows="4" placeholder="Tulis norek DANA/OVO/GoPay juga, contoh: DANA 081234567890 a.n MahaSora" class="w-full border rounded-lg px-3 py-2 mt-1">{{ old('payment_instructions', $store->payment_instructions) }}</textarea>
                <p class="text-xs text-stone-500 mt-1">Akan tampil di halaman checkout & detail pesanan.</p>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button class="bg-amber-800 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-amber-900">Simpan Info Toko</button>
            <a href="{{ route('store.show') }}" class="border px-6 py-2.5 rounded-lg hover:bg-amber-50">Batal</a>
        </div>
    </form>
</div>
@endsection
