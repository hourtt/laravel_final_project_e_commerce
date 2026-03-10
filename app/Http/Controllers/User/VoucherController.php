<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * AJAX: Validate and apply a voucher code.
     *
     * The client sends: { code, product_ids[] }
     * We validate the code, check product scope, and return the discount data.
     * The discount amount stays in the session for `preparePayment()` to deduct.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            // product_ids no longer needed — we read the cart from session server-side
        ]);

        $voucher = Voucher::where('code', strtoupper(trim($request->code)))->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid voucher code.',
            ], 422);
        }

        // ── Product-scope check ───────────────────────────────────────────────
        // Read the cart directly from the SERVER-SIDE session.
        // Never trust client-sent product IDs — the session is the single source
        // of truth and avoids all type-mismatch (int vs string) issues entirely.
        $matchedProductId = null;
        if ($voucher->product_id !== null) {
            $cart           = session()->get('cart', []);
            $cartProductIds = array_map('intval', array_keys($cart)); // keys are always strings in PHP

            if (!in_array((int) $voucher->product_id, $cartProductIds, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This voucher is only valid for "' . ($voucher->product->name ?? 'a specific product') . '". That product is not in your cart.',
                ], 422);
            }
            $matchedProductId = $voucher->product_id;
        }

        $result = $voucher->isValid($matchedProductId);
        if (!$result['valid']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        // Calculate discount on current cart subtotal
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty.'], 422);
        }

        // More reliable: recalculate from DB
        $productIds = array_keys($cart);
        $products   = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');
        $subtotal   = 0;
        $discountableSubtotal = 0;

        foreach ($cart as $productId => $cartItem) {
            if (isset($products[$productId])) {
                $itemTotal = $products[$productId]->price * $cartItem['quantity'];
                $subtotal += $itemTotal;
                
                // If the voucher is tied to a specific product, only that product's total is considered for the percentage discount.
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

        // Store in session so preparePayment() can read it
        session([
            'voucher_code'     => $voucher->code,
            'voucher_id'       => $voucher->id,
            'voucher_discount' => $discountAmount,
        ]);

        return response()->json([
            'success'          => true,
            'message'          => 'Voucher applied successfully!',
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

        // Recalculate subtotal so the UI can revert
        $cart = session()->get('cart', []);
        $productIds = array_keys($cart);
        $products   = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');
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
