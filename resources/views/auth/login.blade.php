@extends('layouts.app')
@section('title','Login')
@section('content')
<div class="max-w-md mx-auto bg-white shadow rounded-xl p-8 mt-6">
    <h1 class="text-2xl font-bold text-center mb-2">Masuk ke MahaSora</h1>
    <p class="text-center text-sm text-slate-500 mb-6">Gunakan akun Admin atau Customer untuk melanjutkan</p>
    <div class="bg-slate-50 border rounded-lg p-3 text-xs mb-6">
        <p class="font-semibold mb-1">Akun Demo (setelah seeder):</p>
        <p>Admin: admin@nusa.test / password</p>
        <p>Customer: budi@nusa.test / password</p>
    </div>
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        </div>
        <div>
            <label class="text-sm font-medium">Password</label>
            <input type="password" name="password" required class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember"> Ingat saya</label>
        <button class="w-full bg-indigo-600 text-white py-2.5 rounded-lg font-semibold hover:bg-indigo-700">Login</button>
    </form>
    <p class="text-center text-sm mt-6">Belum punya akun? <a href="{{ route('register') }}" class="text-indigo-600 font-semibold">Daftar</a></p>
</div>
@endsection
