<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    // Public catalog
    public function catalog(Request $request)
    {
        $query = Service::query();
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('service_name', 'like', "%$q%")->orWhere('description', 'like', "%$q%");
            });
        }
        $services = $query->latest()->paginate(12)->withQueryString();
        return view('catalog.index', compact('services'));
    }

    public function show(Service $service)
    {
        return view('catalog.show', compact('service'));
    }

    // Admin CRUD
    public function index(Request $request)
    {
        $query = Service::query();
        if ($request->filled('q')) {
            $query->where('service_name', 'like', "%{$request->q}%");
        }
        $services = $query->latest()->paginate(10)->withQueryString();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);
        $data = $request->only('service_name', 'description', 'price');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }
        Service::create($data);
        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil ditambahkan');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);
        $data = $request->only('service_name', 'description', 'price');
        if ($request->hasFile('image')) {
            if ($service->image) Storage::disk('public')->delete($service->image);
            $data['image'] = $request->file('image')->store('services', 'public');
        }
        $service->update($data);
        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil diperbarui');
    }

    public function destroy(Service $service)
    {
        if ($service->image) Storage::disk('public')->delete($service->image);
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dihapus');
    }
}
