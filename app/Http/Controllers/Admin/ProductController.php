<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category', 'brand', 'images', 'variants')
            ->when($request->query('q'), fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product(),
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'sizes' => Size::orderBy('name')->get(),
            'colors' => Color::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);

        $product = Product::create($data);
        $product->detail()->create($this->detailData($request));

        // 1. Upload initial images if provided
        if ($request->hasFile('images')) {
            $request->validate([
                'images.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            ], [
                'images.*.image' => 'File tải lên phải là hình ảnh hợp lệ.',
            ]);

            foreach ($request->file('images', []) as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image' => 'storage/'.$path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index + 1,
                ]);
            }
        }

        // 2. Create initial variant if price and options are provided
        if ($request->filled('initial_price') && $request->filled('size_id') && $request->filled('color_id')) {
            $sku = $request->filled('sku')
                ? trim($request->input('sku'))
                : 'MH-' . strtoupper(Str::random(6));

            $product->variants()->create([
                'size_id' => $request->input('size_id'),
                'color_id' => $request->input('color_id'),
                'sku' => $sku,
                'price' => (int) $request->input('initial_price'),
                'stock' => (int) $request->input('initial_stock', 10),
                'status' => 'active',
            ]);
        }

        return redirect()->route('admin.products.show', $product)->with('success', 'Đã tạo sản phẩm thành công kèm phân loại và tồn kho.');
    }

    public function show(Product $product)
    {
        $product->load('category', 'brand', 'images', 'detail', 'variants.size', 'variants.color', 'reviews.user');

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load('detail', 'images', 'variants');

        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'sizes' => Size::orderBy('name')->get(),
            'colors' => Color::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request, $product);

        if ($product->name !== $data['name']) {
            $data['slug'] = $this->uniqueSlug($data['name'], $product->id);
        }

        $product->update($data);
        $product->detail()->updateOrCreate([], $this->detailData($request));

        return redirect()->route('admin.products.show', $product)->with('success', 'Đã cập nhật sản phẩm.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm.');
    }

    public function storeImages(Request $request, Product $product)
    {
        $request->validate([
            'images' => ['required', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ], [
            'images.required' => 'Vui lòng chọn ít nhất một ảnh.',
            'images.*.image' => 'File tải lên phải là ảnh.',
        ]);

        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        foreach ($request->file('images', []) as $index => $image) {
            $path = $image->store('products', 'public');

            $product->images()->create([
                'image' => 'storage/'.$path,
                'is_primary' => ! $hasPrimary && $index === 0,
                'sort_order' => $product->images()->count() + $index + 1,
            ]);
        }

        return back()->with('success', 'Đã upload ảnh sản phẩm.');
    }

    public function setPrimaryImage(ProductImage $image)
    {
        $image->product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Đã đặt ảnh chính.');
    }

    public function destroyImage(ProductImage $image)
    {
        if (str_starts_with($image->image, 'storage/')) {
            Storage::disk('public')->delete(str_replace('storage/', '', $image->image));
        }

        $image->delete();

        return back()->with('success', 'Đã xóa ảnh sản phẩm.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['required', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:180', Rule::unique('products', 'name')->ignore($product?->id)],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'brand_id.required' => 'Vui lòng chọn thương hiệu.',
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'name.unique' => 'Tên sản phẩm đã tồn tại.',
        ]);
    }

    private function detailData(Request $request): array
    {
        return $request->validate([
            'suction_capacity' => ['nullable', 'string', 'max:120'],
            'noise_level' => ['nullable', 'string', 'max:120'],
            'speed_levels' => ['nullable', 'string', 'max:120'],
            'voltage' => ['nullable', 'string', 'max:120'],
            'material' => ['nullable', 'string', 'max:120'],
            'hood_type' => ['nullable', 'string', 'max:120'],
            'dimensions' => ['nullable', 'string', 'max:120'],
            'warranty' => ['nullable', 'string', 'max:120'],
        ]);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: Str::random(8);
        $slug = $base;
        $i = 2;

        while (Product::where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
