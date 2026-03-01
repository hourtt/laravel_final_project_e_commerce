<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;

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
        $categories     = \App\Models\Category::count();
        $recentProducts = Product::with('category')->latest()->take(8)->get();
        $categoryStats  = \App\Models\Category::withCount('products')
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
