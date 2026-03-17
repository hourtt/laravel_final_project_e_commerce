<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'product_id',
        'category_id',
        'usage_limit',
        'used_count',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'status'     => 'boolean',
    ];

   // Ensure boolean status is stored correctly for Postgres using DB::raw.

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? \DB::raw('true') : \DB::raw('false');
    }

    // Relationship: the specific product this voucher is tied to (nullable).
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship: the specific category this voucher is tied to (nullable).
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Generate a random uppercase voucher code.
    public static function generateCode(int $length = 8): string
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    // Check if the voucher is currently valid (active, not expired, usage not exceeded).
    // Optionally pass a product to validate product-specific or category-specific vouchers.
    public function isValid(?Product $product = null): array
    {
        if (!$this->status) {
            return ['valid' => false, 'message' => 'This voucher code is inactive or has been expired.'];
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return ['valid' => false, 'message' => 'This voucher code has expired.'];
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'This voucher code has reached its usage limit.'];
        }

        // If tied to a specific product, check that the user is buying it
        if ($this->product_id !== null) {
            if (!$product || (int)$this->product_id !== (int)$product->id) {
                return ['valid' => false, 'message' => 'This voucher code is only valid for "' . ($this->product->name ?? 'a specific product') . '".'];
            }
        }

        // If tied to a specific category, check that the product belongs to it
        if ($this->category_id !== null) {
            if (!$product || (int)$this->category_id !== (int)$product->category_id) {
                return ['valid' => false, 'message' => 'This voucher code is only valid for products in the "' . ($this->category->name ?? 'specific category') . '" category.'];
            }
        }

        return ['valid' => true, 'message' => 'Voucher valid!'];
    }

    // Calculate the discount amount for a given subtotal.
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->discount_type === 'percentage') {
            $discount = $subtotal * ($this->discount_value / 100);
        } else {
            $discount = $this->discount_value;
        }

        // Discount cannot exceed the subtotal
        return min($discount, $subtotal);
    }

    // Atomically increment usage and deactivate when limit is reached.
    public function incrementUsage(): void
    {
        // Step 1: increment used_count in one atomic statement
        \DB::table('vouchers')
            ->where('id', $this->id)
            ->increment('used_count');

        // Step 2: deactivate in one atomic statement if limit is now reached.
        // We refresh the model first to get the updated used_count from DB.
        $this->refresh();
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit && $this->status) {
            $this->update(['status' => false]);
        }

        // Refresh the in-memory model so callers see the updated values
        $this->refresh();
    }
}
