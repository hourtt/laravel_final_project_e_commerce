<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;

// Public home
Route::get('/', [UserController::class, 'index'])->name('home');

// AJAX: products filter (no auth required so guests can filter too)
Route::get('/products/filter', [UserController::class, 'filter'])->name('products.filter');

//* User routes (logged in)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/checkout', [PaymentController::class, 'index'])->name('checkout');
    Route::post('/cart/add', [UserController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update', [UserController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/{id}', [UserController::class, 'removeFromCart'])->name('cart.remove');

    // ABA Payment Routes (Redirect flow)
    Route::get('/payment/check', [PaymentController::class, 'checkTransaction'])->name('payment.check');
    // Regenerate ABA hash with current cart total just before form submit
    Route::post('/payment/prepare', [PaymentController::class, 'preparePayment'])->name('payment.prepare');
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
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
