<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = Size::withCount('variants')->latest()->paginate(10);

        return view('admin.sizes.index', compact('sizes'));
    }

    public function create()
    {
        return view('admin.sizes.form', ['size' => new Size()]);
    }

    public function store(Request $request)
    {
        Size::create($this->validated($request));

        return redirect()->route('admin.sizes.index')->with('success', 'Đã tạo kích thước.');
    }

    public function edit(Size $size)
    {
        return view('admin.sizes.form', compact('size'));
    }

    public function update(Request $request, Size $size)
    {
        $size->update($this->validated($request, $size));

        return redirect()->route('admin.sizes.index')->with('success', 'Đã cập nhật kích thước.');
    }

    public function destroy(Size $size)
    {
        if ($size->variants()->exists()) {
            return back()->with('error', 'Không thể xóa kích thước đang dùng cho variant.');
        }

        $size->delete();

        return back()->with('success', 'Đã xóa kích thước.');
    }

    private function validated(Request $request, ?Size $size = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('sizes', 'name')->ignore($size?->id)],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'name.required' => 'Vui lòng nhập kích thước.',
            'name.unique' => 'Kích thước đã tồn tại.',
        ]);
    }
}
