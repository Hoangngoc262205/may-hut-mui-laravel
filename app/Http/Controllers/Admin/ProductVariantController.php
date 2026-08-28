<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    public function index(Request $request)
    {
        $variants = ProductVariant::with('product', 'size', 'color')
            ->when($request->query('product_id'), fn ($query, $productId) => $query->where('product_id', $productId))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.variants.index', [
            'variants' => $variants,
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.variants.form', [
            'variant' => new ProductVariant(['product_id' => $request->query('product_id')]),
            'products' => Product::orderBy('name')->get(),
            'sizes' => Size::orderBy('name')->get(),
            'colors' => Color::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $this->ensureUniqueCombination($data);

        if ($request->hasFile('image')) {
            $data['image'] = 'storage/'.$request->file('image')->store('products/variants', 'public');
        }

        ProductVariant::create($data);

        return redirect()->route('admin.product-variants.index', ['product_id' => $data['product_id']])->with('success', 'Đã tạo variant.');
    }

    public function edit(ProductVariant $productVariant)
    {
        return view('admin.variants.form', [
            'variant' => $productVariant,
            'products' => Product::orderBy('name')->get(),
            'sizes' => Size::orderBy('name')->get(),
            'colors' => Color::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ProductVariant $productVariant)
    {
        $data = $this->validated($request, $productVariant);
        $this->ensureUniqueCombination($data, $productVariant->id);

        if ($request->hasFile('image')) {
            $this->deleteStoredImage($productVariant->image);
            $data['image'] = 'storage/'.$request->file('image')->store('products/variants', 'public');
        }

        $productVariant->update($data);

        return redirect()->route('admin.product-variants.index', ['product_id' => $productVariant->product_id])->with('success', 'Đã cập nhật variant.');
    }

    public function destroy(ProductVariant $productVariant)
    {
        $productId = $productVariant->product_id;
        $this->deleteStoredImage($productVariant->image);
        $productVariant->delete();

        return redirect()->route('admin.product-variants.index', ['product_id' => $productId])->with('success', 'Đã xóa variant.');
    }

    private function validated(Request $request, ?ProductVariant $variant = null): array
    {
        return $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'size_id' => ['required', 'exists:sizes,id'],
            'color_id' => ['required', 'exists:colors,id'],
            'sku' => ['required', 'string', 'max:80', Rule::unique('product_variants', 'sku')->ignore($variant?->id)],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'size_id.required' => 'Vui lòng chọn kích thước.',
            'color_id.required' => 'Vui lòng chọn màu sắc.',
            'sku.required' => 'Vui lòng nhập SKU.',
            'sku.unique' => 'SKU đã tồn tại.',
            'price.min' => 'Giá không được âm.',
            'stock.min' => 'Tồn kho không được âm.',
        ]);
    }

    private function ensureUniqueCombination(array $data, ?int $ignoreId = null): void
    {
        $exists = ProductVariant::where('product_id', $data['product_id'])
            ->where('size_id', $data['size_id'])
            ->where('color_id', $data['color_id'])
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();

        if ($exists) {
            back()->withInput()->with('error', 'Variant với sản phẩm, kích thước và màu này đã tồn tại.')->throwResponse();
        }
    }

    private function deleteStoredImage(?string $path): void
    {
        if ($path && str_starts_with($path, 'storage/')) {
            Storage::disk('public')->delete(str_replace('storage/', '', $path));
        }
    }
}
