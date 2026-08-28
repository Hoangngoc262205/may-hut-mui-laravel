<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = Wishlist::firstOrCreate(['user_id' => Auth::id()])
            ->load('items.product.brand', 'items.product.images', 'items.product.variants', 'items.product.reviews');

        return view('wishlist.index', compact('wishlist'));
    }

    public function toggle(Product $product)
    {
        $wishlist = Wishlist::firstOrCreate(['user_id' => Auth::id()]);
        $item = $wishlist->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->delete();

            return back()->with('success', 'Đã bỏ sản phẩm khỏi danh sách yêu thích.');
        }

        $wishlist->items()->create(['product_id' => $product->id]);

        return back()->with('success', 'Đã thêm sản phẩm vào danh sách yêu thích.');
    }

    public function destroy(WishlistItem $wishlistItem)
    {
        abort_unless($wishlistItem->wishlist->user_id === Auth::id(), 403);
        $wishlistItem->delete();

        return back()->with('success', 'Đã xóa sản phẩm yêu thích.');
    }
}
