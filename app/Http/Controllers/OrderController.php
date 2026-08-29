<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Customer: form checkout
    public function checkout(Service $service)
    {
        return view('orders.checkout', compact('service'));
    }

    public function store(Request $request, Service $service = null)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'notes' => 'nullable|string',
        ]);

        $service = $service ?? Service::findOrFail($request->service_id);
        // if service_id passed differs, use that
        $service = Service::findOrFail($request->service_id);

        $order = Order::create([
            'order_code' => Order::generateOrderCode(),
            'user_id' => Auth::id(),
            'service_id' => $service->id,
            'total_price' => $service->price,
            'order_type' => 'online',
            'notes' => $request->notes,
        ]);

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'updated_by' => Auth::id(),
            'created_at' => now(),
        ]);

        return redirect()->route('orders.my')->with('success', 'Pesanan berhasil dibuat: ' . $order->order_code);
    }

    // Admin offline order
    public function adminCreate()
    {
        $services = Service::all();
        $customers = User::where('role', 'customer')->get();
        return view('admin.orders.create', compact('services', 'customers'));
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'notes' => 'nullable|string',
            'order_type' => 'required|in:online,offline',
        ]);
        $service = Service::findOrFail($request->service_id);
        $order = Order::create([
            'order_code' => Order::generateOrderCode(),
            'user_id' => $request->user_id,
            'service_id' => $service->id,
            'total_price' => $service->price,
            'order_type' => $request->order_type,
            'notes' => $request->notes,
        ]);
        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'updated_by' => Auth::id(),
            'created_at' => now(),
        ]);
        return redirect()->route('admin.orders.index')->with('success', 'Pesanan offline berhasil dibuat: ' . $order->order_code);
    }

    // Customer My Orders
    public function myOrders(Request $request)
    {
        $query = Order::where('user_id', Auth::id())->with(['service', 'latestStatus']);
        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('latestStatus', fn($q) => $q->where('status', $status));
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($w) => $w->where('order_code', 'like', "%$q%")->orWhereHas('service', fn($s) => $s->where('service_name', 'like', "%$q%")));
        }
        $orders = $query->latest()->paginate(10)->withQueryString();
        return view('orders.my', compact('orders'));
    }

    public function myShow(Order $order)
    {
        if ($order->user_id !== Auth::id() && Auth::user()->role !== 'admin') abort(403);
        $order->load(['service', 'user', 'statuses.updater']);
        return view('orders.show', compact('order'));
    }

    // Admin monitoring
    public function adminIndex(Request $request)
    {
        $query = Order::with(['user', 'service', 'latestStatus']);
        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('latestStatus', fn($q) => $q->where('status', $status));
        }
        if ($request->filled('type')) {
            $query->where('order_type', $request->type);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('order_code', 'like', "%$q%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%$q%"))
                    ->orWhereHas('service', fn($s) => $s->where('service_name', 'like', "%$q%"));
            });
        }
        $orders = $query->latest()->paginate(15)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function adminShow(Order $order)
    {
        $order->load(['service', 'user', 'statuses.updater']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,selesai,dibatalkan',
        ]);
        OrderStatus::create([
            'order_id' => $order->id,
            'status' => $request->status,
            'updated_by' => Auth::id(),
            'created_at' => now(),
        ]);
        return back()->with('success', 'Status diperbarui ke ' . $request->status);
    }
}
