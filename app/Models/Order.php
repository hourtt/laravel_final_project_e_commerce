<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'total_price',
        'status',
        'voucher_code',
        'voucher_discount',
        'shipping_full_name',
        'shipping_phone_number',
        'shipping_street_address',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
    ];

    // An order belong to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // An order can has many items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the address associated with the order.
     */
    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}

