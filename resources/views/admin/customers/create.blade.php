@extends('layouts.admin')
@section('title','Tambah Pelanggan')
@section('admin-content')
<div class="max-w-2xl bg-white shadow rounded-xl p-6">
    <h1 class="text-lg font-bold mb-4">Tambah Pelanggan Manual</h1>
    <form method="POST" action="{{ route('admin.customers.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div><label class="text-sm font-medium">Nama *</label><input type="text" name="name" required class="w-full border rounded-lg px-3 py-2 mt-1"></div>
        <div><label class="text-sm font-medium">Email *</label><input type="email" name="email" required class="w-full border rounded-lg px-3 py-2 mt-1"></div>
        <div><label class="text-sm font-medium">Password *</label><input type="password" name="password" required class="w-full border rounded-lg px-3 py-2 mt-1"></div>
        <div class="grid grid-cols-2 gap-3">
            <div><label class="text-sm font-medium">Phone</label><input type="text" name="phone" class="w-full border rounded-lg px-3 py-2 mt-1"></div>
            <div><label class="text-sm font-medium">Status</label><select name="customer_status" class="w-full border rounded-lg px-3 py-2 mt-1"><option value="active">active</option><option value="inactive">inactive</option></select></div>
        </div>
        <div><label class="text-sm font-medium">Alamat</label><textarea name="address" rows="2" class="w-full border rounded-lg px-3 py-2 mt-1"></textarea></div>
        <div><label class="text-sm font-medium">Bio</label><textarea name="bio" rows="2" class="w-full border rounded-lg px-3 py-2 mt-1"></textarea></div>
        <div><label class="text-sm font-medium">Avatar</label><input type="file" name="avatar" class="w-full border rounded-lg px-3 py-2 mt-1"></div>
        <button class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">Simpan</button>
        <a href="{{ route('admin.customers.index') }}" class="border px-6 py-2 rounded-lg">Batal</a>
    </form>
</div>
@endsection
