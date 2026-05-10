<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCatalogController;
use App\Http\Controllers\InboundController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
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
        // DANH MỤC SẢN PHẨM
        // ==========================================================
        Route::prefix('san-pham')->name('catalog.')->group(function () {
            Route::get('/', [ProductCatalogController::class, 'index'])->name('index')
                ->middleware('permission:tonkho,view');
            Route::post('/', [ProductCatalogController::class, 'store'])->name('store')
                ->middleware('permission:tonkho,edit');
            Route::get('/export', [ProductCatalogController::class, 'export'])->name('export')
                ->middleware('permission:tonkho,view');
            Route::post('/import', [ProductCatalogController::class, 'import'])->name('import')
                ->middleware('permission:tonkho,edit');
            Route::get('/template', [ProductCatalogController::class, 'template'])->name('template');
            Route::get('/check-ma-hang', [ProductCatalogController::class, 'checkMaHang'])->name('check-ma-hang');
            Route::get('/{productCatalog}', [ProductCatalogController::class, 'show'])->name('show');
            Route::match(['PUT','PATCH'], '/{productCatalog}', [ProductCatalogController::class, 'update'])->name('update')
                ->middleware('permission:tonkho,edit');
            Route::delete('/{productCatalog}', [ProductCatalogController::class, 'destroy'])->name('destroy')
                ->middleware('permission:tonkho,delete');
        });

        // ==========================================================
        // KHÁCH HÀNG
        // ==========================================================
        Route::get('/khach-hang', [CustomerController::class, 'index'])->name('customers.index')
            ->middleware('permission:khachhang,view');
        Route::get('/khach-hang/export', [CustomerController::class, 'export'])->name('customers.export')
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
        Route::post('/khach-hang/import', [CustomerController::class, 'import'])->name('customers.import')
            ->middleware('permission:khachhang,edit');

        // ==========================================================
        // ĐƠN HÀNG
        // ==========================================================
        Route::get('/don-hang', [OrderController::class, 'index'])->name('orders.index')
            ->middleware('permission:donhang,view');
        Route::get('/don-hang/next-code', [OrderController::class, 'nextCode'])->name('orders.next-code');
        Route::get('/don-hang/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit')
            ->middleware('permission:donhang,edit');
        Route::get('/don-hang/{order}/pdf', [OrderController::class, 'exportPdf'])->name('orders.pdf')
            ->middleware('permission:donhang,view');
        Route::get('/don-hang/{order}/excel', [OrderController::class, 'exportExcel'])->name('orders.excel')
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
        Route::get('/phieu-giao/next-code', [DeliveryNoteController::class, 'nextCode'])->name('deliveries.next-code');
        Route::get('/phieu-giao/{deliveryNote}', [DeliveryNoteController::class, 'show'])->name('deliveries.show')
            ->middleware('permission:phieugiao,view');
        Route::get('/phieu-giao/{deliveryNote}/pdf', [DeliveryNoteController::class, 'exportPdf'])->name('deliveries.pdf')
            ->middleware('permission:phieugiao,view');
        Route::post('/phieu-giao', [DeliveryNoteController::class, 'store'])->name('deliveries.store')
            ->middleware('permission:phieugiao,edit');
        Route::patch('/phieu-giao/{deliveryNote}/trang-thai', [DeliveryNoteController::class, 'updateStatus'])->name('deliveries.update-status')
            ->middleware('permission:phieugiao,edit');
        Route::post('/phieu-giao/{deliveryNote}/save-items', [DeliveryNoteController::class, 'saveItems'])->name('deliveries.save-items')
            ->middleware('permission:phieugiao,edit');
        Route::delete('/phieu-giao/{deliveryNote}', [DeliveryNoteController::class, 'destroy'])->name('deliveries.destroy')
            ->middleware('permission:phieugiao,delete');

        // ==========================================================
        // TỒN KHO & SẢN PHẨM
        // ==========================================================
        Route::prefix('ton-kho')->name('inventory.')->group(function() {
            Route::get('/nhap-kho', [InboundController::class, 'create'])->name('inbound')
                ->middleware('permission:tonkho,view');
            Route::post('/nhap-kho', [InboundController::class, 'store'])->name('inbound.store');
            Route::delete('/nhap-kho/attachment/{id}', [InboundController::class, 'deleteAttachment'])->name('inbound.attachment.delete');
            Route::delete('/nhap-kho/item/{id}', [InboundController::class, 'deleteItem'])->name('inbound.item.delete');
            Route::get('/nhap-kho/next-code', [InboundController::class, 'nextCode'])->name('inbound.next-code');
            Route::get('/nhap-kho/product-search', [InboundController::class, 'productSearch'])->name('inbound.product-search');
            Route::get('/bao-cao-xuat-kho', [ProductController::class, 'outboundReport'])->name('outbound-report')
                ->middleware('permission:tonkho,view');
            Route::get('/bao-cao-ton-kho', [ProductController::class, 'stockReport'])->name('stock-report')
                ->middleware('permission:tonkho,view');

            // APIs
            Route::get('/api/stock/{maHang}', [ProductController::class, 'getStock'])->name('stock');
            Route::get('/api/all-stock', [ProductController::class, 'getAllStock'])->name('all-stock');

            // Legacy store/update/delete if still needed for the new logic later
            Route::post('/', [ProductController::class, 'store'])->name('store')
                ->middleware('permission:tonkho,edit');
            Route::match(['PUT', 'PATCH'], '/{product}', [ProductController::class, 'update'])->name('update')
                ->middleware('permission:tonkho,edit');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy')
                ->middleware('permission:tonkho,delete');
        });

        // ==========================================================
        // CÔNG NỢ & THANH TOÁN
        // ==========================================================
        Route::get('/cong-no', [PaymentController::class, 'indexDebt'])->name('debt.index');
        Route::get('/debt/export', [PaymentController::class, 'exportDebt'])->name('debt.export');
        Route::get('/thanh-toan', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/thanh-toan', [PaymentController::class, 'store'])->name('payments.store');
        Route::delete('/thanh-toan/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
        Route::get('/thanh-toan/api/debt/{ctoCode}', [PaymentController::class, 'getDebtInfo'])->name('payments.debt-info');
        Route::get('/bao-cao-tai-chinh', [ReportController::class, 'finance'])->name('reports.finance');
        Route::get('/bao-cao-tong-hop', [ReportController::class, 'summary'])->name('reports.summary');

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
