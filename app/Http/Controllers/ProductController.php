<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function index()
    {
        $categories = Category::with([
            'products' => function ($query) {
                $query->select('id', 'name', 'description', 'price', 'image', 'category_id', 'is_featured', 'stock');
            },
        ],)->latest()->get(['id', 'name']);

        $groupProducts = $categories->map(function ($category) {
            if ($category->products->isEmpty()) return null;

            return [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'category_description' => $category->description,
                'products' => $category->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $product->description,
                        'price' => $product->price,
                        'image' => asset('storage/' . $product->image),
                        'stock' => $product->stock,
                    ];
                }),
            ];
        })->filter()->values();

        $filteredProducts = Product::where('is_featured', true)->limit(10)->get(['id', 'name', 'image', 'description', 'price']);

        $lstFeatured = $filteredProducts->map(fn($product) => [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'image' => $product->image ? asset('storage/' . $product->image) : null,
        ]);

        return response()->json([
            'categories' => $groupProducts,
            'featured_products' => $lstFeatured,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'is_featured' => 'required|boolean',
            'stock' => 'required|integer',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = Storage::disk('public')->putFile('products', $image);
            $request->image = $path;
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $request->image,
            'category_id' => $request->category_id,
            'is_featured' => $request->is_featured,
            'stock' => $request->stock,
        ]);

        return response()->json($product);
    }
}
