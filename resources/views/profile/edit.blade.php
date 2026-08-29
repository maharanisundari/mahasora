@extends('layouts.app')
@section('title','Edit Profil')
@section('content')
<div class="max-w-2xl mx-auto bg-white shadow rounded-xl p-6">
    <h1 class="text-xl font-bold mb-6">Edit Profil</h1>
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf @method('PUT')
        <div class="flex items-center gap-4">
            @if($user->avatar)
                <img src="{{ asset('storage/'.$user->avatar) }}" class="w-20 h-20 rounded-full object-cover">
            @else
                <div class="w-20 h-20 rounded-full bg-amber-100 flex items-center justify-center text-2xl font-bold text-amber-700">{{ substr($user->name,0,1) }}</div>
            @endif
            <div>
                <label class="text-sm font-medium">Foto Profil</label>
                <input type="file" name="avatar" class="block text-sm mt-1">
                <p class="text-xs text-slate-500">JPG/PNG max 2MB</p>
            </div>
        </div>
        <div>
            <label class="text-sm font-medium">Nama</label>
            <input type="text" name="name" value="{{ old('name',$user->name) }}" required class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email',$user->email) }}" required class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium">Phone / WA</label>
                <input type="text" name="phone" value="{{ old('phone',$user->phone) }}" class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">Role</label>
                <input type="text" value="{{ $user->role }}" disabled class="w-full border rounded-lg px-3 py-2 mt-1 bg-slate-100">
            </div>
        </div>
        <div>
            <label class="text-sm font-medium">Alamat</label>
            <textarea name="address" rows="2" class="w-full border rounded-lg px-3 py-2 mt-1">{{ old('address',$user->address) }}</textarea>
        </div>
        <div>
            <label class="text-sm font-medium">Bio</label>
            <textarea name="bio" rows="2" placeholder="Ceritakan sedikit tentang Anda..." class="w-full border rounded-lg px-3 py-2 mt-1">{{ old('bio',$user->bio) }}</textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium">Password Baru (kosongkan jika tidak ganti)</label>
                <input type="password" name="password" class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
        </div>
        <button class="w-full bg-amber-600 text-white py-2.5 rounded-lg font-semibold hover:bg-amber-700">Simpan Profil</button>
    </form>
</div>
@endsection
