<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $with = ['brand', 'category', 'images', 'variants', 'reviews'];

        return view('home.index', [
            'categories' => Category::active()->withCount('products')->orderByDesc('products_count')->take(6)->get(),
            'brands' => Brand::active()->withCount('products')->orderByDesc('products_count')->take(12)->get(),
            'featuredProducts' => Product::active()->with($with)->latest()->take(8)->get(),
            'bestSellingProducts' => Product::active()->with($with)->withCount('orderItems')->orderByDesc('order_items_count')->latest()->take(8)->get(),
            'newProducts' => Product::active()->with($with)->latest()->take(8)->get(),
        ]);
    }
}
