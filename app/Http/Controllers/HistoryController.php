<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        // Hanya pesanan selesai
        $query = Order::whereHas('latestStatus', fn($q) => $q->where('status','selesai'))->with(['user','service','latestStatus']);

        // Filter tanggal
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('period')) {
            $p = $request->period;
            if ($p==='today') $query->whereDate('created_at', today());
            elseif ($p==='week') $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            elseif ($p==='month') $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            elseif ($p==='year') $query->whereYear('created_at', now()->year);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($w) use($q){
                $w->where('order_code','like',"%$q%")
                  ->orWhereHas('user', fn($u)=>$u->where('name','like',"%$q%"))
                  ->orWhereHas('service', fn($s)=>$s->where('service_name','like',"%$q%"));
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        // Rekap total per periode (dari query yang sudah difilter tanggal/q)
        $baseForTotal = Order::whereHas('latestStatus', fn($q)=>$q->where('status','selesai'));
        if ($request->filled('from')) $baseForTotal->whereDate('created_at','>=',$request->from);
        if ($request->filled('to')) $baseForTotal->whereDate('created_at','<=',$request->to);
        if ($request->filled('q')) {
            $q=$request->q;
            $baseForTotal->where(function($w) use($q){
                $w->where('order_code','like',"%$q%")
                  ->orWhereHas('user', fn($u)=>$u->where('name','like',"%$q%"))
                  ->orWhereHas('service', fn($s)=>$s->where('service_name','like',"%$q%"));
            });
        }

        $totalAll = (clone $baseForTotal)->sum('total_price');
        $totalToday = Order::whereHas('latestStatus', fn($q)=>$q->where('status','selesai'))->whereDate('created_at', today())->sum('total_price');
        $totalWeek = Order::whereHas('latestStatus', fn($q)=>$q->where('status','selesai'))->whereBetween('created_at',[now()->startOfWeek(), now()->endOfWeek()])->sum('total_price');
        $totalMonth = Order::whereHas('latestStatus', fn($q)=>$q->where('status','selesai'))->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_price');
        $totalYear = Order::whereHas('latestStatus', fn($q)=>$q->where('status','selesai'))->whereYear('created_at', now()->year)->sum('total_price');

        $countToday = Order::whereHas('latestStatus', fn($q)=>$q->where('status','selesai'))->whereDate('created_at', today())->count();
        $countWeek = Order::whereHas('latestStatus', fn($q)=>$q->where('status','selesai'))->whereBetween('created_at',[now()->startOfWeek(), now()->endOfWeek()])->count();
        $countMonth = Order::whereHas('latestStatus', fn($q)=>$q->where('status','selesai'))->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $countYear = Order::whereHas('latestStatus', fn($q)=>$q->where('status','selesai'))->whereYear('created_at', now()->year)->count();
        $countAll = Order::whereHas('latestStatus', fn($q)=>$q->where('status','selesai'))->count();

        return view('admin.history.index', compact('orders','totalAll','totalToday','totalWeek','totalMonth','totalYear','countToday','countWeek','countMonth','countYear','countAll'));
    }
}
