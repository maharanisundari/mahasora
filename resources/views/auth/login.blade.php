@extends('layouts.app')
@section('title','Login')
@section('content')
<div class="max-w-md mx-auto bg-white shadow rounded-xl p-8 mt-6 border border-amber-100">
    <h1 class="text-2xl font-bold text-center mb-2 text-stone-800">Masuk ke MahaSora</h1>
    <p class="text-center text-sm text-stone-500 mb-6">Masuk dengan email terdaftar Anda</p>
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-amber-200 rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-amber-500 focus:outline-none bg-white">
        </div>
        <div>
            <label class="text-sm font-medium">Password</label>
            <input type="password" name="password" required class="w-full border border-amber-200 rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-amber-500 focus:outline-none bg-white">
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" class="accent-amber-800"> Ingat saya</label>
        <button class="w-full bg-amber-800 text-white py-2.5 rounded-lg font-semibold hover:bg-amber-900">Login</button>
    </form>
    <p class="text-center text-sm mt-6">Belum punya akun? <a href="{{ route('register') }}" class="text-amber-800 font-semibold">Daftar</a></p>
</div>
@endsection
