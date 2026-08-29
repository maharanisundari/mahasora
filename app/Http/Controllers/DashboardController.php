<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::whereHas('latestStatus', fn($q) => $q->where('status', 'selesai'))->sum('total_price');
        // alternative: sum all selesai orders
        $totalRevenue = Order::whereIn('id', function ($query) {
            $query->select('order_id')->from('order_statuses')->where('status', 'selesai');
        })->sum('total_price');

        $totalCustomers = User::where('role', 'customer')->count();
        $totalServices = Service::count();

        $statusCounts = OrderStatus::select('status', DB::raw('count(*) as total'))
            ->whereIn('id', function ($q) {
                $q->select(DB::raw('MAX(id)'))->from('order_statuses')->groupBy('order_id');
            })
            ->groupBy('status')->pluck('total', 'status');

        // ensure keys exist
        foreach (['pending', 'diproses', 'selesai', 'dibatalkan'] as $s) {
            if (!isset($statusCounts[$s])) $statusCounts[$s] = 0;
        }

        $recentOrders = Order::with(['user', 'service', 'latestStatus'])->latest()->take(5)->get();

        // chart data: orders per last 7 days
        $ordersPerDay = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')->orderBy('date')->get()->pluck('total', 'date');

        $labels = [];
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d M');
            $data[] = $ordersPerDay[$d] ?? 0;
        }

        return view('admin.dashboard', compact('totalOrders', 'totalRevenue', 'totalCustomers', 'totalServices', 'statusCounts', 'recentOrders', 'labels', 'data'));
    }
}
