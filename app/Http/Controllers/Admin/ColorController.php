<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::withCount('variants')->latest()->paginate(10);

        return view('admin.colors.index', compact('colors'));
    }

    public function create()
    {
        return view('admin.colors.form', ['color' => new Color()]);
    }

    public function store(Request $request)
    {
        Color::create($this->validated($request));

        return redirect()->route('admin.colors.index')->with('success', 'Đã tạo màu sắc.');
    }

    public function edit(Color $color)
    {
        return view('admin.colors.form', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        $color->update($this->validated($request, $color));

        return redirect()->route('admin.colors.index')->with('success', 'Đã cập nhật màu sắc.');
    }

    public function destroy(Color $color)
    {
        if ($color->variants()->exists()) {
            return back()->with('error', 'Không thể xóa màu đang dùng cho variant.');
        }

        $color->delete();

        return back()->with('success', 'Đã xóa màu sắc.');
    }

    private function validated(Request $request, ?Color $color = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('colors', 'name')->ignore($color?->id)],
            'hex_code' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'name.required' => 'Vui lòng nhập tên màu.',
            'name.unique' => 'Màu sắc đã tồn tại.',
            'hex_code.regex' => 'Mã màu phải có dạng #RRGGBB.',
        ]);
    }
}
