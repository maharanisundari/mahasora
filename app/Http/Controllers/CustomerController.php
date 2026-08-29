<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer');
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")->orWhere('email', 'like', "%$q%")->orWhere('phone', 'like', "%$q%");
            });
        }
        $customers = $query->withCount('orders')->latest()->paginate(10)->withQueryString();
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'customer_status' => 'nullable|in:active,inactive',
        ]);
        $data = $request->only('name', 'email', 'phone', 'address', 'bio', 'customer_status');
        $data['password'] = $request->password;
        $data['role'] = 'customer';
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        User::create($data);
        return redirect()->route('admin.customers.index')->with('success', 'Pelanggan berhasil ditambahkan');
    }

    public function show(User $customer)
    {
        $customer->load('orders.service');
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(User $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'customer_status' => 'nullable|in:active,inactive',
        ]);
        $data = $request->only('name', 'email', 'phone', 'address', 'bio', 'customer_status');
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }
        if ($request->hasFile('avatar')) {
            if ($customer->avatar) Storage::disk('public')->delete($customer->avatar);
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        $customer->update($data);
        return redirect()->route('admin.customers.index')->with('success', 'Pelanggan berhasil diperbarui');
    }

    public function destroy(User $customer)
    {
        if ($customer->avatar) Storage::disk('public')->delete($customer->avatar);
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Pelanggan berhasil dihapus');
    }
}
