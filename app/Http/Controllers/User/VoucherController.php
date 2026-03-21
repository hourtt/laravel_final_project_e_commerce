<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    // AJAX: Validate and apply a voucher code.
    public function apply(Request $request)
    {
        $request->validate([
            'code'       => 'required|string|max:50',
            'product_id' => 'nullable|integer|exists:products,id',
        ]);

        $code      = strtoupper(trim($request->code));
        $voucher   = Voucher::where('code', $code)->first();
        $productId = $request->product_id;

        // 1. Existence
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Invalid voucher code.'], 422);
        }

        // 2. Status & Expiry & Usage limit (Model-based validation)
        // Load the targeted product
        $product = $productId ? Product::find($productId) : null;
        $result = $voucher->isValid($product);
        if (!$result['valid']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }

        // 3. Cart context check
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty.'], 422);
        }

        if ($productId && !isset($cart[$productId])) {
            return response()->json(['success' => false, 'message' => 'This product is not in your cart.'], 422);
        }

        // 4. Usage Rule: Prevent duplicate usage of SAME voucher in past orders
        $userId = auth()->id();
        if ($userId) {
            $alreadyUsed = OrderItem::where('voucher_code', $voucher->code)
                ->whereHas('order', function ($query) use ($userId) {
                    $query->where('user_id', $userId)->whereIn('status', ['Completed', 'Pending']);
                })
                ->exists();

            if ($alreadyUsed) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already used this voucher code in a previous order.',
                ], 422);
            }
        }

        // 5. Prevent using the same voucher code multiple times in the SAME session
        $appliedVouchers = session()->get('applied_vouchers', []);
        foreach ($appliedVouchers as $pId => $data) {
            if ($data['code'] === $voucher->code) {
                return response()->json([
                    'success' => false,
                    'message' => 'This voucher is already applied to this order.',
                ], 422);
            }
        }

        // 6. Prevent multiple vouchers on the SAME product (one voucher per item line)
        if ($productId && isset($appliedVouchers[$productId])) {
            return response()->json([
                'success' => false,
                'message' => 'A voucher is already applied to this product.',
            ], 422);
        }

        // 7. Calculate Discount
        // If it's a shop-wide voucher (no product_id/category_id), apply to the specific item clicked
        // Or if it's tied to product/category, apply to that item.
        // If no product_id was sent (legacy), we apply to the whole cart? 
        // For this refactor, we assume product_id is sent from the per-item UI.

        $itemQty = $cart[$productId]['quantity'];
        $itemTotal = $product->price * $itemQty;
        $discountAmount = $voucher->calculateDiscount($itemTotal);

        // Store in session
        $appliedVouchers[$productId] = [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'discount' => $discountAmount,
        ];
        session(['applied_vouchers' => $appliedVouchers]);

        // array_key() returns only keys from the cart array (product ids and not values)
        $productIds = array_keys($cart);
        // Use whereIn() to fetch multiple products in the cart at once
        $cartProducts = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $subtotal = 0;
        foreach ($cart as $id => $item) {
            if (isset($cartProducts[$id])) {
                $subtotal += $cartProducts[$id]->price * $item['quantity'];
            }
        }

        $totalDiscount = array_sum(array_column($appliedVouchers, 'discount'));
        $finalTotal = max(0, $subtotal - $totalDiscount);

        return response()->json([
            'success' => true,
            'message' => 'Voucher applied to ' . $product->name,
            'voucher_code' => $voucher->code,
            'item_id' => $productId,
            'discount_amount' => round($discountAmount, 2),
            'total_discount' => round($totalDiscount, 2),
            'subtotal' => round($subtotal, 2),
            'final_total' => round($finalTotal, 2),
            'applied_vouchers' => $appliedVouchers,
        ]);
    }

    // AJAX: Remove a specific voucher.
    public function remove(Request $request)
    {
        $productId = $request->product_id;
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);
        $appliedVouchers = session()->get('applied_vouchers', []);

        if (isset($appliedVouchers[$productId])) {
            unset($appliedVouchers[$productId]);
            session(['applied_vouchers' => $appliedVouchers]);
        }

        // Recalculate
        $cart = session()->get('cart', []);
        $totalSubtotal = 0;

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        foreach ($cart as $id => $item) {
            if (isset($products[$id]))
                $totalSubtotal += $products[$id]->price * $item['quantity'];
        }

        $totalDiscount = array_sum(array_column($appliedVouchers, 'discount'));
        $finalTotal = max(0, $totalSubtotal - $totalDiscount);

        return response()->json([
            'success' => true,
            'message' => 'Voucher removed.',
            'total_discount' => round($totalDiscount, 2),
            'subtotal' => round($totalSubtotal, 2),
            'final_total' => round($finalTotal, 2),
        ]);
    }
}