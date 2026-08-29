<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Admin MahaSora',
            'email' => 'admin@mahasora.test',
            'password' => 'password',
            'phone' => '081234567890',
            'address' => 'Jl. Katapang No. 1, Bandung',
            'role' => 'admin',
            'bio' => 'Administrator TeFa SMKN 1 Katapang - MahaSora',
            'customer_status' => 'active',
        ]);

        \App\Models\StoreSetting::current();

        $customers = [
            ['name' => 'Budi Santoso', 'email' => 'budi@mahasora.test', 'phone' => '081111111111', 'address' => 'Katapang, Bandung'],
            ['name' => 'Siti Aminah', 'email' => 'siti@mahasora.test', 'phone' => '082222222222', 'address' => 'Soreang, Bandung'],
            ['name' => 'Andi Wijaya', 'email' => 'andi@mahasora.test', 'phone' => '083333333333', 'address' => 'Cililin, Bandung'],
        ];
        $customerModels = [];
        foreach ($customers as $c) {
            $customerModels[] = \App\Models\User::create([
                'name' => $c['name'],
                'email' => $c['email'],
                'password' => 'password',
                'phone' => $c['phone'],
                'address' => $c['address'],
                'role' => 'customer',
                'bio' => 'Pelanggan setia MahaSora',
            ]);
        }

        $services = [
            ['service_name' => 'Jasa Pembuatan Website Company Profile', 'description' => 'Website responsif + domain .com + SEO basic, pengerjaan 7 hari.', 'price' => 2500000],
            ['service_name' => 'Desain Logo & Branding', 'description' => 'Desain logo profesional + brand guideline + mockup.', 'price' => 750000],
            ['service_name' => 'Jasa Service Laptop & Komputer', 'description' => 'Service hardware, install ulang, pembersihan, ganti sparepart.', 'price' => 150000],
            ['service_name' => 'Kursus Private Coding Laravel', 'description' => 'Belajar Laravel dari nol sampai deploy, 8x pertemuan.', 'price' => 1200000],
            ['service_name' => 'Foto Produk & Editing', 'description' => 'Foto produk e-commerce 20 foto + editing premium.', 'price' => 500000],
            ['service_name' => 'Jasa Ketik & Print Dokumen', 'description' => 'Ketik cepat, print warna & hitam putih, jilid rapi.', 'price' => 50000],
        ];
        $serviceModels = [];
        foreach ($services as $s) {
            $serviceModels[] = \App\Models\Service::create($s);
        }

        // Sample orders
        foreach (range(1,5) as $i) {
            $cust = $customerModels[array_rand($customerModels)];
            $svc = $serviceModels[array_rand($serviceModels)];
            $order = \App\Models\Order::create([
                'order_code' => \App\Models\Order::generateOrderCode(),
                'user_id' => $cust->id,
                'service_id' => $svc->id,
                'total_price' => $svc->price,
                'order_type' => rand(0,1) ? 'media_sosial' : 'offline',
                'payment_method' => ['cash','transfer_bank','dana'][array_rand(['cash','transfer_bank','dana'])],
                'payment_status' => ['belum_bayar','dp_50','lunas'][array_rand(['belum_bayar','dp_50','lunas'])],
                'delivery_type' => rand(0,1) ? 'ambil_di_toko' : 'antar',
                'delivery_address' => rand(0,1) ? 'Jl. Contoh No. '.$i : null,
                'ongkir' => rand(0,1) ? 15000 : 0,
                'notes' => 'Demo order #' . $i,
            ]);
            \App\Models\OrderStatus::create(['order_id' => $order->id, 'status' => 'pending', 'updated_by' => $admin->id, 'created_at' => now()->subDays($i)]);
            if ($i % 2 == 0) {
                \App\Models\OrderStatus::create(['order_id' => $order->id, 'status' => 'diproses', 'updated_by' => $admin->id, 'created_at' => now()->subDays($i-1)]);
            }
            if ($i == 4) {
                \App\Models\OrderStatus::create(['order_id' => $order->id, 'status' => 'selesai', 'updated_by' => $admin->id, 'created_at' => now()]);
            }
        }
    }
}
