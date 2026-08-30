<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'service_id',
        'total_price',
        'order_type',
        'payment_method',
        'payment_status',
        'delivery_type',
        'delivery_address',
        'ongkir',
        'notes',
        'cancellation_status',
        'cancellation_reason',
        'cancellation_requested_at',
        'cancellation_processed_at',
        'cancellation_processed_by',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'ongkir' => 'decimal:2',
        'cancellation_requested_at' => 'datetime',
        'cancellation_processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cancellationProcessor()
    {
        return $this->belongsTo(User::class, 'cancellation_processed_by');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function statuses()
    {
        return $this->hasMany(OrderStatus::class)->orderByDesc('created_at');
    }

    public function latestStatus()
    {
        return $this->hasOne(OrderStatus::class)->latestOfMany('created_at');
    }

    public function getCurrentStatusAttribute(): string
    {
        return $this->latestStatus?->status ?? 'pending';
    }

    public static function generateOrderCode(): string
    {
        $date = now()->format('Ymd');
        $count = static::whereDate('created_at', now()->toDateString())->count() + 1;
        return sprintf('TRX-%s-%03d', $date, $count);
    }
}
