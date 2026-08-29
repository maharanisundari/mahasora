@extends('layouts.admin')
@section('title','Riwayat Selesai')
@section('admin-content')
<h1 class="text-xl font-bold mb-2">Riwayat Pesanan Selesai</h1>
<p class="text-sm text-stone-500 mb-6">Terpisah dari monitoring — hanya yang sudah <strong>selesai</strong> beserta total harga per hari/minggu/bulan/tahun.</p>

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-amber-800">
        <p class="text-xs text-stone-500">Hari Ini</p>
        <p class="text-lg font-bold">Rp {{ number_format($totalToday,0,',','.') }}</p>
        <p class="text-xs text-stone-400">{{ $countToday }} pesanan</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-400">
        <p class="text-xs text-stone-500">Minggu Ini</p>
        <p class="text-lg font-bold">Rp {{ number_format($totalWeek,0,',','.') }}</p>
        <p class="text-xs text-stone-400">{{ $countWeek }} pesanan</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-emerald-500">
        <p class="text-xs text-stone-500">Bulan Ini</p>
        <p class="text-lg font-bold">Rp {{ number_format($totalMonth,0,',','.') }}</p>
        <p class="text-xs text-stone-400">{{ $countMonth }} pesanan</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-stone-600">
        <p class="text-xs text-stone-500">Tahun Ini</p>
        <p class="text-lg font-bold">Rp {{ number_format($totalYear,0,',','.') }}</p>
        <p class="text-xs text-stone-400">{{ $countYear }} pesanan</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-amber-600">
        <p class="text-xs text-stone-500">Semua (awal)</p>
        <p class="text-lg font-bold">Rp {{ number_format($totalAll,0,',','.') }}</p>
        <p class="text-xs text-stone-400">{{ $countAll }} pesanan</p>
    </div>
</div>

<form method="GET" class="bg-white shadow rounded-xl p-4 mb-4 grid grid-cols-1 md:grid-cols-5 gap-3">
    <select name="period" class="border rounded-lg px-3 py-2" onchange="this.form.submit()">
        <option value="">Semua periode</option>
        <option value="today" @selected(request('period')=='today')>Hari ini</option>
        <option value="week" @selected(request('period')=='week')>Minggu ini</option>
        <option value="month" @selected(request('period')=='month')>Bulan ini</option>
        <option value="year" @selected(request('period')=='year')>Tahun ini</option>
    </select>
    <input type="date" name="from" value="{{ request('from') }}" class="border rounded-lg px-3 py-2" onchange="this.form.submit()">
    <input type="date" name="to" value="{{ request('to') }}" class="border rounded-lg px-3 py-2" onchange="this.form.submit()">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode/pelanggan/layanan..." class="border rounded-lg px-3 py-2" onchange="this.form.submit()">
    <a href="{{ route('admin.history.index') }}" class="border rounded-lg px-3 py-2 text-center hover:bg-stone-50">Reset</a>
</form>

<div class="bg-white shadow rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-amber-50">
                <tr>
                    <th class="text-left px-4 py-3">Kode</th>
                    <th class="text-left px-4 py-3">Pelanggan</th>
                    <th class="text-left px-4 py-3">Layanan</th>
                    <th class="text-left px-4 py-3">Total</th>
                    <th class="text-left px-4 py-3">Bayar</th>
                    <th class="text-left px-4 py-3">Tanggal Selesai</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($orders as $o)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs font-bold">{{ $o->order_code }}</td>
                        <td class="px-4 py-3">{{ $o->user->name }}<br><span class="text-xs text-stone-500">{{ $o->user->email }}</span></td>
                        <td class="px-4 py-3">{{ $o->service->service_name }}</td>
                        <td class="px-4 py-3 font-bold">Rp {{ number_format($o->total_price,0,',','.') }}</td>
                        <td class="px-4 py-3 text-xs">{{ str_replace('_',' ',ucfirst($o->payment_method ?? '-')) }}<br><span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 font-bold">{{ $o->payment_status==='lunas' ? 'Lunas' : 'DP 50%' }}</span></td>
                        <td class="px-4 py-3 text-xs">{{ $o->latestStatus->created_at->format('d/m/Y H:i') ?? $o->updated_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center"><a href="{{ route('admin.orders.show',$o) }}" class="text-amber-800 hover:underline text-xs border px-3 py-1 rounded">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-8 text-stone-500">Belum ada pesanan selesai</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $orders->links() }}</div>
    <div class="p-4 bg-amber-50 border-t text-sm flex justify-between">
        <span>Total terfilter: <strong>{{ $orders->total() }} pesanan</strong></span>
        <span>Total harga terfilter: <strong>Rp {{ number_format($totalAll,0,',','.') }}</strong></span>
    </div>
</div>
@endsection
