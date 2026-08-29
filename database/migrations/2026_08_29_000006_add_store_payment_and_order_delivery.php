<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('opening_hours');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');
            $table->text('payment_instructions')->nullable()->after('bank_account_name');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('delivery_type', ['ambil_di_toko', 'antar'])->default('ambil_di_toko')->after('payment_status');
            $table->text('delivery_address')->nullable()->after('delivery_type');
            $table->decimal('ongkir', 12, 2)->default(0)->after('delivery_address');
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['bank_name','bank_account_number','bank_account_name','payment_instructions']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_type','delivery_address','ongkir']);
        });
    }
};
