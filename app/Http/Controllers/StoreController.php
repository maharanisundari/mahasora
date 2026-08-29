<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    // Pembeli + guest + admin boleh lihat (hanya lihat, tidak edit)
    public function show()
    {
        $store = StoreSetting::current();
        return view('store.show', compact('store'));
    }

    // Admin: form edit
    public function edit()
    {
        $store = StoreSetting::current();
        return view('admin.store.edit', compact('store'));
    }

    // Admin: update (hanya admin)
    public function update(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'opening_hours' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
        ]);

        $store = StoreSetting::current();
        $data = $request->only(['store_name','description','address','phone','email','whatsapp','instagram','facebook','opening_hours']);

        if ($request->hasFile('logo')) {
            if ($store->logo) Storage::disk('public')->delete($store->logo);
            $data['logo'] = $request->file('logo')->store('store', 'public');
        }

        $store->update($data);

        return redirect()->route('store.show')->with('success', 'Informasi toko MahaSora berhasil diperbarui');
    }
}
