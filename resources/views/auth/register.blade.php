@extends('layouts.app')
@section('title','Daftar')
@section('content')
<div class="max-w-md mx-auto bg-white shadow rounded-xl p-8 mt-6 border border-amber-100">
    <h1 class="text-2xl font-bold text-center mb-2 text-stone-800">Daftar di MahaSora</h1>
    <p class="text-center text-sm text-stone-500 mb-6">Buat akun dengan email aktif Anda</p>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-amber-200 rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-amber-500 focus:outline-none">
            @error('name')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-amber-200 rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-amber-500 focus:outline-none">
            @error('email')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-sm font-medium">WhatsApp / HP <span class="text-red-500">*</span></label>
                <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full border border-amber-200 rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                @error('phone')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-sm font-medium">Alamat</label>
                <input type="text" name="address" value="{{ old('address') }}" class="w-full border border-amber-200 rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                @error('address')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div>
            <label class="text-sm font-medium">Password <span class="text-red-500">*</span></label>
            <input type="password" name="password" required class="w-full border border-amber-200 rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-amber-500 focus:outline-none">
            @error('password')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-stone-500 mt-1">Minimal 8 karakter, harus ada huruf besar, angka, dan simbol (@$!%*#?&)</p>
        </div>
        <div>
            <label class="text-sm font-medium">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" required class="w-full border border-amber-200 rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-amber-500 focus:outline-none">
            @error('password_confirmation')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <button class="w-full bg-amber-800 text-white py-2.5 rounded-lg font-semibold hover:bg-amber-900">Daftar</button>
    </form>
    <p class="text-center text-sm mt-6">Sudah punya akun? <a href="{{ route('login') }}" class="text-amber-800 font-semibold">Login</a></p>
</div>
@endsection