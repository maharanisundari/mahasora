<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update data lama online -> media_sosial dulu sebelum ubah enum
        DB::table('orders')->where('order_type', 'online')->update(['order_type' => 'media_sosial']);
        // Ubah enum order_type dari online/offline menjadi media_sosial/offline
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_type ENUM('media_sosial','offline') NOT NULL DEFAULT 'offline'");
    }

    public function down(): void
    {
        DB::table('orders')->where('order_type', 'media_sosial')->update(['order_type' => 'online']);
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_type ENUM('online','offline') NOT NULL DEFAULT 'online'");
    }
};
