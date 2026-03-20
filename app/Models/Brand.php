<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['name', 'logo'];
    // One brand can has many products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
