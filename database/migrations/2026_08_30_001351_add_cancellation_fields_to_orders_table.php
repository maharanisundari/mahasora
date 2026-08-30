<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('cancellation_status', ['none', 'requested', 'accepted', 'rejected'])
                ->default('none')
                ->after('payment_status');
            $table->text('cancellation_reason')->nullable()->after('cancellation_status');
            $table->timestamp('cancellation_requested_at')->nullable()->after('cancellation_reason');
            $table->timestamp('cancellation_processed_at')->nullable()->after('cancellation_requested_at');
            $table->foreignId('cancellation_processed_by')->nullable()->constrained('users')->after('cancellation_processed_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cancellation_processed_by');
            $table->dropColumn([
                'cancellation_status',
                'cancellation_reason',
                'cancellation_requested_at',
                'cancellation_processed_at',
                'cancellation_processed_by',
            ]);
        });
    }
};