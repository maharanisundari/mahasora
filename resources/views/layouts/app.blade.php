<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','MahaSora') - Sistem Pemesanan Layanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Instrument Sans', sans-serif; }
    </style>
    @vite(['resources/js/app.js'])
</head>
<body class="bg-[#FFFBF0] text-stone-800 min-h-screen flex flex-col">
    <nav class="bg-[#FFFEFB] shadow-sm border-b border-amber-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-8">
                    <a href="{{ route('catalog.index') }}" class="flex items-center gap-2 font-bold text-xl text-amber-900 shrink-0">
                        @if(isset($storeInfo) && $storeInfo->logo)
                            <img src="{{ asset('storage/'.$storeInfo->logo) }}" alt="Logo" class="w-9 h-9 rounded-lg object-cover border border-amber-200 shrink-0" style="width:36px;height:36px;object-fit:cover;">
                        @else
                            <span class="w-9 h-9 rounded-lg bg-amber-800 text-white flex items-center justify-center font-bold text-sm shrink-0" style="width:36px;height:36px;">MS</span>
                        @endif
                        <span class="truncate max-w-[120px]">{{ $storeInfo->store_name ?? 'MahaSora' }}</span>
                    </a>
                    <div class="hidden md:flex items-center gap-4 text-sm">
                        <a href="{{ route('catalog.index') }}" class="hover:text-amber-800 {{ request()->routeIs('catalog.*') ? 'text-amber-900 font-semibold' : 'text-stone-600' }}">Katalog</a>
                        <a href="{{ route('store.show') }}" class="hover:text-amber-800 {{ request()->routeIs('store.show') ? 'text-amber-900 font-semibold' : 'text-stone-600' }}">Info Toko</a>
                        @auth
                            @if(auth()->user()->role==='customer')
                                <a href="{{ route('dashboard') }}" class="hover:text-amber-800 {{ request()->routeIs('dashboard') ? 'text-amber-900 font-semibold' : 'text-stone-600' }}">Dashboard</a>
                                <a href="{{ route('orders.my') }}" class="hover:text-amber-800 {{ request()->routeIs('orders.my*') ? 'text-amber-900 font-semibold' : 'text-stone-600' }}">Pesanan Saya</a>
                            @endif
                            @if(auth()->user()->role==='admin')
                                <a href="{{ route('admin.dashboard') }}" class="hover:text-amber-800 {{ request()->routeIs('admin.dashboard') ? 'text-amber-900 font-semibold' : 'text-stone-600' }}">Dashboard</a>
                            @endif
                        @endauth
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="text-sm px-4 py-2 border border-amber-200 rounded-lg hover:bg-amber-50 text-stone-700">Login</a>
                        <a href="{{ route('register') }}" class="text-sm px-4 py-2 bg-amber-800 text-white rounded-lg hover:bg-amber-900">Daftar</a>
                    @else
                        <div class="relative group">
                            <button class="flex items-center gap-2 text-sm relative">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/'.auth()->user()->avatar) }}" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <span class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-900 font-bold">{{ substr(auth()->user()->name,0,1) }}</span>
                                @endif
                                <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                                <span class="text-xs">▼</span>
                                @php
                                    if (auth()->user()->role === 'admin') {
                                        $notifCount = \App\Models\Order::whereHas('latestStatus', fn($q) => $q->where('status', 'pending'))->count();
                                    } else {
                                        $notifCount = auth()->user()->unreadNotifications()->where('type', 'order_status_update')->count();
                                    }
                                @endphp
                                @if($notifCount > 0)
                                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center ring-2 ring-white">{{ $notifCount }}</span>
                                @endif
                            </button>
                            <div class="absolute right-0 mt-2 w-72 bg-white shadow-lg rounded-lg border border-amber-100 hidden group-hover:block z-50">
                                <div class="p-3 text-xs text-stone-500 border-b flex justify-between items-center">
                                    <span>{{ auth()->user()->email }}<br>
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-{{ auth()->user()->role==='admin'?'amber':'emerald' }}-100 text-{{ auth()->user()->role==='admin'?'amber':'emerald' }}-700 rounded-full">{{ auth()->user()->role }}</span></span>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-amber-50">Profil</a>
                                @if(auth()->user()->role==='admin')
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-amber-50">Dashboard</a>
                                    <a href="{{ route('admin.services.index') }}" class="block px-4 py-2 text-sm hover:bg-amber-50">Layanan</a>
                                    <a href="{{ route('admin.customers.index') }}" class="block px-4 py-2 text-sm hover:bg-amber-50">Pelanggan</a>
                                    <a href="{{ route('admin.orders.index') }}" class="block px-4 py-2 text-sm hover:bg-amber-50 flex justify-between items-center">
                                        Monitoring Pesanan
                                        @php $c = \App\Models\Order::whereHas('latestStatus', fn($q) => $q->where('status', 'pending'))->count(); @endphp
                                        @if($c > 0)<span class="px-2 py-0.5 text-xs bg-red-500 text-white rounded-full ring-2 ring-white">{{ $c }}</span>@endif
                                    </a>
                                @else
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm hover:bg-amber-50 flex justify-between items-center">
                                        Dashboard
                                        @php $c = auth()->user()->unreadNotifications()->where('type', 'order_status_update')->count(); @endphp
                                        @if($c > 0)<span class="px-2 py-0.5 text-xs bg-red-500 text-white rounded-full ring-2 ring-white">{{ $c }}</span>@endif
                                    </a>
                                    <a href="{{ route('orders.my') }}" class="block px-4 py-2 text-sm hover:bg-amber-50">Pesanan Saya</a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}" class="border-t">
                                    @csrf
                                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-amber-50 text-red-600">Logout</button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg flex justify-between items-center">
                <span>✓ {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-700 font-bold">×</button>
            </div>
        </div>
    @endif
    @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @yield('content')
    </main>

    <footer class="bg-[#FFFEFB] border-t border-amber-100 mt-8 py-6 text-sm text-stone-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(isset($storeInfo))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                    <div>
                        <p class="font-bold text-stone-800 flex items-center gap-2">
                            @if($storeInfo->logo)
                                <img src="{{ asset('storage/'.$storeInfo->logo) }}" class="w-6 h-6 rounded object-cover">
                            @endif
                            {{ $storeInfo->store_name }}
                        </p>
                        <p class="mt-1 line-clamp-3">{{ $storeInfo->description }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-stone-700">Kontak & Alamat</p>
                        <p class="mt-1">{{ $storeInfo->address }}</p>
                        <p>Telp/WA: {{ $storeInfo->whatsapp ?? $storeInfo->phone }} | Email: {{ $storeInfo->email }}</p>
                        <p>IG: {{ $storeInfo->instagram ?? '-' }} | TikTok: {{ $storeInfo->tiktok ?? '-' }} | FB: {{ $storeInfo->facebook ?? '-' }}</p>
                    </div>
                    <div class="text-right md:text-right">
                        <p class="font-semibold">Jam Buka: {{ $storeInfo->opening_hours }}</p>
                        <a href="{{ route('store.show') }}" class="text-amber-800 hover:underline">Lihat Info Toko Lengkap →</a>
                        <p class="mt-3">&copy; {{ date('Y') }} {{ $storeInfo->store_name }}</p>
                    </div>
                </div>
            @else
                <p class="text-center">&copy; {{ date('Y') }} MahaSora</p>
            @endif
        </div>
    </footer>
</body>
</html>
