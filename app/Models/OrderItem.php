<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'product_name', 'voucher_code', 'voucher_discount', 'quantity', 'price'];

    // An order item belong to one order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    // An order item has one product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
