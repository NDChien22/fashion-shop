<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FlashSaleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Route::get('/', function () {
//     return view('welcome');
// });
//User routes
Route::get('/', [HomeController::class, 'index'])->name('dashboard');

Route::get('/cart', function () {
    return redirect()->route('user.cart');
})->name('cart');

Route::get('/wishlist', function () {
    return redirect()->route('user.wishlist');
})->name('wishlist');

Route::get('/profile', function () {
    return redirect()->route('user.profile');
})->name('profile');

Route::prefix('user')->name('user.')->group(function () {
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

    Route::get('/cart', function () {
        return view('pages.user.cart.index');
    })->name('cart');

    Route::get('/collection', function () {
        return view('pages.user.collection.index');
    })->name('collection');

    Route::get('/contact', function () {
        return view('pages.user.contact.index');
    })->name('contact');

    Route::get('/introduce', function () {
        return view('pages.user.introduce.index');
    })->name('introduce');

    Route::get('/orders', function () {
        return view('pages.user.order.index');
    })->name('orders');

    Route::get('/product', function () {
        return view('pages.user.product.index');
    })->name('product');

    Route::get('/product/detail', function () {
        return view('pages.user.product.detail');
    })->name('product-detail');

    Route::get('/support', function () {
        return view('pages.user.support.index');
    })->name('support');

    Route::get('/wishlist', function () {
        return view('pages.user.wishlist.index');
    })->name('wishlist');

    Route::post('/vouchers/{voucher}/copy', [VoucherController::class, 'copyVoucherForGuest'])
        ->name('vouchers.copy');

    Route::middleware('auth')->group(function () {
        Route::get('/vouchers', [VoucherController::class, 'userVoucherListView'])->name('vouchers');
        Route::post('/vouchers/{voucher}/collect', [VoucherController::class, 'collectVoucherForUser'])
            ->name('vouchers.collect');

        Route::get('/profile', function () {
            $user = Auth::user();
            $membership = \App\Models\CustomerMembershipLevel::query()
                ->with('membershipLevel')
                ->where('user_id', $user->id)
                ->first();

            return view('pages.user.profile.index', [
                'user' => $user,
                'membership' => $membership,
            ]);
        })->name('profile');

        Route::put('/profile', function (\Illuminate\Http\Request $request) {
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

                if (is_string($user->avatar) && $user->avatar !== '' && !Str::startsWith($user->avatar, ['http://', 'https://', '/'])) {
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

        Route::put('/profile/password', function (\Illuminate\Http\Request $request) {
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

//Auth routes
Route::middleware('guest')->group(function(){ 
    //Login
    Route::get('/login', function(){
        return view('pages.auth.login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'loginHandler'])->name('login_handler');

    //Register
    Route::get('/register', function(){
        return view('pages.auth.register');
    })->name('register');
    Route::post('/register', [AuthController::class, 'registerHandler'])->name('register_handler');

    //Login with Google
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google_login');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google_callback');

    //Forgot Password
    Route::get('/forgot-password', function(){
        return view('pages.auth.forgot-password');
    })->name('forgot_password');
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetEmail'])->name('send_reset_password_email');

    Route::get('/password/reset/{token}', [AuthController::class, 'resetPasswordForm'])->name('password_reset');
    Route::post('/password/reset', [AuthController::class, 'resetPasswordHandler'])->name('reset_password');
});

Route::middleware('auth')->group(function(){
    //Logout
    Route::post('/logout', [AuthController::class, 'logoutHandler'])->name('logout');
});

//Admin routes
Route::prefix('admin')->name('admin.')->group(function(){
    Route::middleware('auth')->group(function(){
        Route::get('/', [AdminController::class, 'DashboardView'])->name('admin_dashboard');

        Route::get('/profile', [AdminController::class, 'ProfileView'])->name('admin-profile');
        Route::get('/account-manager', [AdminController::class, 'AccountManagerView'])->name('admin-account-manager');

        //Product Manager
        Route::get('/product-manager', [AdminController::class, 'ProductManagerView'])->name('product-manager');
        Route::get('/product-categories', [CategoryController::class, 'index'])->name('product-categories');
        Route::get('/product-manager/add', [ProductController::class, 'addProductForm'])->name('add-product');
        Route::post('/product-manager/add', [ProductController::class, 'addProductHandler'])->name('add-product-handler');
        Route::get('/product-manager/edit/{product:slug}', [ProductController::class, 'editProductForm'])->name('edit-product');
        Route::put('/product-manager/edit/{product:slug}', [ProductController::class, 'updateProductHandler'])->name('update-product-handler');
        
        //Category Manager
        Route::post('/product-manager/category', [CategoryController::class, 'store'])->name('add-product-category-handler');
        Route::delete('/product-categories/{id}', [CategoryController::class, 'destroy'])->name('product-categories.destroy');
        //Collection Manager
        Route::get('/product-manager/collections', [CollectionController::class, 'showCollectionManager'])->name('product-collections');
        Route::get('/product-manager/collections/create', [CollectionController::class, 'addCollectionForm'])->name('create-collection');
        Route::post('/product-manager/collections', [CollectionController::class, 'addCollectionHandler'])->name('store-collection');
        Route::get('/product-manager/collections/{collection:slug}', [CollectionController::class, 'showCollectionDetail'])->name('show-collection');
        Route::get('/product-manager/collections/{collection:slug}/edit', [CollectionController::class, 'editCollectionForm'])->name('edit-collection');
        Route::put('/product-manager/collections/{collection:slug}', [CollectionController::class, 'updateCollectionHandler'])->name('update-collection');
        Route::delete('/product-manager/collections/{collection:slug}', [CollectionController::class, 'deleteCollectionHandler'])->name('destroy-collection');
        Route::post('/product-manager/collections/{collection:slug}/add-products', [CollectionController::class, 'addProductToCollection'])->name('add-products-to-collection');
        Route::post('/product-manager/collections/{collection:slug}/remove-product', [CollectionController::class, 'removeProductFromCollection'])->name('remove-product-from-collection');
        //Voucher Manager
        Route::get('/voucher-manager', [VoucherController::class, 'VoucherManagerView'])->name('voucher-manager');
        Route::get('/voucher-manager/add', [VoucherController::class, 'addVoucherView'])->name('add-voucher');
        Route::post('/voucher-manager/add', [VoucherController::class, 'storeVoucherHandler'])->name('store-voucher');
        Route::get('/voucher-manager/edit/{voucher}', [VoucherController::class, 'editVoucherView'])->name('edit-voucher');
        Route::put('/voucher-manager/edit/{voucher}', [VoucherController::class, 'updateVoucherHandler'])->name('update-voucher');
        //Flash Sale Manager
        Route::get('/flash-sale-manager', [FlashSaleController::class, 'flashSaleManagerView'])->name('flash-sale-manager');
        Route::get('/flash-sale-manager/add', [FlashSaleController::class, 'addFlashSaleView'])->name('add-flash-sale');
        Route::post('/flash-sale-manager/add', [FlashSaleController::class, 'storeFlashSaleHandler'])->name('store-flash-sale');
        Route::get('/flash-sale-manager/edit/{flashSale}', [FlashSaleController::class, 'editFlashSaleView'])->name('edit-flash-sale');
        Route::put('/flash-sale-manager/edit/{flashSale}', [FlashSaleController::class, 'updateFlashSaleHandler'])->name('update-flash-sale');
        Route::delete('/flash-sale-manager/{flashSale}', [FlashSaleController::class, 'deleteFlashSaleHandler'])->name('delete-flash-sale');

        //Banner Manager
        Route::get('/banner-manager', [BannerController::class, 'bannerManagerView'])->name('banner-manager');
        Route::get('/banner-manager/add', [BannerController::class, 'addBannerView'])->name('add-banner');
        Route::post('/banner-manager/add', [BannerController::class, 'storeBannerHandler'])->name('store-banner');
        Route::get('/banner-manager/edit/{banner}', [BannerController::class, 'editBannerView'])->name('edit-banner');
        Route::put('/banner-manager/edit/{banner}', [BannerController::class, 'updateBannerHandler'])->name('update-banner');
        Route::delete('/banner-manager/{banner}', [BannerController::class, 'deleteBannerHandler'])->name('delete-banner');

        //Order Manager

        //Customer Manager

        //Report Manager

        //ServiceCenter Manager

        //Employee Manager
        Route::get('/employee-manager', [EmployeeController::class, 'EmployeeManagerView'])->name('employee-manager');
        Route::get('/employee-manager/add', [EmployeeController::class, 'addEmployeeView'])->name('add-employee');
        Route::post('/employee-manager/add', [EmployeeController::class, 'storeEmployeeHandler'])->name('store-employee');
        Route::get('/employee-manager/edit/{employee}', [EmployeeController::class, 'editEmployeeView'])->name('edit-employee');
        Route::put('/employee-manager/edit/{employee}', [EmployeeController::class, 'updateEmployeeHandler'])->name('update-employee');
        Route::delete('/employee-manager/{employee}', [EmployeeController::class, 'deleteEmployee'])->name('delete-employee');

    });
});