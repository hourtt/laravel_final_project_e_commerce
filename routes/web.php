<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController; // Admin dashboard controller
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\User\VoucherController as UserVoucherController;
use App\Http\Controllers\User\AddressController;

// Public home
Route::get('/', [UserController::class, 'index'])->name('home');

// AJAX: products filter (no auth required so guests can filter too)
Route::get('/products/filter', [UserController::class, 'filter'])->name('products.filter');
// AJAX: products search (case-insensitive, no auth required)
Route::get('/products/search', [UserController::class, 'search'])->name('products.search');
// Product Show page (Detail view)
Route::get('/products/{id}', [UserController::class, 'show'])->name('products.show');

//* User routes (logged in)
Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/checkout', [PaymentController::class, 'index'])->name('checkout');
    Route::post('/cart/add', [UserController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update', [UserController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/{id}', [UserController::class, 'removeFromCart'])->name('cart.remove');

    // ABA Payment Routes (Redirect flow)
    Route::get('/payment/check', [PaymentController::class, 'checkTransaction'])->name('payment.check');
    // Regenerate ABA hash with current cart total just before form submit
    Route::post('/payment/prepare', [PaymentController::class, 'preparePayment'])->name('payment.prepare');

    // User order history
    Route::get('/orders', [UserController::class, 'orders'])->name('user.orders');
    Route::get('/orders/{id}', [UserController::class, 'orderShow'])->name('user.orders.show');

    // Voucher (AJAX)
    Route::post('/voucher/apply', [UserVoucherController::class, 'apply'])->name('voucher.apply');
    Route::post('/voucher/remove', [UserVoucherController::class, 'remove'])->name('voucher.remove');
});

// Since Pushback is a Webhook sent by ABA servers, it doesn't need 'auth' middleware
Route::post('/payment/pushback', [PaymentController::class, 'pushback'])->name('payment.pushback');

//* Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('users', AdminUserController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('vouchers', AdminVoucherController::class);
    Route::get('/vouchers-generate-code', [AdminVoucherController::class, 'generateCode'])->name('vouchers.generate-code');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/image', [ProfileController::class, 'updateImage'])->name('profile.image.update');
    Route::delete('/profile/image', [ProfileController::class, 'destroyImage'])->name('profile.image.destroy');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Addresses
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::patch('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/addresses/{address}/default', [AddressController::class, 'setAsDefault'])->name('addresses.default');
});

require __DIR__ . '/auth.php';
