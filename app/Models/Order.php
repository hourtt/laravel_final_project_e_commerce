<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total_price', 'status', 'voucher_code', 'voucher_discount'];

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
}

