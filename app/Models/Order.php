<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** Human-readable (Bangla) labels for every order status. */
    public const STATUS_LABELS = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    protected $fillable = [
        'order_number',
        'user_id',
        'name',
        'phone',
        'division',
        'district',
        'thana',
        'address',
        'notes',
        'subtotal',
        'shipping_fee',
        'total',
        'delivery_zone',
        'status',
        'payment_method',
        'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
