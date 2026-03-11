<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Voucher;
use App\Services\PayWayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $payWayService;

    public function __construct(PayWayService $payWayService)
    {
        $this->payWayService = $payWayService;
    }

    /**
     * Show cart & prepare ABA payment hash.
     * Creates a pending order ONLY if no valid pending order already exists
     * for the current cart contents, to avoid duplicating orders on refresh.
     */
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);

        // Initialize variables to avoid "undefined variable" errors in the view
        $cartData        = [];
        $total           = 0;
        $voucherDiscount = 0.0;
        $voucherCode     = null;
        $discountedTotal = 0.0;
        $cartProductIds  = [];   // passed to view so checkout-script can send it via AJAX
        $hash = $tranId = $amount = $merchant_id = $req_time = $currency = $payment_option = $return_url = $continue_success_url = '';

        // if (empty($cart)) {
        //     return redirect()->route('dashboard')->with('error', 'Your cart is empty.');
        // }

        if (!empty($cart)) {
            $productIds = array_keys($cart);
            $products = Product::whereIn('id', $productIds)->get();

            // Build cartData & calculate total (no DB lock needed at view stage)
            foreach ($products as $product) {
                $qty = $cart[$product->id]['quantity'];
                if ($product->stock < $qty) {
                    return redirect()->route('dashboard')
                        ->with('error', "Insufficient stock for {$product->name}.");
                }
                $total += $product->price * $qty;
                $cartData[] = [
                    'product'  => $product,
                    'quantity' => $qty,
                    'subtotal' => $product->price * $qty,
                ];
            }

            // Apply session voucher discount if any
            $voucherDiscount = (float) session('voucher_discount', 0);
            $voucherCode     = session('voucher_code');
            $discountedTotal = max(0, $total - $voucherDiscount);

            // Validation: Ensure order cannot be created with $0.00
            if ($discountedTotal <= 0) {
                $existingOrderId = session('order_id');
                if ($existingOrderId) {
                    Order::where('id', $existingOrderId)->where('status', 'Pending')->delete();
                    session()->forget('order_id');
                }
                return redirect()->route('dashboard')
                    ->with('error', 'Orders with a total price of $0.00 are not allowed.');
            }

            // Build the product ID list for the JS voucher apply AJAX call.
            // Done here in the controller so it is available in the layout's
            // @include('checkout-script'), which runs outside product-state scope.
            $cartProductIds = $products->pluck('id')->map(fn($id) => (int) $id)->toArray();

            try {
                // Reuse an existing Pending order if it belongs to this user,
                // so refreshing the checkout page does not spawn duplicate orders.
                $existingOrderId = session('order_id');
                $order = null;

                if ($existingOrderId) {
                    $candidate = Order::with('items')->find($existingOrderId);
                    if (
                        $candidate &&
                        $candidate->status === 'Pending' &&
                        $candidate->user_id === Auth::id()
                    ) {
                        $order = $candidate;
                    }
                }

                if ($order) {
                    // ── Sync existing pending order with the current cart ──
                    DB::beginTransaction();

                    $cartProductIds = $products->pluck('id')->toArray();

                    // Delete items that are no longer in the cart
                    OrderItem::where('order_id', $order->id)
                        ->whereNotIn('product_id', $cartProductIds)
                        ->delete();

                    // Calculate which product gets the voucher discount
                    $voucherProductId = null;
                    if ($voucherCode) {
                        $v = Voucher::where('code', $voucherCode)->first();
                        // If it's a specific product, use that ID. Otherwise if it's a general voucher,
                        // we can either apply it to everything or leave it null (meaning whole order). 
                        // For clarity, we only mark order_items if the voucher is specifically tied to them.
                        $voucherProductId = $v ? $v->product_id : null;
                    }

                    // Add / update items that ARE in the cart
                    foreach ($products as $product) {
                        $qty = $cart[$product->id]['quantity'];
                        
                        $itemVoucherCode = null;
                        $itemVoucherDiscount = null;
                        
                        // If the voucher is specifically for this product OR it's a general voucher
                        if ($voucherCode && ($voucherProductId === null || $voucherProductId === $product->id)) {
                             $itemVoucherCode = $voucherCode;
                             // Assign the full discount to the product line if specific, or proportional if general?
                             // Simplest representation: show the discount amount on the item line where applicable.
                             $itemVoucherDiscount = ($voucherProductId === $product->id) ? $voucherDiscount : null;
                        }

                        OrderItem::updateOrCreate(
                            ['order_id' => $order->id, 'product_id' => $product->id],
                            [
                                'product_name' => $product->name, 
                                'voucher_code' => $itemVoucherCode,
                                'voucher_discount' => $itemVoucherDiscount,
                                'quantity' => $qty, 
                                'price' => $product->price
                            ]
                        );
                    }

                    // Persist total + voucher info so admin can see which code was used
                    $order->update([
                        'total_price'      => $discountedTotal,
                        'voucher_code'     => $voucherCode ?: null,
                        'voucher_discount' => $voucherDiscount > 0 ? $voucherDiscount : null,
                    ]);

                    DB::commit();

                } else {
                    // ── No valid pending order — create a fresh one ──
                    DB::beginTransaction();
                    $order = Order::create([
                        'user_id'          => Auth::id(),
                        'total_price'      => $discountedTotal,
                        'status'           => 'Pending',
                        'voucher_code'     => $voucherCode ?: null,
                        'voucher_discount' => $voucherDiscount > 0 ? $voucherDiscount : null,
                    ]);
                    // Calculate which product gets the voucher discount
                    $voucherProductId = null;
                    if ($voucherCode) {
                        $v = Voucher::where('code', $voucherCode)->first();
                        $voucherProductId = $v ? $v->product_id : null;
                    }

                    foreach ($products as $product) {
                        $qty = $cart[$product->id]['quantity'];
                        
                        $itemVoucherCode = null;
                        $itemVoucherDiscount = null;
                        
                        if ($voucherCode && ($voucherProductId === null || $voucherProductId === $product->id)) {
                             $itemVoucherCode = $voucherCode;
                             $itemVoucherDiscount = ($voucherProductId === $product->id) ? $voucherDiscount : null;
                        }
                        
                        OrderItem::create([
                            'order_id'         => $order->id,
                            'product_id'       => $product->id,
                            'product_name'     => $product->name,
                            'voucher_code'     => $itemVoucherCode,
                            'voucher_discount' => $itemVoucherDiscount,
                            'quantity'         => $qty,
                            'price'            => $product->price,
                        ]);
                    }
                    DB::commit();
                    session(['order_id' => $order->id]);
                }

                // GENERATE ABA PARAMS (use discounted total)
                $merchant_id          = config('payway.merchant_id');
                $req_time             = time();
                $tranId               = 'ORD-' . $order->id . '-' . $req_time;
                $amount               = number_format($discountedTotal, 2, '.', '');
                $currency             = 'USD';
                $payment_option       = 'abapay_khqr';
                $return_url           = base64_encode(route('payment.check'));
                $continue_success_url = route('payment.check');

                // Always store the latest tran_id so checkTransaction() uses
                // the exact tran_id that was submitted to ABA on this page load.
                session(['tran_id' => $tranId]);

                $hashString = $req_time . $merchant_id . $tranId . $amount . $payment_option . $return_url . $continue_success_url . $currency;
                $hash = $this->payWayService->getHash($hashString);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Order creation failed', ['error' => $e->getMessage()]);
                return redirect()->route('dashboard')
                    ->with('error', 'An error occurred during checkout.');
            }
        }
        return view('user.checkout', compact(
            'cartData',
            'cartProductIds',
            'total',
            'voucherDiscount',
            'voucherCode',
            'discountedTotal',
            'hash',
            'tranId',
            'amount',
            'merchant_id',
            'req_time',
            'currency',
            'payment_option',
            'return_url',
            'continue_success_url'
        ));
    }

    /**
     * AJAX: Recalculate cart total from session and return fresh ABA params.
     * Called by JS immediately before submitting the ABA form, so the amount
     * and hash always reflect the current cart — even if the user changed
     * quantities after the page first loaded.
     */
    public function preparePayment(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty.'], 422);
        }

        $productIds = array_keys($cart);
        $products   = Product::whereIn('id', $productIds)->get();

        // ── Recalculate subtotal ──────────────────────────────────────────────
        $total = 0;
        foreach ($products as $product) {
            $qty = $cart[$product->id]['quantity'] ?? 0;
            if ($product->stock < $qty) {
                return response()->json([
                    'error' => "Insufficient stock for {$product->name}. Only {$product->stock} left.",
                ], 422);
            }
            $total += $product->price * $qty;
        }

        // ── Apply voucher discount ────────────────────────────────────────────
        $voucherDiscount = (float) session('voucher_discount', 0);
        $discountedTotal = max(0, $total - $voucherDiscount);

        // Validation for AJAX: total must be > 0
        if ($discountedTotal <= 0) {
            $orderId = session('order_id');
            if ($orderId) {
                Order::where('id', $orderId)->where('status', 'Pending')->delete();
                session()->forget('order_id');
            }
            return response()->json(['error' => 'Orders with $0.00 total are not permitted.'], 422);
        }

        try {
            // ── Resolve order ─────────────────────────────────────────────────
            // Find the existing pending order OR create a fresh one with all items.
            // This prevents the "0 items / $0.00" bug that occurred when the session
            // order_id was stale or the order was somehow invalid.
            $orderId = session('order_id');
            $order   = $orderId ? Order::find($orderId) : null;

            DB::transaction(function () use (
                &$order, $products, $cart, $discountedTotal, $voucherDiscount
            ) {
                // Resolve session voucher fields inside closure
                $voucherCode = session('voucher_code');

                if ($order && $order->status === 'Pending' && $order->user_id === Auth::id()) {
                    // ── Sync existing pending order ───────────────────────────
                    $cartProductIds = $products->pluck('id')->toArray();

                    // Remove items that are no longer in the cart
                    OrderItem::where('order_id', $order->id)
                        ->whereNotIn('product_id', $cartProductIds)
                        ->delete();

                    // Calculate which product gets the voucher discount
                    $voucherProductId = null;
                    if ($voucherCode) {
                        $v = Voucher::where('code', $voucherCode)->first();
                        $voucherProductId = $v ? $v->product_id : null;
                    }

                    // Upsert current cart items
                    foreach ($products as $product) {
                        $qty = $cart[$product->id]['quantity'];
                        
                        $itemVoucherCode = null;
                        $itemVoucherDiscount = null;
                        
                        if ($voucherCode && ($voucherProductId === null || $voucherProductId === $product->id)) {
                             $itemVoucherCode = $voucherCode;
                             $itemVoucherDiscount = ($voucherProductId === $product->id) ? $voucherDiscount : null;
                        }
                        
                        OrderItem::updateOrCreate(
                            ['order_id'   => $order->id, 'product_id' => $product->id],
                            [
                                'product_name'     => $product->name, 
                                'voucher_code'     => $itemVoucherCode,
                                'voucher_discount' => $itemVoucherDiscount,
                                'quantity'         => $qty,       
                                'price'            => $product->price
                            ]
                        );
                    }

                    $order->update([
                        'total_price'      => $discountedTotal,
                        'voucher_code'     => $voucherCode ?: null,
                        'voucher_discount' => $voucherDiscount > 0 ? $voucherDiscount : null,
                    ]);

                } else {
                    // ── No valid order in session — create a fresh one ────────
                    // This handles: page loaded without cart, session expired,
                    // or stale order_id pointing to a non-Pending / foreign order.
                    $order = Order::create([
                        'user_id'          => Auth::id(),
                        'total_price'      => $discountedTotal,
                        'status'           => 'Pending',
                        'voucher_code'     => $voucherCode ?: null,
                        'voucher_discount' => $voucherDiscount > 0 ? $voucherDiscount : null,
                    ]);

                    // Calculate which product gets the voucher discount
                    $voucherProductId = null;
                    if ($voucherCode) {
                        $v = Voucher::where('code', $voucherCode)->first();
                        $voucherProductId = $v ? $v->product_id : null;
                    }

                    foreach ($products as $product) {
                        $qty = $cart[$product->id]['quantity'];
                        
                        $itemVoucherCode = null;
                        $itemVoucherDiscount = null;
                        
                        if ($voucherCode && ($voucherProductId === null || $voucherProductId === $product->id)) {
                             $itemVoucherCode = $voucherCode;
                             $itemVoucherDiscount = ($voucherProductId === $product->id) ? $voucherDiscount : null;
                        }

                        OrderItem::create([
                            'order_id'         => $order->id,
                            'product_id'       => $product->id,
                            'product_name'     => $product->name,
                            'voucher_code'     => $itemVoucherCode,
                            'voucher_discount' => $itemVoucherDiscount,
                            'quantity'         => $qty,
                            'price'            => $product->price,
                        ]);
                    }

                    session(['order_id' => $order->id]);
                }
            });

        } catch (\Exception $e) {
            Log::error('preparePayment: order sync failed', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);
            return response()->json([
                'error' => 'Could not prepare your order. Please reload the checkout page.',
            ], 500);
        }

        // ── Generate fresh ABA payment parameters ─────────────────────────────
        $merchant_id          = config('payway.merchant_id');
        $req_time             = time();
        $tranId               = 'ORD-' . $order->id . '-' . $req_time;
        $amount               = number_format($discountedTotal, 2, '.', '');
        $currency             = 'USD';
        $payment_option       = 'abapay_khqr';
        $return_url           = base64_encode(route('payment.check'));
        $continue_success_url = route('payment.check');

        session(['tran_id' => $tranId]);

        $hashString = $req_time . $merchant_id . $tranId . $amount . $payment_option . $return_url . $continue_success_url . $currency;
        $hash = $this->payWayService->getHash($hashString);

        return response()->json([
            'hash'                 => $hash,
            'tran_id'              => $tranId,
            'amount'               => $amount,
            'merchant_id'          => $merchant_id,
            'req_time'             => $req_time,
            'currency'             => $currency,
            'payment_option'       => $payment_option,
            'return_url'           => $return_url,
            'continue_success_url' => $continue_success_url,
        ]);
    }

    /**
     * Handle user redirect from ABA after payment attempt.
     */
    public function checkTransaction(Request $request)
    {
        Log::info('CHECK TRANSACTION HIT', [
            'query'        => $request->query(),
            'session_tran' => session('tran_id'),
        ]);

        $tranId  = $request->input('tran_id') ?? session('tran_id');
        $orderId = session('order_id') ?? $request->input('order_id');

        if (!$tranId) {
            return redirect()->route('dashboard')->with('error', 'Transaction ID missing.');
        }

        if ($this->checkAbaApproved($tranId)) {
            Log::info('PAYMENT CONFIRMED (Browser Redirect)', ['tran_id' => $tranId]);
            $this->finalizeOrder($orderId);

            // Clear the cart and all payment session data
            session()->forget(['cart', 'tran_id', 'order_id']);

            return redirect()->route('dashboard')
                ->with('success', 'Payment successful! Your order has been placed.');
        }

        return redirect()->route('dashboard')
            ->with('error', 'Payment failed or is still pending.');
    }

    /**
     * Handle ABA Server-to-Server IPN Pushback webhook.
     */
    public function pushback(Request $request)
    {
        Log::info('ABA PUSHBACK RECEIVED', $request->all());

        $tranId = $request->input('tran_id');

        if ($this->checkAbaApproved($tranId)) {
            if (preg_match('/ORD-(\d+)-/', $tranId, $matches)) {
                $orderId = $matches[1];
                $this->finalizeOrder($orderId);
                Log::info('ABA PUSHBACK SUCCESS', ['order_id' => $orderId]);
            }
            return response()->json(['status' => 0, 'message' => 'success']);
        }

        return response()->json(['status' => 1, 'message' => 'failed']);
    }

    private function finalizeOrder($orderId)
    {
        if (!$orderId) return;

        DB::transaction(function () use ($orderId) {
            /** @var \App\Models\Order|null $order */
            $order = Order::with('items')->lockForUpdate()->find($orderId);
            if ($order && $order->status !== 'Completed') {
                $order->update(['status' => 'Completed']);
                foreach ($order->items as $item) {
                    Product::where('id', $item->product_id)->decrement('stock', $item->quantity);
                }
            }
        });

        // Increment voucher used_count if one was applied
        $voucherId = session('voucher_id');
        if ($voucherId) {
            $voucher = Voucher::find($voucherId);
            if ($voucher) {
                $voucher->incrementUsage();
            }
            session()->forget(['voucher_id', 'voucher_code', 'voucher_discount']);
        }
    }

    public function checkAbaApproved(string $tranId): bool
    {
        $merchant_id = config('payway.merchant_id');
        $public_key  = config('payway.public_key');
        $req_time    = time();

        // IMPORTANT: No spaces between fields — this is the correct ABA signature format.
        $hash = base64_encode(hash_hmac(
            'sha512',
            $req_time . $merchant_id . $tranId,
            $public_key,
            true
        ));

        $url = config('payway.transaction_url');

        $response = Http::asMultipart()->post($url, [
            'merchant_id' => $merchant_id,
            'req_time'    => $req_time,
            'tran_id'     => $tranId,
            'hash'        => $hash,
        ]);

        Log::info('ABA check-transaction response', [
            'tran_id' => $tranId,
            'http_status' => $response->status(),
            'body'    => $response->body(),
        ]);

        if (!$response->successful()) {
            Log::warning('ABA check failed', ['tran_id' => $tranId, 'body' => $response->body()]);
            return false;
        }

        $data         = $response->json();
        $responseData = $data['data'] ?? $data;

        if (empty($responseData)) return false;

        $paymentStatus = $responseData['payment_status'] ?? null;
        $status        = $responseData['status'] ?? null;

        return strtoupper((string) $paymentStatus) === 'APPROVED'
            || strtoupper((string) $status) === 'SUCCESS'
            || strtoupper((string) $status) === 'APPROVED';
    }
}
