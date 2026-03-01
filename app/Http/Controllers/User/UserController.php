<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Shared logic: resolve category + products.
     */
    private function resolveProducts(Request $request): array
    {
        $category   = $request->query('category', 'All');
        $categories = Category::pluck('name')->toArray();
        array_unshift($categories, 'All');

        $query = Product::with('category')
            ->select('products.*')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id');

        if($category === "All"){
            // Random products for the 'All' tab
            $query->inRandomOrder();
        } else {
            // Specific category: show newest first
            $query->where('categories.name', $category)
                  ->orderBy('products.id', 'desc');
        }
        $products = $query->get();

        return compact('products', 'categories', 'category');
    }

    /**
     * Home page (public).
     */
    public function index(Request $request)
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('user.dashboard', $this->resolveProducts($request));
    }

    /**
     * Authenticated user dashboard.
     */
    public function dashboard(Request $request)
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('user.dashboard', $this->resolveProducts($request));
    }

    /**
     * AJAX endpoint — returns only the product grid HTML partial.
     * Called by the JS category filter to avoid full page reload.
     */
    public function filter(Request $request)
    {
        [
            'products'   => $products,
            'category'   => $category,
            'categories' => $categories,
        ] = $this->resolveProducts($request);

        return view('user.partials.product-grid', compact('products', 'category', 'categories'));
    }



    /**
     * Add to cart (AJAX)
     */
    public function addToCart(Request $request)
    {
        $productId = $request->input('product_id');
        $product = Product::findOrFail($productId);
        
        $cart = session()->get('cart', []);

        $currentQty = isset($cart[$productId]) ? $cart[$productId]['quantity'] : 0;
        $newQty = $currentQty + 1;

        if ($newQty > $product->stock) {
            return response()->json([
                'success' => false,
                'message' => "Only {$product->stock} items available in stock for {$product->name}.",
            ]);
        }

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'quantity' => 1,
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
        }

        // Recalculate totals
        $cartData = session()->get('cart', []);
        $productIds = array_keys($cartData);
        $products = Product::whereIn('id', $productIds)->get();
        $grandTotal = 0;
        $rowSubtotal = 0;

        foreach ($products as $p) {
            $qty = $cartData[$p->id]['quantity'];
            $lineTotal = $p->price * $qty;
            $grandTotal += $lineTotal;
            
            if ($p->id == $productId) {
                $rowSubtotal = $lineTotal;
            }
        }

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
        }
        
        return redirect()->route('checkout')->with('success', "{$productName} removed from cart.");
    }



}
