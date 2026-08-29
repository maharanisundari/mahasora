<?php

namespace App\Http\Controllers;

use App\Models\Notification;
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
            'payment_method' => 'required|in:cash,transfer_bank,dana,ovo,gopay,shopeepay,lainnya',
            'delivery_type' => 'required|in:ambil_di_toko,antar',
            'delivery_address' => 'nullable|required_if:delivery_type,antar|string',
            'notes' => 'nullable|string',
        ]);

        $service = $service ?? Service::findOrFail($request->service_id);
        $service = Service::findOrFail($request->service_id);
        $ongkir = $request->delivery_type === 'antar' ? 15000 : 0;
        $total = $service->price + $ongkir;

        $order = Order::create([
            'order_code' => Order::generateOrderCode(),
            'user_id' => Auth::id(),
            'service_id' => $service->id,
            'total_price' => $total,
            'order_type' => 'media_sosial',
            'payment_method' => $request->payment_method,
            'payment_status' => 'belum_bayar',
            'delivery_type' => $request->delivery_type,
            'delivery_address' => $request->delivery_address,
            'ongkir' => $ongkir,
            'notes' => $request->notes,
        ]);

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'updated_by' => Auth::id(),
            'created_at' => now(),
        ]);

        // Notify all admins about new order
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'notifiable_id' => $admin->id,
                'notifiable_type' => User::class,
                'type' => 'new_order',
                'data' => [
                    'order_id' => $order->id,
                    'order_code' => $order->order_code,
                    'customer_name' => $order->user->name,
                    'service_name' => $service->service_name,
                    'total_price' => $order->total_price,
                    'message' => "Pesanan baru {$order->order_code} dari {$order->user->name}",
                ],
            ]);
        }

        return redirect()->route('orders.my')->with('success', 'Pesanan berhasil dibuat: ' . $order->order_code . ' — DP 50% ('.number_format($service->price*0.5,0,',','.').') wajib dibayar sebelum diproses. Metode: '.$request->payment_method);
    }

    // Admin offline order
    public function adminCreate()
    {
        $services = Service::all();
        $customers = User::where('role', 'customer')->get();
        $store = \App\Models\StoreSetting::current();
        return view('admin.orders.create', compact('services', 'customers', 'store'));
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'payment_method' => 'required|in:cash,transfer_bank,dana,ovo,gopay,shopeepay,lainnya',
            'payment_status' => 'required|in:belum_bayar,dp_50,lunas',
            'delivery_type' => 'required|in:ambil_di_toko,antar',
            'delivery_address' => 'nullable|required_if:delivery_type,antar|string',
            'notes' => 'nullable|string',
            'order_type' => 'required|in:media_sosial,offline',
        ]);
        $service = Service::findOrFail($request->service_id);
        $ongkir = $request->delivery_type === 'antar' ? 15000 : 0;
        $total = $service->price + $ongkir;
        $order = Order::create([
            'order_code' => Order::generateOrderCode(),
            'user_id' => $request->user_id,
            'service_id' => $service->id,
            'total_price' => $total,
            'order_type' => $request->order_type,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'delivery_type' => $request->delivery_type,
            'delivery_address' => $request->delivery_address,
            'ongkir' => $ongkir,
            'notes' => $request->notes,
        ]);
        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'updated_by' => Auth::id(),
            'created_at' => now(),
        ]);

        // Notify all admins about new offline order
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'notifiable_id' => $admin->id,
                'notifiable_type' => User::class,
                'type' => 'new_order',
                'data' => [
                    'order_id' => $order->id,
                    'order_code' => $order->order_code,
                    'customer_name' => $order->user->name,
                    'service_name' => $service->service_name,
                    'total_price' => $order->total_price,
                    'message' => "Pesanan offline baru {$order->order_code} untuk {$order->user->name}",
                ],
            ]);
        }

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
        // DP 50% wajib sebelum diproses
        if (in_array($request->status, ['diproses','selesai']) && $order->payment_status === 'belum_bayar') {
            return back()->withErrors(['status' => 'Pesanan tidak bisa diproses sebelum DP 50% dibayar. Ubah status pembayaran dulu menjadi DP 50% / Lunas.']);
        }
        OrderStatus::create([
            'order_id' => $order->id,
            'status' => $request->status,
            'updated_by' => Auth::id(),
            'created_at' => now(),
        ]);

        // Notify customer about order status update
        Notification::create([
            'notifiable_id' => $order->user_id,
            'notifiable_type' => User::class,
            'type' => 'order_status_update',
            'data' => [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'status' => $request->status,
                'message' => "Status pesanan {$order->order_code} diperbarui menjadi: {$request->status}",
            ],
        ]);

        return back()->with('success', 'Status diperbarui ke ' . $request->status);
    }

    public function updatePayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:belum_bayar,dp_50,lunas',
        ]);
        $order->update(['payment_status' => $request->payment_status]);

        // Notify customer about payment status update
        Notification::create([
            'notifiable_id' => $order->user_id,
            'notifiable_type' => User::class,
            'type' => 'order_status_update',
            'data' => [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'payment_status' => $request->payment_status,
                'message' => "Status pembayaran pesanan {$order->order_code} diperbarui menjadi: {$request->payment_status}",
            ],
        ]);

        return back()->with('success', 'Status pembayaran diperbarui ke ' . $request->payment_status);
    }
}
