<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// ==========================================================
// AUTH ROUTES (Guest only)
// ==========================================================
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Chờ duyệt (MoiDangKy)
    Route::get('/cho-duyet', [AuthController::class, 'choDuyet'])->name('cho-duyet');

    // Profile
    Route::post('/doi-mat-khau', [AuthController::class, 'doiMatKhau'])->name('doi-mat-khau');
    Route::post('/doi-thong-tin', [AuthController::class, 'doiThongTin'])->name('doi-thong-tin');

    // ==========================================================
    // MAIN APP ROUTES (Không phải MoiDangKy)
    // ==========================================================
    Route::middleware('role:Admin,NhanVien,KeToan,QuanLy')->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // ==========================================================
        // KHÁCH HÀNG
        // ==========================================================
        Route::get('/khach-hang', [CustomerController::class, 'index'])->name('customers.index')
            ->middleware('permission:khachhang,view');
        Route::get('/khach-hang/{customer}', [CustomerController::class, 'show'])->name('customers.show')
            ->middleware('permission:khachhang,view');
        Route::post('/khach-hang', [CustomerController::class, 'store'])->name('customers.store')
            ->middleware('permission:khachhang,edit');
        Route::match(['PUT', 'PATCH'], '/khach-hang/{customer}', [CustomerController::class, 'update'])->name('customers.update')
            ->middleware('permission:khachhang,edit');
        Route::delete('/khach-hang/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy')
            ->middleware('permission:khachhang,delete');
        Route::get('/khach-hang/check-duplicate', [CustomerController::class, 'checkDuplicate'])->name('customers.check');

        // ==========================================================
        // ĐƠN HÀNG
        // ==========================================================
        Route::get('/don-hang', [OrderController::class, 'index'])->name('orders.index')
            ->middleware('permission:donhang,view');
        Route::get('/don-hang/{order}', [OrderController::class, 'show'])->name('orders.show')
            ->middleware('permission:donhang,view');
        Route::post('/don-hang', [OrderController::class, 'store'])->name('orders.store')
            ->middleware('permission:donhang,edit');
        Route::match(['PUT', 'PATCH'], '/don-hang/{order}', [OrderController::class, 'update'])->name('orders.update')
            ->middleware('permission:donhang,edit');
        Route::post('/don-hang/{order}/save-items', [OrderController::class, 'saveItems'])->name('orders.save-items')
            ->middleware('permission:donhang,edit');
        Route::delete('/don-hang/{order}', [OrderController::class, 'destroy'])->name('orders.destroy')
            ->middleware('permission:donhang,delete');

        // ==========================================================
        // PHIẾU GIAO HÀNG
        // ==========================================================
        Route::get('/phieu-giao', [DeliveryNoteController::class, 'index'])->name('deliveries.index')
            ->middleware('permission:phieugiao,view');
        Route::get('/phieu-giao/{deliveryNote}', [DeliveryNoteController::class, 'show'])->name('deliveries.show')
            ->middleware('permission:phieugiao,view');
        Route::post('/phieu-giao', [DeliveryNoteController::class, 'store'])->name('deliveries.store')
            ->middleware('permission:phieugiao,edit');
        Route::patch('/phieu-giao/{deliveryNote}/trang-thai', [DeliveryNoteController::class, 'updateStatus'])->name('deliveries.update-status')
            ->middleware('permission:phieugiao,edit');
        Route::delete('/phieu-giao/{deliveryNote}', [DeliveryNoteController::class, 'destroy'])->name('deliveries.destroy')
            ->middleware('permission:phieugiao,delete');

        // ==========================================================
        // TỒN KHO & SẢN PHẨM
        // ==========================================================
        Route::get('/ton-kho', [ProductController::class, 'index'])->name('products.index')
            ->middleware('permission:tonkho,view');
        Route::post('/ton-kho', [ProductController::class, 'store'])->name('products.store')
            ->middleware('permission:tonkho,edit');
        Route::match(['PUT', 'PATCH'], '/ton-kho/{product}', [ProductController::class, 'update'])->name('products.update')
            ->middleware('permission:tonkho,edit');
        Route::delete('/ton-kho/{product}', [ProductController::class, 'destroy'])->name('products.destroy')
            ->middleware('permission:tonkho,delete');
        Route::get('/ton-kho/api/stock/{maHang}', [ProductController::class, 'getStock'])->name('products.stock');
        Route::get('/ton-kho/api/all-stock', [ProductController::class, 'getAllStock'])->name('products.all-stock');

        // ==========================================================
        // CÔNG NỢ & THANH TOÁN
        // ==========================================================
        Route::get('/cong-no', [PaymentController::class, 'indexDebt'])->name('debt.index');
        Route::get('/thanh-toan', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/thanh-toan', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/thanh-toan/api/debt/{ctoCode}', [PaymentController::class, 'getDebtInfo'])->name('payments.debt-info');

        // ==========================================================
        // ADMIN (chỉ Admin)
        // ==========================================================
        Route::middleware('role:Admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('index');
            Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
            Route::match(['PUT', 'PATCH'], '/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
            Route::patch('/users/{user}/toggle-status', [AdminController::class, 'toggleStatus'])->name('users.toggle-status');
            Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
            Route::get('/users/{user}/permissions', [AdminController::class, 'getPermissions'])->name('users.permissions');
            Route::post('/users/{user}/permissions', [AdminController::class, 'savePermissions'])->name('users.permissions.save');
            Route::post('/settings', [AdminController::class, 'saveSetting'])->name('settings.save');
        });
    });
});
