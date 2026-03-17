<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
class AdminController extends Controller
{
    
    //Display the admin dashboard.
    public function index()
    {
        $totalProducts  = Product::count();
        $totalStock     = Product::sum('stock');
        $avgPrice       = Product::avg('price');
        $categories     = Category::count();
        //* Fragment() method enhances user experience by preventing page jumps, commonly used with pagination
        $recentProducts = Product::with('category')->orderBy('stock', 'desc')->paginate(5)->withQueryString()->fragment('recent-products');
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
