<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard.index', [
            'totalProducts' => Product::count(),
            'totalOrders' => Order::count(),
            'totalUsers' => User::where('role', 'customer')->count(),
            'totalRevenue' => Order::where('status', 'completed')->sum('total'),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'confirmedOrders' => Order::where('status', 'confirmed')->count(),
            'processingOrders' => Order::where('status', 'processing')->count(),
            'shippingOrders' => Order::where('status', 'shipping')->count(),
            'completedOrders' => Order::where('status', 'completed')->count(),
            'cancelledOrders' => Order::where('status', 'cancelled')->count(),
            'inStockProducts' => Product::whereHas('variants', fn ($query) => $query->where('stock', '>', 0))->count(),
            'lowStockProducts' => Product::whereHas('variants', fn ($query) => $query->whereBetween('stock', [1, 5]))->count(),
            'outOfStockProducts' => Product::whereDoesntHave('variants', fn ($query) => $query->where('stock', '>', 0))->count(),
            'totalReviews' => Review::count(),
            'recentOrders' => Order::with('user')->latest()->take(8)->get(),
            'lowStockVariants' => ProductVariant::with('product', 'size', 'color')->where('stock', '<=', 5)->orderBy('stock')->take(8)->get(),
            'featuredProducts' => Product::with('brand', 'images', 'variants')->latest()->take(6)->get(),
        ]);
    }
}
