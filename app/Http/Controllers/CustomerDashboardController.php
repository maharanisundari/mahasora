<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $totalPesanan = Order::where('user_id', $userId)->count();
        $pending = Order::where('user_id', $userId)->whereHas('latestStatus', fn($q) => $q->where('status','pending'))->count();
        $diproses = Order::where('user_id', $userId)->whereHas('latestStatus', fn($q) => $q->where('status','diproses'))->count();
        $selesai = Order::where('user_id', $userId)->whereHas('latestStatus', fn($q) => $q->where('status','selesai'))->count();
        $totalBelanja = Order::where('user_id', $userId)->whereHas('latestStatus', fn($q) => $q->where('status','selesai'))->sum('total_price');
        $recentOrders = Order::where('user_id', $userId)->with(['service','latestStatus'])->latest()->take(5)->get();
        return view('customer.dashboard', compact('totalPesanan','pending','diproses','selesai','totalBelanja','recentOrders'));
    }
}
