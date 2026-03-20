<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'product_image',
        'description',
        'price',
        'category_id',
        'brand_id',
        'stock',
    ];

    // A product belong to one brand
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

  
    //* Get Image Helper Function
    public function getImageUrlAttribute(): ?string
    {
        $path = $this->product_image;

        if (empty($path)) {
            return null;
        }

        // Case 1: seeded path — lives directly in public/ folder
        // e.g. "images/products/phones/iphone.jpg" → /images/products/phones/iphone.jpg
        if (str_starts_with($path, 'images/')) {
           return asset($path);
        }

        // Case 2: admin-uploaded file stored on the public disk
        // e.g. "products/samsung-s24.jpg" → /storage/products/samsung-s24.jpg
        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        // Case 3: fallback — treat as a direct public asset path
        return asset($path);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

