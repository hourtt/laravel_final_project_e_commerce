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

    // Show cart, prepare ABA hash, and create a pending order only if none exists.
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);

        // Initialize variables to avoid "undefined variable" errors in the view
        $cartData        = [];
        $total           = 0;
        $voucherDiscount = 0.0;
        $voucherCode     = null;
        $discountedTotal = 0.0;
        $appliedVouchers = [];
        $cartProductIds  = [];   // passed to view so checkout-script can send it via AJAX
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

            // Apply session vouchers discount
            $appliedVouchers = session()->get('applied_vouchers', []);
            $voucherDiscount = 0.0;
            foreach ($appliedVouchers as $pId => $data) {
                // Ensure the voucher is still valid for the product in cart
                if (isset($cart[$pId])) {
                     $voucherDiscount += (float) $data['discount'];
                }
            }
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

            // Build product IDs for voucher AJAX, used in checkout-script outside scope.
            $cartProductIds = $products->pluck('id')->map(fn($id) => (int) $id)->toArray();

            try {
               // Reuse existing pending order to prevent duplicates on refresh.
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
                    // Sync existing pending order with the current cart
                    DB::beginTransaction();

                    $cartProductIds = $products->pluck('id')->toArray();

                    // Delete items that are no longer in the cart
                    OrderItem::where('order_id', $order->id)
                        ->whereNotIn('product_id', $cartProductIds)
                        ->delete();

                    // Add / update items that ARE in the cart
                    foreach ($products as $product) {
                        $qty = $cart[$product->id]['quantity'];
                        
                        $itemVoucherCode = null;
                        $itemVoucherDiscount = null;
                        
                        if (isset($appliedVouchers[$product->id])) {
                             $itemVoucherCode = $appliedVouchers[$product->id]['code'];
                             $itemVoucherDiscount = $appliedVouchers[$product->id]['discount'];
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

                    // Persist total + voucher info (summary)
                    $voucherCodesStr = implode(', ', array_column($appliedVouchers, 'code'));
                    $order->update([
                        'total_price'      => $discountedTotal,
                        'voucher_code'     => $voucherCodesStr ?: null,
                        'voucher_discount' => $voucherDiscount > 0 ? $voucherDiscount : null,
                    ]);

                    DB::commit();

                } else {
                    // No valid pending order — create a fresh one
                    DB::beginTransaction();
                    $voucherCodesStr = implode(', ', array_column($appliedVouchers, 'code'));
                    $order = Order::create([
                        'user_id'          => Auth::id(),
                        'total_price'      => $discountedTotal,
                        'status'           => 'Pending',
                        'voucher_code'     => $voucherCodesStr ?: null,
                        'voucher_discount' => $voucherDiscount > 0 ? $voucherDiscount : null,
                    ]);

                    foreach ($products as $product) {
                        $qty = $cart[$product->id]['quantity'];
                        
                        $itemVoucherCode = null;
                        $itemVoucherDiscount = null;
                        
                        if (isset($appliedVouchers[$product->id])) {
                             $itemVoucherCode = $appliedVouchers[$product->id]['code'];
                             $itemVoucherDiscount = $appliedVouchers[$product->id]['discount'];
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

        $addresses = Auth::user()->addresses()->orderByDesc('is_default')->get();

        return view('user.checkout', compact(
            'cartData',
            'cartProductIds',
            'total',
            'voucherDiscount',
            'appliedVouchers',
            'discountedTotal',
            'hash',
            'tranId',
            'amount',
            'merchant_id',
            'req_time',
            'currency',
            'payment_option',
            'return_url',
            'continue_success_url',
            'addresses'
        ));
    }

   // AJAX: Recalculate cart and return updated ABA params before submit.

    public function preparePayment(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty.'], 422);
        }

        $productIds = array_keys($cart);
        $products   = Product::whereIn('id', $productIds)->get();

        // Recalculate subtotal
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

        // Apply voucher discount
        $appliedVouchers = session('applied_vouchers', []);
        $voucherDiscount = 0.0;
        foreach ($appliedVouchers as $pId => $vData) {
            if (isset($cart[$pId])) {
                $voucherDiscount += (float) $vData['discount'];
            }
        }
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
            // Resolve order
            // Get or create a valid pending order to avoid empty or invalid data.
            $orderId = session('order_id');
            $order   = $orderId ? Order::find($orderId) : null;

            DB::transaction(function () use (
                &$order, $products, $cart, $discountedTotal, $voucherDiscount, $request
            ) {
                // Resolve session voucher fields inside closure
                $voucherCode = session('voucher_code');
                $addressId = $request->input('address_id');

                if ($order && $order->status === 'Pending' && $order->user_id === Auth::id()) {
                    // Sync existing pending order
                    $cartProductIds = $products->pluck('id')->toArray();

                    // Remove items that are no longer in the cart
                    OrderItem::where('order_id', $order->id)
                        ->whereNotIn('product_id', $cartProductIds)
                        ->delete();

                    // Upsert current cart items
                    $appliedVouchers = session('applied_vouchers', []);
                    foreach ($products as $product) {
                        $qty = $cart[$product->id]['quantity'];
                        
                        $itemVoucherCode = null;
                        $itemVoucherDiscount = null;
                        
                        if (isset($appliedVouchers[$product->id])) {
                             $itemVoucherCode = $appliedVouchers[$product->id]['code'];
                             $itemVoucherDiscount = $appliedVouchers[$product->id]['discount'];
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

                    $voucherCodesStr = implode(', ', array_column($appliedVouchers, 'code'));
                    
                    $updates = [
                        'total_price'      => $discountedTotal,
                        'voucher_code'     => $voucherCodesStr ?: null,
                        'voucher_discount' => $voucherDiscount > 0 ? $voucherDiscount : null,
                    ];

                    // Capture shipping address snapshot if provided
                    if ($addressId) {
                        $address = \App\Models\Address::find($addressId);
                        if ($address && $address->user_id === Auth::id()) {
                            $updates['address_id'] = $address->id;
                            $updates['shipping_full_name'] = $address->full_name;
                            $updates['shipping_phone_number'] = $address->phone_number;
                            $updates['shipping_street_address'] = $address->street_address;
                            $updates['shipping_city'] = $address->city;
                            $updates['shipping_state'] = $address->state;
                            $updates['shipping_postal_code'] = $address->postal_code;
                            $updates['shipping_country'] = $address->country;
                        }
                    }

                    $order->update($updates);

                } else {
                    // No valid order in session — create a fresh one
                    // This handles: page loaded without cart, session expired,
                    // or stale order_id pointing to a non-Pending / foreign order.
                    $appliedVouchers = session('applied_vouchers', []);
                    $voucherCodesStr = implode(', ', array_column($appliedVouchers, 'code'));
                    
                    $orderData = [
                        'user_id'          => Auth::id(),
                        'total_price'      => $discountedTotal,
                        'status'           => 'Pending',
                        'voucher_code'     => $voucherCodesStr ?: null,
                        'voucher_discount' => $voucherDiscount > 0 ? $voucherDiscount : null,
                    ];

                    // Capture shipping address snapshot if provided
                    if ($addressId) {
                        $address = \App\Models\Address::find($addressId);
                        if ($address && $address->user_id === Auth::id()) {
                            $orderData['address_id'] = $address->id;
                            $orderData['shipping_full_name'] = $address->full_name;
                            $orderData['shipping_phone_number'] = $address->phone_number;
                            $orderData['shipping_street_address'] = $address->street_address;
                            $orderData['shipping_city'] = $address->city;
                            $orderData['shipping_state'] = $address->state;
                            $orderData['shipping_postal_code'] = $address->postal_code;
                            $orderData['shipping_country'] = $address->country;
                        }
                    }

                    $order = Order::create($orderData);

                    foreach ($products as $product) {
                        $qty = $cart[$product->id]['quantity'];
                        
                        $itemVoucherCode = null;
                        $itemVoucherDiscount = null;
                        
                        if (isset($appliedVouchers[$product->id])) {
                             $itemVoucherCode = $appliedVouchers[$product->id]['code'];
                             $itemVoucherDiscount = $appliedVouchers[$product->id]['discount'];
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

        // Generate fresh ABA payment parameters
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

    // Handle user redirect from ABA after payment attempt.
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

    // Handle ABA Server-to-Server IPN Pushback webhook.
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

        // Increment voucher used_count for all applied vouchers
        $appliedVouchers = session('applied_vouchers', []);
        if (!empty($appliedVouchers)) {
            foreach ($appliedVouchers as $data) {
                $voucher = Voucher::find($data['id']);
                if ($voucher) {
                    $voucher->incrementUsage();
                }
            }
            session()->forget('applied_vouchers');
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
