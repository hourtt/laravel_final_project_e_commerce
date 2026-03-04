<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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
        $cartData = [];
        $total = 0;
        $hash = $tranId = $amount = $merchant_id = $req_time = $currency = $payment_option = $return_url = $continue_success_url = '';

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
                    // This ensures removed / swapped products don't linger in
                    // order history when the user changes their cart and revisits checkout.
                    DB::beginTransaction();

                    $cartProductIds = $products->pluck('id')->toArray();

                    // Delete items that are no longer in the cart
                    OrderItem::where('order_id', $order->id)
                        ->whereNotIn('product_id', $cartProductIds)
                        ->delete();

                    // Add / update items that ARE in the cart
                    foreach ($products as $product) {
                        $qty = $cart[$product->id]['quantity'];
                        OrderItem::updateOrCreate(
                            ['order_id' => $order->id, 'product_id' => $product->id],
                            ['quantity' => $qty, 'price' => $product->price]
                        );
                    }

                    // Persist the recalculated total
                    $order->update(['total_price' => $total]);

                    DB::commit();

                } else {
                    // ── No valid pending order — create a fresh one ──
                    DB::beginTransaction();
                    $order = Order::create([
                        'user_id'     => Auth::id(),
                        'total_price' => $total,
                        'status'      => 'Pending',
                    ]);
                    foreach ($products as $product) {
                        $qty = $cart[$product->id]['quantity'];
                        OrderItem::create([
                            'order_id'   => $order->id,
                            'product_id' => $product->id,
                            'quantity'   => $qty,
                            'price'      => $product->price,
                        ]);
                    }
                    DB::commit();
                    session(['order_id' => $order->id]);
                }

                // GENERATE ABA PARAMS
                $merchant_id          = config('payway.merchant_id');
                $req_time             = time();
                $tranId               = 'ORD-' . $order->id . '-' . $req_time;
                $amount               = number_format($total, 2, '.', '');
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
            'total',
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

        $orderId = session('order_id');
        $order   = $orderId ? Order::find($orderId) : null;

        if (!$order || $order->status !== 'Pending' || $order->user_id !== Auth::id()) {
            return response()->json(['error' => 'No valid pending order found. Please reload the page.'], 422);
        }

        // Update the order's stored total in case quantities changed
       DB::transaction(function () use ($order, $products, $cart, $total) {

        // Keep track of product IDs currently in cart
        $cartProductIds = [];

        foreach ($products as $product) {
            $qty = $cart[$product->id]['quantity'];
            $cartProductIds[] = $product->id;

            // Update existing item or create if not exists
            OrderItem::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                ],
                [
                    'quantity' => $qty,
                    'price' => $product->price,
                ]
            );
        }

        // Remove items that were deleted from cart
        OrderItem::where('order_id', $order->id)
        ->whereNotIn('product_id', $cartProductIds)
        ->delete();

        // Update order total
        $order->update([
        'total_price' => $total
        ]);
    });
        $merchant_id          = config('payway.merchant_id');
        $req_time             = time();
        $tranId               = 'ORD-' . $order->id . '-' . $req_time;
        $amount               = number_format($total, 2, '.', '');
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
