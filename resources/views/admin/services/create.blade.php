@extends('layouts.admin')
@section('title','Tambah Layanan')
@section('admin-content')
<div class="max-w-2xl bg-white shadow rounded-xl p-6">
    <h1 class="text-lg font-bold mb-4">Tambah Layanan Baru</h1>
    <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Nama Layanan *</label>
            <input type="text" name="service_name" value="{{ old('service_name') }}" required class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Harga (Rp) *</label>
            <input type="number" step="0.01" name="price" value="{{ old('price') }}" required class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Deskripsi</label>
            <textarea name="description" rows="4" class="w-full border rounded-lg px-3 py-2 mt-1">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="text-sm font-medium">Gambar (opsional)</label>
            <input type="file" name="image" class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div class="flex gap-2">
            <button class="bg-amber-600 text-white px-6 py-2 rounded-lg hover:bg-amber-700">Simpan</button>
            <a href="{{ route('admin.services.index') }}" class="border px-6 py-2 rounded-lg">Batal</a>
        </div>
    </form>
</div>
@endsection
