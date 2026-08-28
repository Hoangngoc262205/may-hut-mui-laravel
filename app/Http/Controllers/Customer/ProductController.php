<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()
            ->with(['brand', 'category', 'images', 'variants.size', 'variants.color', 'reviews']);

        if ($search = $request->query('q')) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('brand', fn ($brand) => $brand->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('category', fn ($category) => $category->where('name', 'like', "%{$search}%"));
            });
        }

        if ($category = $request->query('category')) {
            $query->where('category_id', $category);
        }

        if ($brand = $request->query('brand')) {
            $query->where('brand_id', $brand);
        }

        if ($size = $request->query('size')) {
            $query->whereHas('variants', fn ($variant) => $variant->where('size_id', $size));
        }

        if ($color = $request->query('color')) {
            $query->whereHas('variants', fn ($variant) => $variant->where('color_id', $color));
        }

        if ($request->filled('min_price')) {
            $query->whereHas('variants', fn ($variant) => $variant->where('price', '>=', (int) $request->query('min_price')));
        }

        if ($request->filled('max_price')) {
            $query->whereHas('variants', fn ($variant) => $variant->where('price', '<=', (int) $request->query('max_price')));
        }

        if ($request->query('stock') === 'in_stock') {
            $query->whereHas('variants', fn ($variant) => $variant->where('stock', '>', 0)->where('status', 'active'));
        }

        match ($request->query('sort')) {
            'price_asc' => $query->orderBy(ProductVariant::select('price')
                ->whereColumn('product_variants.product_id', 'products.id')
                ->where('product_variants.status', 'active')
                ->orderBy('price')
                ->limit(1)),
            'price_desc' => $query->orderByDesc(ProductVariant::select('price')
                ->whereColumn('product_variants.product_id', 'products.id')
                ->where('product_variants.status', 'active')
                ->orderByDesc('price')
                ->limit(1)),
            'best_selling' => $query->withCount('orderItems')->orderByDesc('order_items_count'),
            default => $query->latest(),
        };

        return view('products.index', [
            'products' => $query->paginate(9)->withQueryString(),
            'categories' => Category::active()->orderBy('name')->get(),
            'brands' => Brand::active()->orderBy('name')->get(),
            'sizes' => Size::active()->orderBy('name')->get(),
            'colors' => Color::active()->orderBy('name')->get(),
        ]);
    }

    public function show(string $slug)
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with([
                'brand',
                'category',
                'images',
                'detail',
                'variants.size',
                'variants.color',
                'reviews.user',
            ])
            ->firstOrFail();

        $variants = $product->variants
            ->where('status', 'active')
            ->values();

        $defaultVariant = $variants->firstWhere('stock', '>', 0) ?: $variants->first();
        $variantData = $variants->map(fn ($variant) => [
            'id' => $variant->id,
            'size_id' => $variant->size_id,
            'size_name' => $variant->size?->name,
            'color_id' => $variant->color_id,
            'color_name' => $variant->color?->name,
            'color_hex' => $variant->color?->hex_code,
            'sku' => $variant->sku,
            'price' => (int) $variant->price,
            'stock' => (int) $variant->stock,
            'image_url' => asset($variant->image ?: $product->primary_image_path),
        ]);

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['brand', 'images', 'variants', 'reviews'])
            ->latest()
            ->take(4)
            ->get();

        return view('products.show', [
            'product' => $product,
            'variants' => $variants,
            'variantData' => $variantData,
            'defaultVariant' => $defaultVariant,
            'sizes' => $variants->pluck('size')->filter()->unique('id')->values(),
            'colors' => $variants->pluck('color')->filter()->unique('id')->values(),
            'approvedReviews' => $product->reviews->where('status', 'approved'),
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
