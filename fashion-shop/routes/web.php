<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CustomerManagementController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FlashSaleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderManagementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\WhistlistController;
use App\Livewire\User\HomePage;
use App\Livewire\User\ProductDetail;
use App\Models\CustomerMembershipLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Route::get('/', function () {
//     return view('welcome');
// });
Broadcast::routes(['middleware' => ['web', 'auth']]);

// User routes
Route::get('/', HomePage::class)->name('dashboard');

Route::get('/cart', function () {
    return redirect()->route('user.cart');
})->name('cart');

Route::get('/whistlist', function () {
    return redirect()->route('user.whistlist');
})->name('whistlist');

Route::get('/profile', function () {
    return redirect()->route('user.profile');
})->name('profile');

Route::prefix('user')->name('user.')->group(function () {
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

    Route::post('/cart/items', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart/products/{productId}/skus', [CartController::class, 'productSkus'])->name('cart.product-skus');
    Route::patch('/cart/items/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/items/{cart}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

    // User storefront pages
    Route::get('/product', [ProductController::class, 'listing'])->name('product');
    Route::get('/collections', [CollectionController::class, 'index'])->name('collections');
    Route::get('/collection/{collection:slug}', [CollectionController::class, 'listing'])->name('collection');
    Route::get('/product/{product:slug}', ProductDetail::class)->name('product-detail');
    Route::get('/cart', [CartController::class, 'index'])->name('cart');

    // Static Pages
    Route::get('/contact', function () {
        return view('pages.user.contact.index');
    })->name('contact');

    Route::get('/introduce', function () {
        return view('pages.user.introduce.index');
    })->name('introduce');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/feedback', [OrderController::class, 'storeFeedback'])->name('orders.feedback');

    Route::get('/support', function () {
        return view('pages.user.support.index');
    })->name('support');

    Route::get('/whistlist', [WhistlistController::class, 'index'])->name('whistlist');
    Route::post('/whistlist/items', [WhistlistController::class, 'store'])->name('whistlist.add');
    Route::delete('/whistlist/items/{productId}', [WhistlistController::class, 'destroy'])->name('whistlist.remove');

    Route::post('/vouchers/{voucher}/copy', [VoucherController::class, 'copyVoucherForGuest'])
        ->name('vouchers.copy');

    Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
    Route::get('/checkout/vnpay/return', [CheckoutController::class, 'vnpayReturn'])->name('checkout.vnpay-return');
    Route::get('/checkout/stripe/success', [CheckoutController::class, 'stripeSuccess'])->name('checkout.stripe-success');
    Route::get('/checkout/stripe/cancel', [CheckoutController::class, 'stripeCancel'])->name('checkout.stripe-cancel');

    Route::middleware('auth')->group(function () {
        Route::get('/vouchers', [VoucherController::class, 'userVoucherListView'])->name('vouchers');
        Route::post('/vouchers/{voucher}/collect', [VoucherController::class, 'collectVoucherForUser'])
            ->name('vouchers.collect');

        Route::get('/profile', function () {
            $user = Auth::user();
            $membership = CustomerMembershipLevel::query()
                ->with('membershipLevel')
                ->where('user_id', $user->id)
                ->first();

            return view('pages.user.profile.index', [
                'user' => $user,
                'membership' => $membership,
            ]);
        })->name('profile');

        Route::put('/profile', function (Request $request) {
            $validated = $request->validate([
                'full_name' => ['required', 'string', 'max:255'],
                'phone_number' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:255'],
                'gender' => ['nullable', 'in:male,female,other'],
                'birthday' => ['nullable', 'date'],
                'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ], [
                'full_name.required' => 'Vui lòng nhập họ và tên.',
                'gender.in' => 'Giới tính không hợp lệ.',
                'birthday.date' => 'Ngày sinh không hợp lệ.',
                'avatar.image' => 'Ảnh đại diện phải là tệp hình ảnh.',
                'avatar.mimes' => 'Ảnh đại diện chỉ hỗ trợ jpg, jpeg, png, webp.',
                'avatar.max' => 'Ảnh đại diện không được vượt quá 2MB.',
            ]);

            $user = $request->user();

            if ($request->hasFile('avatar')) {
                $avatarFile = $request->file('avatar');
                $extension = $avatarFile->getClientOriginalExtension() ?: 'jpg';
                $fileName = sprintf('user-%d-%s.%s', $user->id, Str::uuid()->toString(), $extension);
                $newAvatarPath = $avatarFile->storeAs('avatars', $fileName, 'public');

                if (is_string($user->avatar) && $user->avatar !== '' && ! Str::startsWith($user->avatar, ['http://', 'https://', '/'])) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $validated['avatar'] = $newAvatarPath;
            }

            $user->update($validated);

            return back()->with('success', 'Cập nhật hồ sơ thành công.');
        })->name('profile.update');

        Route::get('/profile/password', function () {
            return view('pages.user.profile.change-password');
        })->name('profile.password');

        Route::put('/profile/password', function (Request $request) {
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ], [
                'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
                'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
                'password.required' => 'Vui lòng nhập mật khẩu mới.',
                'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
                'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            ]);

            $user = $request->user();
            $user->password = Hash::make($validated['password']);
            $user->save();

            return redirect()->route('user.profile.password')->with('success', 'Đổi mật khẩu thành công.');
        })->name('profile.password.update');
    });
});

// Auth routes
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', function () {
        return view('pages.auth.login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'loginHandler'])->name('login_handler');

    // Register
    Route::get('/register', function () {
        return view('pages.auth.register');
    })->name('register');
    Route::post('/register', [AuthController::class, 'registerHandler'])->name('register_handler');

    // Login with Google
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google_login');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google_callback');

    // Forgot Password
    Route::get('/forgot-password', function () {
        return view('pages.auth.forgot-password');
    })->name('forgot_password');
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetEmail'])->name('send_reset_password_email');

    Route::get('/password/reset/{token}', [AuthController::class, 'resetPasswordForm'])->name('password_reset');
    Route::post('/password/reset', [AuthController::class, 'resetPasswordHandler'])->name('reset_password');
});

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logoutHandler'])->name('logout');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.access'])->group(function () {
    Route::middleware('admin.permission:dashboard')->group(function () {
        Route::get('/', [AdminController::class, 'DashboardView'])->name('admin_dashboard');
    });

    Route::middleware('admin.permission:profile')->group(function () {
        Route::get('/profile', [AdminController::class, 'ProfileView'])->name('admin-profile');
    });

    Route::middleware('admin.permission:account-manager')->group(function () {
        Route::get('/account-manager', [AdminController::class, 'AccountManagerView'])->name('admin-account-manager');
    });

    Route::middleware('admin.permission:products')->group(function () {
        // Product Manager
        Route::get('/product-manager', [AdminController::class, 'ProductManagerView'])->name('product-manager');
        Route::get('/product-categories', [CategoryController::class, 'index'])->name('product-categories');
        Route::get('/product-manager/add', [ProductController::class, 'addProductForm'])->name('add-product');
        Route::post('/product-manager/add', [ProductController::class, 'addProductHandler'])->name('add-product-handler');
        Route::get('/product-manager/edit/{product:slug}', [ProductController::class, 'editProductForm'])->name('edit-product');
        Route::put('/product-manager/edit/{product:slug}', [ProductController::class, 'updateProductHandler'])->name('update-product-handler');

        // Category Manager
        Route::post('/product-manager/category', [CategoryController::class, 'store'])->name('add-product-category-handler');
        Route::delete('/product-categories/{id}', [CategoryController::class, 'destroy'])->name('product-categories.destroy');
        // Collection Manager
        Route::get('/product-manager/collections', [CollectionController::class, 'showCollectionManager'])->name('product-collections');
        Route::get('/product-manager/collections/create', [CollectionController::class, 'addCollectionForm'])->name('create-collection');
        Route::post('/product-manager/collections', [CollectionController::class, 'addCollectionHandler'])->name('store-collection');
        Route::get('/product-manager/collections/{collection:slug}', [CollectionController::class, 'showCollectionDetail'])->name('show-collection');
        Route::get('/product-manager/collections/{collection:slug}/edit', [CollectionController::class, 'editCollectionForm'])->name('edit-collection');
        Route::put('/product-manager/collections/{collection:slug}', [CollectionController::class, 'updateCollectionHandler'])->name('update-collection');
        Route::delete('/product-manager/collections/{collection:slug}', [CollectionController::class, 'deleteCollectionHandler'])->name('destroy-collection');
        Route::post('/product-manager/collections/{collection:slug}/add-products', [CollectionController::class, 'addProductToCollection'])->name('add-products-to-collection');
        Route::post('/product-manager/collections/{collection:slug}/remove-product', [CollectionController::class, 'removeProductFromCollection'])->name('remove-product-from-collection');
        // Voucher Manager
        Route::get('/voucher-manager', [VoucherController::class, 'VoucherManagerView'])->name('voucher-manager');
        Route::get('/voucher-manager/add', [VoucherController::class, 'addVoucherView'])->name('add-voucher');
        Route::post('/voucher-manager/add', [VoucherController::class, 'storeVoucherHandler'])->name('store-voucher');
        Route::get('/voucher-manager/edit/{voucher}', [VoucherController::class, 'editVoucherView'])->name('edit-voucher');
        Route::put('/voucher-manager/edit/{voucher}', [VoucherController::class, 'updateVoucherHandler'])->name('update-voucher');
        // Flash Sale Manager
        Route::get('/flash-sale-manager', [FlashSaleController::class, 'flashSaleManagerView'])->name('flash-sale-manager');
        Route::get('/flash-sale-manager/add', [FlashSaleController::class, 'addFlashSaleView'])->name('add-flash-sale');
        Route::post('/flash-sale-manager/add', [FlashSaleController::class, 'storeFlashSaleHandler'])->name('store-flash-sale');
        Route::get('/flash-sale-manager/edit/{flashSale}', [FlashSaleController::class, 'editFlashSaleView'])->name('edit-flash-sale');
        Route::put('/flash-sale-manager/edit/{flashSale}', [FlashSaleController::class, 'updateFlashSaleHandler'])->name('update-flash-sale');
        Route::delete('/flash-sale-manager/{flashSale}', [FlashSaleController::class, 'deleteFlashSaleHandler'])->name('delete-flash-sale');

        // Banner Manager
        Route::get('/banner-manager', [BannerController::class, 'bannerManagerView'])->name('banner-manager');
        Route::get('/banner-manager/add', [BannerController::class, 'addBannerView'])->name('add-banner');
        Route::post('/banner-manager/add', [BannerController::class, 'storeBannerHandler'])->name('store-banner');
        Route::get('/banner-manager/edit/{banner}', [BannerController::class, 'editBannerView'])->name('edit-banner');
        Route::put('/banner-manager/edit/{banner}', [BannerController::class, 'updateBannerHandler'])->name('update-banner');
        Route::delete('/banner-manager/{banner}', [BannerController::class, 'deleteBannerHandler'])->name('delete-banner');
    });

    Route::middleware('admin.permission:orders')->group(function () {
        // Order Manager
        Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders');
        Route::put('/orders/{order}', [OrderManagementController::class, 'update'])->name('orders.update');
        Route::get('/feedback-manager', [AdminController::class, 'FeedbackManagerView'])->name('feedback-manager');
    });

    Route::middleware('admin.permission:customers')->group(function () {
        // Customer Manager
        Route::get('/customers', [CustomerManagementController::class, 'index'])->name('customers');
    });

    Route::middleware('admin.permission:revenue')->group(function () {
        // Report Manager
        Route::get('/revenue', [AdminController::class, 'RevenueView'])->name('revenue');
    });

    Route::middleware('admin.permission:support')->group(function () {
        // ServiceCenter Manager
        Route::get('/support', [AdminController::class, 'SupportManagerView'])->name('support');
    });

    Route::middleware('admin.permission:employees')->group(function () {
        // Employee Manager
        Route::get('/employee-manager', [EmployeeController::class, 'EmployeeManagerView'])->name('employee-manager');
        Route::get('/employee-manager/add', [EmployeeController::class, 'addEmployeeView'])->name('add-employee');
        Route::post('/employee-manager/add', [EmployeeController::class, 'storeEmployeeHandler'])->name('store-employee');
        Route::get('/employee-manager/edit/{employee}', [EmployeeController::class, 'editEmployeeView'])->name('edit-employee');
        Route::put('/employee-manager/edit/{employee}', [EmployeeController::class, 'updateEmployeeHandler'])->name('update-employee');
        Route::delete('/employee-manager/{employee}', [EmployeeController::class, 'deleteEmployee'])->name('delete-employee');
    });
});
