<?php

use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ColorController as AdminColorController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FinanceController as AdminFinanceController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductVariantController as AdminProductVariantController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SizeController as AdminSizeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

use App\Http\Controllers\Customer\AuthController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\LocationController;
use App\Http\Controllers\Customer\MomoController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\ProductController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Customer\WishlistController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


// ==================== EMAIL VERIFICATION ====================

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('home')
        ->with('success', 'Email đã được xác thực thành công.');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->route('home');
    }

    $request->user()->sendEmailVerificationNotification();

    return back()->with(
        'success',
        'Đã gửi lại email xác thực. Vui lòng kiểm tra hộp thư.'
    );
})->middleware(['auth', 'throttle:6,1'])
  ->name('verification.send');


// ==================== MOMO CALLBACK & IPN ====================

Route::post('/payment/momo/ipn', [MomoController::class, 'ipn'])
    ->name('payment.momo.ipn');

Route::get('/payment/momo/callback', [MomoController::class, 'callback'])
    ->name('payment.momo.callback');


// ==================== CUSTOMER ====================

Route::middleware(['auth', 'verified'])->group(function () {

    // Cart
    Route::get('/cart', [CartController::class, 'index'])
        ->name('cart.index');

    Route::post('/cart', [CartController::class, 'store'])
        ->name('cart.store');

    Route::delete('/cart', [CartController::class, 'clear'])
        ->name('cart.clear');

    Route::patch('/cart/items/{cartItem}', [CartController::class, 'update'])
        ->name('cart.items.update');

    Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])
        ->name('cart.items.destroy');


    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])
        ->name('checkout.index');

    Route::post('/checkout', [CheckoutController::class, 'store'])
        ->name('checkout.store');


    // Locations / GHN
    Route::get('/locations/provinces', [LocationController::class, 'provinces'])
        ->name('locations.provinces');

    Route::get('/locations/districts/{provinceId}', [LocationController::class, 'districts'])
        ->name('locations.districts');

    Route::get('/locations/wards/{districtId}', [LocationController::class, 'wards'])
        ->name('locations.wards');

    Route::post('/locations/calculate-fee', [LocationController::class, 'calculateFee'])
        ->name('locations.calculate-fee');


    // Orders
    Route::get('/orders', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->name('orders.show');


    // MoMo Payment
    Route::get('/orders/{order}/start-momo', [MomoController::class, 'start'])
        ->name('orders.momo.start');

    Route::get('/orders/{order}/pay/momo', [MomoController::class, 'payAgain'])
        ->name('orders.momo.pay');


    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])
        ->name('wishlist.index');

    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])
        ->name('wishlist.toggle');

    Route::delete('/wishlist/items/{wishlistItem}', [WishlistController::class, 'destroy'])
        ->name('wishlist.destroy');


    // Reviews
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])
        ->name('reviews.store');
});


// ==================== ADMIN ====================

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('products', AdminProductController::class);

        Route::post('/products/{product}/images', [AdminProductController::class, 'storeImages'])
            ->name('products.images.store');

        Route::patch('/product-images/{image}/primary', [AdminProductController::class, 'setPrimaryImage'])
            ->name('product-images.primary');

        Route::delete('/product-images/{image}', [AdminProductController::class, 'destroyImage'])
            ->name('product-images.destroy');

        Route::resource('categories', AdminCategoryController::class)
            ->except('show');

        Route::resource('brands', AdminBrandController::class)
            ->except('show');

        Route::resource('sizes', AdminSizeController::class)
            ->except('show');

        Route::resource('colors', AdminColorController::class)
            ->except('show');

        Route::resource('product-variants', AdminProductVariantController::class)
            ->except('show');

        Route::patch('/product-variants/{product_variant}/quick-stock', [AdminProductVariantController::class, 'quickStock'])
            ->name('product-variants.quick-stock');


        // ==================== FINANCE - LAB 09 ====================

        Route::get('/finance', [AdminFinanceController::class, 'index'])
            ->name('finance.index');

        Route::get('/finance/transactions', [AdminFinanceController::class, 'transactions'])
            ->name('finance.transactions');

        Route::patch('/finance/{order}/status', [AdminFinanceController::class, 'updateStatus'])
            ->name('finance.update-status');


        // ==================== ADMIN ORDERS ====================

        Route::get('/orders', [AdminOrderController::class, 'index'])
            ->name('orders.index');

        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])
            ->name('orders.show');

        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
            ->name('orders.status');

        Route::post('/orders/{order}/ghn', [AdminOrderController::class, 'createGhnOrder'])
            ->name('orders.ghn.create');

        Route::delete('/orders/{order}/ghn', [AdminOrderController::class, 'cancelGhnOrder'])
            ->name('orders.ghn.cancel');


        // ==================== USERS ====================

        Route::get('/users', [AdminUserController::class, 'index'])
            ->name('users.index');

        Route::patch('/users/{user}', [AdminUserController::class, 'update'])
            ->name('users.update');


        // ==================== REVIEWS ====================

        Route::get('/reviews', [AdminReviewController::class, 'index'])
            ->name('reviews.index');

        Route::patch('/reviews/{review}', [AdminReviewController::class, 'update'])
            ->name('reviews.update');

        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])
            ->name('reviews.destroy');
    });