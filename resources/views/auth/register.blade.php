@extends('layouts.app')
@section('title','Daftar')
@section('content')
<div class="max-w-md mx-auto bg-white shadow rounded-xl p-8 mt-6">
    <h1 class="text-2xl font-bold text-center mb-6">Daftar Akun Customer</h1>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-sm font-medium">WhatsApp / HP</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">Alamat</label>
                <input type="text" name="address" value="{{ old('address') }}" class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
        </div>
        <div>
            <label class="text-sm font-medium">Password</label>
            <input type="password" name="password" required class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" required class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <button class="w-full bg-indigo-600 text-white py-2.5 rounded-lg font-semibold hover:bg-indigo-700">Daftar</button>
    </form>
    <p class="text-center text-sm mt-6">Sudah punya akun? <a href="{{ route('login') }}" class="text-indigo-600 font-semibold">Login</a></p>
</div>
@endsection
