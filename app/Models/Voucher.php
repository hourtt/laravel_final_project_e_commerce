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
        'usage_limit',
        'used_count',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'status'     => 'boolean',
    ];

    /**
     * Relationship: the specific product this voucher is tied to (nullable).
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Generate a random uppercase voucher code.
     */
    public static function generateCode(int $length = 8): string
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Check if the voucher is currently valid (active, not expired, usage not exceeded).
     * Optionally pass a product_id to validate product-specific vouchers.
     */
    public function isValid(?int $productId = null): array
    {
        if (!$this->status) {
            return ['valid' => false, 'message' => 'This voucher code is inactive.'];
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return ['valid' => false, 'message' => 'This voucher code has expired.'];
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'This voucher code has reached its usage limit.'];
        }

        // If tied to a specific product, check that the user is buying it
        if ($this->product_id !== null && $productId !== null && $this->product_id !== $productId) {
            return ['valid' => false, 'message' => 'This voucher code cannot be used for this product.'];
        }

        return ['valid' => true, 'message' => 'Voucher applied successfully!'];
    }

    /**
     * Calculate the discount amount for a given subtotal.
     */
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

    /**
     * Increment the used_count counter atomically.
     * Automatically deactivates the voucher in the same DB round-trip when the
     * usage limit is reached — no Eloquent events, no race conditions.
     */
    public function incrementUsage(): void
    {
        // Step 1: increment used_count in one atomic statement
        \DB::table('vouchers')
            ->where('id', $this->id)
            ->increment('used_count');

        // Step 2: deactivate in one atomic statement if limit is now reached.
        // WHERE used_count >= usage_limit ensures this is idempotent and safe
        // under concurrent requests.
        \DB::table('vouchers')
            ->where('id', $this->id)
            ->whereNotNull('usage_limit')
            ->whereColumn('used_count', '>=', 'usage_limit')
            ->where('status', true)
            ->update(['status' => false]);

        // Refresh the in-memory model so callers see the updated values
        $this->refresh();
    }
}
