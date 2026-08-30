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

        // Mark order_status_update notifications as read for this order
        if (auth()->user()->role === 'customer') {
            auth()->user()->notifications()
                ->where('type', 'order_status_update')
                ->where('data->order_id', $order->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

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
        // Filter cancellation requests
        if ($request->filled('cancellation')) {
            $query->where('cancellation_status', $request->cancellation);
        }

        // Mark all new_order notifications as read when admin views monitoring orders
        if (auth()->user()->role === 'admin') {
            auth()->user()->notifications()
                ->where('type', 'new_order')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
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

    // Customer: Request cancellation
    public function requestCancellation(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if order can be cancelled
        if (in_array($order->current_status, ['selesai', 'dibatalkan'])) {
            return back()->withErrors(['cancellation' => 'Pesanan yang sudah selesai atau dibatalkan tidak bisa dibatalkan lagi.']);
        }

        if ($order->cancellation_status !== 'none') {
            return back()->withErrors(['cancellation' => 'Permintaan pembatalan sudah diajukan sebelumnya.']);
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $order->update([
            'cancellation_status' => 'requested',
            'cancellation_reason' => $request->cancellation_reason,
            'cancellation_requested_at' => now(),
        ]);

        // Notify all admins about cancellation request
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'notifiable_id' => $admin->id,
                'notifiable_type' => User::class,
                'type' => 'cancellation_request',
                'data' => [
                    'order_id' => $order->id,
                    'order_code' => $order->order_code,
                    'customer_name' => $order->user->name,
                    'reason' => $request->cancellation_reason,
                    'message' => "{$order->user->name} meminta pembatalan pesanan {$order->order_code}: {$request->cancellation_reason}",
                ],
            ]);
        }

        return back()->with('success', 'Permintaan pembatalan pesanan telah dikirim ke admin. Menunggu persetujuan.');
    }

    // Admin: Accept cancellation
    public function acceptCancellation(Request $request, Order $order)
    {
        if ($order->cancellation_status !== 'requested') {
            return back()->withErrors(['cancellation' => 'Tidak ada permintaan pembatalan yang menunggu.']);
        }

        $order->update([
            'cancellation_status' => 'accepted',
            'cancellation_processed_at' => now(),
            'cancellation_processed_by' => Auth::id(),
        ]);

        // Update order status to dibatalkan
        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'dibatalkan',
            'updated_by' => Auth::id(),
            'created_at' => now(),
        ]);

        // Notify customer
        Notification::create([
            'notifiable_id' => $order->user_id,
            'notifiable_type' => User::class,
            'type' => 'cancellation_response',
            'data' => [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'accepted' => true,
                'message' => "Permintaan pembatalan pesanan {$order->order_code} telah DITERIMA oleh admin.",
            ],
        ]);

        return back()->with('success', 'Permintaan pembatalan diterima. Pesanan dibatalkan.');
    }

    // Admin: Reject cancellation
    public function rejectCancellation(Request $request, Order $order)
    {
        if ($order->cancellation_status !== 'requested') {
            return back()->withErrors(['cancellation' => 'Tidak ada permintaan pembatalan yang menunggu.']);
        }

        $order->update([
            'cancellation_status' => 'rejected',
            'cancellation_processed_at' => now(),
            'cancellation_processed_by' => Auth::id(),
        ]);

        // Notify customer
        Notification::create([
            'notifiable_id' => $order->user_id,
            'notifiable_type' => User::class,
            'type' => 'cancellation_response',
            'data' => [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'accepted' => false,
                'message' => "Permintaan pembatalan pesanan {$order->order_code} telah DITOLAK oleh admin.",
            ],
        ]);

        return back()->with('success', 'Permintaan pembatalan ditolak. Pesanan tetap berlangsung.');
    }
}
