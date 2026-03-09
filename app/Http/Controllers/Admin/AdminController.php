<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $totalProducts  = Product::count();
        $totalStock     = Product::sum('stock');
        $avgPrice       = Product::avg('price');
        $categories     = Category::count();
        $recentProducts = Product::with('category')->latest()->paginate(8)->withQueryString()->fragment('recent-products');
        $categoryStats  = Category::withCount('products')
                            ->withSum('products', 'stock')
                            ->get();

        return view("admin.dashboard", compact(
            'totalProducts',
            'totalStock',
            'avgPrice',
            'categories',
            'recentProducts',
            'categoryStats'
        ));
    }
}
