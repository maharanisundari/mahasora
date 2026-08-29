@extends('layouts.admin')
@section('title','Edit Pelanggan')
@section('admin-content')
<div class="max-w-2xl bg-white shadow rounded-xl p-6">
    <h1 class="text-lg font-bold mb-4">Edit Pelanggan #{{ $customer->id }}</h1>
    <form method="POST" action="{{ route('admin.customers.update',$customer) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf @method('PUT')
        <div><label class="text-sm font-medium">Nama *</label><input type="text" name="name" value="{{ $customer->name }}" required class="w-full border rounded-lg px-3 py-2 mt-1"></div>
        <div><label class="text-sm font-medium">Email *</label><input type="email" name="email" value="{{ $customer->email }}" required class="w-full border rounded-lg px-3 py-2 mt-1"></div>
        <div><label class="text-sm font-medium">Password (kosongkan jika tidak ganti)</label><input type="password" name="password" class="w-full border rounded-lg px-3 py-2 mt-1"></div>
        <div class="grid grid-cols-2 gap-3">
            <div><label class="text-sm font-medium">Phone</label><input type="text" name="phone" value="{{ $customer->phone }}" class="w-full border rounded-lg px-3 py-2 mt-1"></div>
            <div><label class="text-sm font-medium">Status</label><select name="customer_status" class="w-full border rounded-lg px-3 py-2 mt-1"><option value="active" @selected($customer->customer_status=='active')>active</option><option value="inactive" @selected($customer->customer_status=='inactive')>inactive</option></select></div>
        </div>
        <div><label class="text-sm font-medium">Alamat</label><textarea name="address" rows="2" class="w-full border rounded-lg px-3 py-2 mt-1">{{ $customer->address }}</textarea></div>
        <div><label class="text-sm font-medium">Bio</label><textarea name="bio" rows="2" class="w-full border rounded-lg px-3 py-2 mt-1">{{ $customer->bio }}</textarea></div>
        <div>
            <label class="text-sm font-medium">Avatar</label>
            @if($customer->avatar)<img src="{{ asset('storage/'.$customer->avatar) }}" class="w-20 h-20 rounded-full object-cover mb-2">@endif
            <input type="file" name="avatar" class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <button class="bg-amber-600 text-white px-6 py-2 rounded-lg hover:bg-amber-700">Update</button>
        <a href="{{ route('admin.customers.index') }}" class="border px-6 py-2 rounded-lg">Batal</a>
    </form>
</div>
@endsection
