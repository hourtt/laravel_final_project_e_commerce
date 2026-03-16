<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Voucher;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    /**
     * Shared logic: resolve category + products, with optional search.
     */
    private function resolveProducts(Request $request): array
    {
        $category   = $request->query('category', 'All');
        $search     = trim($request->query('search', ''));
        
        // Cache category names for 1 hour to avoid repeated DB hits
        $categories = Cache::remember('category_names_list', 3600, function () {
            $names = Category::pluck('name')->toArray();
            array_unshift($names, 'All');
            return $names;
        });

        $query = Product::with('category');

        if ($search !== '') {
            $term = '%' . strtolower($search) . '%';
            $query->where('name', 'ILIKE', $term);
            $query->latest();
        } elseif ($category === 'All') {
            $query->latest();
        } else {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', $category);
            })->latest();
        }

        // Use pagination instead of take(50)
        $products = $query->paginate(20)->withQueryString();

        return compact('products', 'categories', 'category', 'search');
    }

    /**
     * Home page (public).
     */
    public function index(Request $request)
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $data = $this->resolveProducts($request);

        if ($request->ajax()) {
            return view('user.partials.product-grid', $data);
        }

        return view('user.dashboard', $data);
    }

    /**
     * Authenticated user dashboard.
     */
    public function dashboard(Request $request)
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $data = $this->resolveProducts($request);

        if ($request->ajax()) {
            return view('user.partials.product-grid', $data);
        }

        return view('user.dashboard', $data);
    }

    /**
     * AJAX endpoint — returns only the product grid HTML partial.
     * Called by the JS category filter to avoid full page reload.
     */
    public function filter(Request $request)
    {
        return view('user.partials.product-grid', $this->resolveProducts($request));
    }

    /**
     * AJAX search endpoint — returns the product grid partial
     * filtered by the ?search= query param (case-insensitive).
     */
    public function search(Request $request)
    {
        return view('user.partials.product-grid', $this->resolveProducts($request));
    }

    /**
     * Product Detail Page.
     */
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        
        // Resolve breadcrumbs or categories if needed for the navbar/sidebar
        $categories = Category::all();

        return view('user.product-show', compact('product', 'categories'));
    }


    /**
     * Add to cart (AJAX)
     */
    public function addToCart(Request $request)
    {
        $productId = $request->input('product_id');
        $quantityToAdd = (int) $request->input('quantity', 1);
        $product = Product::findOrFail($productId);
        
        $cart = session()->get('cart', []);

        $currentQty = isset($cart[$productId]) ? $cart[$productId]['quantity'] : 0;
        $newQty = $currentQty + $quantityToAdd;

        if ($newQty > $product->stock) {
            return response()->json([
                'success' => false,
                'message' => "Only {$product->stock} items available in stock for {$product->name}.",
            ]);
        }

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $newQty;
        } else {
            $cart[$productId] = [
                'quantity' => $quantityToAdd,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success'   => true,
            'cartCount' => count($cart),
        ]);
    }

    /**
     * Update cart quantity (AJAX)
     */
    public function updateCart(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = (int) $request->input('quantity');
        
        if ($quantity < 1) {
            $quantity = 1;
        }

        $product = Product::findOrFail($productId);
        
        if ($quantity > $product->stock) {
            return response()->json([
                'success' => false,
                'message' => "Only {$product->stock} items available in stock for {$product->name}.",
                'available_stock' => $product->stock,
            ], 400);
        }

        $cart = session()->get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session()->put('cart', $cart);
            // Keep the pending order in sync with the updated quantity
            $this->syncPendingOrder();
        }

        $cartData = session()->get('cart', []);
        $productIds = array_keys($cartData);
        $products = Product::whereIn('id', $productIds)->get();
        $subtotal = 0;
        $discountableSubtotal = 0;
        $rowSubtotal = 0;

        $voucherCode = session('voucher_code');
        $voucherProductId = null;
        $voucher = null;

        if ($voucherCode) {
            $voucher = Voucher::where('code', $voucherCode)->first();
            $voucherProductId = $voucher ? $voucher->product_id : null;
        }

        foreach ($products as $p) {
            $qty = $cartData[$p->id]['quantity'];
            $lineTotal = $p->price * $qty;
            $subtotal += $lineTotal;
            
            if ($voucher && ($voucherProductId === null || $voucherProductId === $p->id)) {
                $discountableSubtotal += $lineTotal;
            }

            if ($p->id == $productId) {
                $rowSubtotal = $lineTotal;
            }
        }

        $voucherDiscount = 0;
        if ($voucher) {
            $voucherDiscount = $voucher->calculateDiscount($discountableSubtotal);
            session(['voucher_discount' => $voucherDiscount]); // update session with fresh discount
        }

        $grandTotal = max(0, $subtotal - $voucherDiscount);

        return response()->json([
            'success' => true,
            'row_subtotal' => $rowSubtotal,
            'grand_total' => $grandTotal,
        ]);
    }

    /**
     * Remove single item from cart
     */
 public function removeFromCart(Request $request, $id)
    {
        $product = Product::find($id);
        $productName = $product?->name ?? 'Item';
        
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            // Keep the pending order in sync after item removal
            $this->syncPendingOrder();
        }
        
        return redirect()->route('checkout')->with('success', "{$productName} removed from cart.");
    }
    /**
     * User's own order history.
     */
    public function orders(Request $request)
    {
        $orders = auth()->user()
            ->orders()
            ->with('items.product')
            ->latest()
            ->paginate(15);

        return view('user.orders', compact('orders'));
    }

    /**
     * User's single order detail.
     */
    public function orderShow($id)
    {
        $order = auth()->user()
            ->orders()
            ->with('items.product')
            ->findOrFail($id);

        return view('user.orders-show', compact('order'));
    }

    /**
     * Sync the current user's Pending order (tracked via session 'order_id')
     * with the current cart session, so order history always reflects the
     * latest quantities and products — not stale data from a previous page load.
     *
     * Called from updateCart() and removeFromCart() so any cart change
     * is immediately reflected in the pending order.
     */
    private function syncPendingOrder(): void
    {
        $orderId = session('order_id');
        if (!$orderId) return;

        $order = Order::where('id', $orderId)
            ->where('user_id', auth()->id())
            ->where('status', 'Pending')
            ->first();

        if (!$order) return;

        $cart = session('cart', []);

        if (empty($cart)) {
            $order->delete();
            session()->forget('order_id');
            return;
        }

        $products = Product::whereIn('id', array_keys($cart))->get();
        $total = 0;
        $discountableSubtotal = 0;
        $cartProductIds = [];

        $voucherCode = session('voucher_code');
        $voucherProductId = null;
        $voucher = null;

        if ($voucherCode) {
            $voucher = Voucher::where('code', $voucherCode)->first();
            $voucherProductId = $voucher ? $voucher->product_id : null;
        }

        foreach ($products as $product) {
            $qty = $cart[$product->id]['quantity'] ?? 1;
            $lineTotal = $product->price * $qty;
            $total += $lineTotal;
            $cartProductIds[] = $product->id;

            if ($voucher && ($voucherProductId === null || $voucherProductId === $product->id)) {
                $discountableSubtotal += $lineTotal;
            }
        }

        $voucherDiscount = 0;
        if ($voucher) {
            $voucherDiscount = $voucher->calculateDiscount($discountableSubtotal);
            session(['voucher_discount' => $voucherDiscount]);
        }

        foreach ($products as $product) {
            $qty = $cart[$product->id]['quantity'] ?? 1;

            $itemVoucherCode = null;
            $itemVoucherDiscount = null;
            
            if ($voucherCode && ($voucherProductId === null || $voucherProductId === $product->id)) {
                 $itemVoucherCode = $voucherCode;
                 $itemVoucherDiscount = ($voucherProductId === $product->id) ? $voucherDiscount : null;
            }

            OrderItem::updateOrCreate(
                ['order_id' => $orderId, 'product_id' => $product->id],
                [
                    'product_name' => $product->name, 
                    'voucher_code' => $itemVoucherCode,
                    'voucher_discount' => $itemVoucherDiscount,
                    'quantity' => $qty, 
                    'price' => $product->price
                ]
            );
        }

        // Remove DB items that are no longer in the session cart
        OrderItem::where('order_id', $orderId)
            ->whereNotIn('product_id', $cartProductIds)
            ->delete();

        $discountedTotal = max(0, $total - $voucherDiscount);

        if ($discountedTotal <= 0) {
            $order->delete();
            session()->forget('order_id');
        } else {
            $order->update([
                'total_price' => $discountedTotal,
                'voucher_discount' => $voucherDiscount > 0 ? $voucherDiscount : null,
            ]);
        }
    }
}
