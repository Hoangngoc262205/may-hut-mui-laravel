<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartAndCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'customer',
        ]);

        $category = Category::create([
            'name' => 'Máy hút mùi âm tủ',
            'slug' => 'may-hut-mui-am-tu',
            'status' => 'active',
        ]);

        $brand = Brand::create([
            'name' => 'Kocher',
            'slug' => 'kocher',
            'status' => 'active',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Máy hút mùi Kocher Test',
            'slug' => 'may-hut-mui-kocher-test',
            'status' => 'active',
        ]);

        $size = Size::create(['name' => '70 cm', 'status' => 'active']);
        $color = Color::create(['name' => 'Inox', 'hex_code' => '#cccccc', 'status' => 'active']);

        $this->variant = ProductVariant::create([
            'product_id' => $product->id,
            'size_id' => $size->id,
            'color_id' => $color->id,
            'sku' => 'TEST-SKU-001',
            'price' => 5000000,
            'stock' => 10,
            'status' => 'active',
        ]);
    }

    public function test_cart_service_can_add_item_and_detect_stock(): void
    {
        $cartService = app(CartService::class);

        $result = $cartService->addToCart($this->user->id, $this->variant->id, 2);
        $this->assertTrue($result['success']);

        $cart = $cartService->getCart($this->user->id);
        $this->assertCount(1, $cart->items);
        $this->assertEquals(2, $cart->items->first()->quantity);

        // Thêm vượt quá tồn kho (kho còn 10, đã có 2, thêm 9 -> lỗi)
        $overflowResult = $cartService->addToCart($this->user->id, $this->variant->id, 9);
        $this->assertFalse($overflowResult['success']);
    }

    public function test_checkout_service_creates_order_and_decrements_stock(): void
    {
        $cartService = app(CartService::class);
        $checkoutService = app(CheckoutService::class);

        $cartService->addToCart($this->user->id, $this->variant->id, 3);
        $cart = $cartService->getCart($this->user->id);

        $order = $checkoutService->placeOrder($cart, [
            'customer_name' => 'Nguyễn Văn Test',
            'phone' => '0987654321',
            'email' => 'test@example.com',
            'address' => '123 Đường Test',
            'province_id' => '201',
            'province_name' => 'Hà Nội',
            'district_id' => '1450',
            'district_name' => 'Quận Hoàng Mai',
            'ward_code' => '21011',
            'ward_name' => 'Phường Định Công',
            'note' => 'Giao giờ hành chính',
            'payment_method' => 'cod',
        ]);

        $this->assertNotNull($order->id);
        $this->assertEquals(OrderStatus::PENDING->value, $order->status);
        $this->assertEquals(7, $this->variant->fresh()->stock); // 10 - 3 = 7

        // Giỏ hàng phải được dọn sạch
        $freshCart = $cartService->getCart($this->user->id);
        $this->assertCount(0, $freshCart->items);
    }
}
