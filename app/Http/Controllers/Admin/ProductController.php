<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $categoryId = $request->input('category_id', 'all');

        $query = Product::with('category');

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryId !== 'all' && $categoryId !== '') {
            $query->where('category_id', $categoryId);
        }

        // Handle sorting - keeping the original default for now or using latest()
        $products = $query->latest('id')->paginate(5)->fragment('products')->withQueryString();
        $categories = Category::all();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.products.product_table', compact('products'))->render(),
                'pagination' => (string) $products->links(),
                'total' => $products->total(),
            ]);
        }

        return view('admin.products.index', compact('products', 'categories', 'search', 'categoryId'));
    }
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'required|string',
            'price'         => 'required|numeric|min:0',
            'category_id'   => 'required|exists:categories,id',
            'stock'         => 'required|integer|min:0',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($request->hasFile('product_image')) {
            $path = $request->file('product_image')->store('products', 'public');
            $validated['product_image'] = $path;
        } else {
            $validated['product_image'] = null;
        }

        Product::create($validated);
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'required|string',
            'price'         => 'required|numeric|min:0',
            'category_id'   => 'required|exists:categories,id',
            'stock'         => 'required|integer|min:0',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($request->hasFile('product_image')) {
            // New file uploaded — swap it out
            if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
                Storage::disk('public')->delete($product->product_image);
            }
            $path = $request->file('product_image')->store('products', 'public');
            $validated['product_image'] = $path;
        } elseif ($request->input('remove_image') === '1') {
            // Admin clicked Remove without uploading a replacement
            if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
                Storage::disk('public')->delete($product->product_image);
            }
            $validated['product_image'] = null;
        } else {
            // No change — keep the existing image path
            unset($validated['product_image']);
        }

        $product->update($validated);
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Delete image from storage when product is deleted
        if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
            Storage::disk('public')->delete($product->product_image);
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
