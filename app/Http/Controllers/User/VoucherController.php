<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * AJAX: Validate and apply a voucher code.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $code = strtoupper(trim($request->code));
        $voucher = Voucher::where('code', $code)->first();

        // 1. Existence: Verify if the voucher code exists
        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid voucher code.',
            ], 422);
        }

        // 2. Status: Check if active
        if (!$voucher->status) {
            return response()->json([
                'success' => false,
                'message' => 'This voucher is currently inactive.',
            ], 422);
        }

        // 3. Expiry: Check if current date is within valid range
        if ($voucher->expires_at && $voucher->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher expired',
            ], 422);
        }

        // 4. Usage Rule: Prevent duplicate usage per product
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty.'], 422);
        }

        $userId = auth()->id();
        if ($userId) {
            foreach ($cart as $productId => $cartItem) {
                // If this voucher applies to this specific product (either tied or general)
                if ($voucher->product_id === null || (int)$voucher->product_id === (int)$productId) {
                    
                    // Check order_items to see if the user has already used this voucher for this product
                    $alreadyUsed = OrderItem::where('product_id', $productId)
                        ->where('voucher_code', $voucher->code)
                        ->whereHas('order', function ($query) use ($userId) {
                            $query->where('user_id', $userId);
                        })
                        ->exists();

                    if ($alreadyUsed) {
                        $product = Product::find($productId);
                        return response()->json([
                            'success' => false,
                            'message' => 'You have already used this voucher for ' . ($product->name ?? 'this product'),
                        ], 422);
                    }
                }
            }
        }

        // 5. Product-specific scope check (is the tied product in cart?)
        $matchedProductId = null;
        if ($voucher->product_id !== null) {
            $cartProductIds = array_map('intval', array_keys($cart));
            if (!in_array((int) $voucher->product_id, $cartProductIds, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This voucher is only valid for "' . ($voucher->product->name ?? 'a specific product') . '".',
                ], 422);
            }
            $matchedProductId = (int) $voucher->product_id;
        }

        // 6. Final model-based validation (e.g. usage_limit)
        $result = $voucher->isValid($matchedProductId);
        if (!$result['valid']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        // Recalculate totals from DB
        $productIds = array_keys($cart);
        $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $subtotal   = 0;
        $discountableSubtotal = 0;

        foreach ($cart as $productId => $cartItem) {
            if (isset($products[$productId])) {
                $itemTotal = $products[$productId]->price * $cartItem['quantity'];
                $subtotal += $itemTotal;
                
                if ($voucher->product_id === null || $voucher->product_id == $productId) {
                    $discountableSubtotal += $itemTotal;
                }
            }
        }

        $discountAmount = $voucher->calculateDiscount($discountableSubtotal);
        $finalTotal     = $subtotal - $discountAmount;

        if ($finalTotal <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Vouchers cannot be used to reduce the order total to $0.00.',
            ], 422);
        }

        // Store in session
        session([
            'voucher_code'     => $voucher->code,
            'voucher_id'       => $voucher->id,
            'voucher_discount' => $discountAmount,
        ]);

        return response()->json([
            'success'          => true,
            'message'          => 'Voucher applied successfully. Discount: $' . number_format($discountAmount, 2),
            'voucher_code'     => $voucher->code,
            'discount_type'    => $voucher->discount_type,
            'discount_value'   => $voucher->discount_value,
            'discount_amount'  => round($discountAmount, 2),
            'subtotal'         => round($subtotal, 2),
            'final_total'      => round($finalTotal, 2),
        ]);
    }

    /**
     * AJAX: Remove the currently applied voucher from session.
     */
    public function remove(Request $request)
    {
        session()->forget(['voucher_code', 'voucher_id', 'voucher_discount']);

        $cart = session()->get('cart', []);
        $productIds = array_keys($cart);
        $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $subtotal   = 0;

        foreach ($cart as $productId => $cartItem) {
            if (isset($products[$productId])) {
                $subtotal += $products[$productId]->price * $cartItem['quantity'];
            }
        }

        return response()->json([
            'success'       => true,
            'message'       => 'Voucher removed.',
            'final_total'   => round($subtotal, 2),
            'subtotal'      => round($subtotal, 2),
            'discount_amount' => 0,
        ]);
    }
}