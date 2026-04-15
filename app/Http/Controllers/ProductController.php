<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\Product;
use App\Services\CodeGeneratorService;
use App\Services\InventoryService;
use App\Services\LogService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Sub-tab: tonkho | lichsu
        $tab = $request->get('tab', 'tonkho');

        // Báo cáo tồn kho (Sub-tab: tonkho)
        $allReportData = InventoryService::getReport();
        $itemsReport = collect($allReportData);
        
        $perPageTK = $request->get('limit', 15);
        $pageTK = $request->get('page', Paginator::resolveCurrentPage() ?: 1);
        
        $inventoryReport = new LengthAwarePaginator(
            $itemsReport->forPage($pageTK, $perPageTK),
            $itemsReport->count(),
            $perPageTK,
            $pageTK,
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        // Lịch sử nhập hàng
        $query = Product::query();
        $filterLS = $request->get('filter_ls', 'all');
        if ($filterLS === 'month') {
            $query->whereMonth('nhap_date', now()->month)->whereYear('nhap_date', now()->year);
        } elseif ($filterLS === 'year') {
            $query->whereYear('nhap_date', now()->year);
        } elseif ($filterLS === 'custom' && $request->ls_start && $request->ls_end) {
            $query->whereBetween('nhap_date', [$request->ls_start, $request->ls_end]);
        }

        if ($kw = $request->get('search_ls')) {
            $query->where(function($q) use ($kw) {
                $q->where('ma_hang', 'like', "%{$kw}%")
                  ->orWhere('ten_hang', 'like', "%{$kw}%");
            });
        }

        $sortLS = $request->get('sort_ls', 'newest');
        match ($sortLS) {
            'oldest'   => $query->orderBy('nhap_date'),
            'sl_desc'  => $query->orderByDesc('so_luong_nhap'),
            'sl_asc'   => $query->orderBy('so_luong_nhap'),
            default    => $query->orderByDesc('nhap_date')->orderByDesc('id'),
        };

        $lichSuNhap = $query->paginate(20, ['*'], 'ls_page')->withQueryString();

        return view('products.index', compact('inventoryReport', 'lichSuNhap', 'tab', 'filterLS', 'sortLS'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'is_new'       => 'boolean',
            'ma_hang'      => 'nullable|string|max:100',
            'ten_hang'     => 'required_if:is_new,true|nullable|string|max:255',
            'mo_ta'        => 'nullable|string',
            'so_luong_nhap'=> 'required|numeric|min:0.001',
            'don_vi_tinh'  => 'nullable|string|max:50',
            'don_gia'      => 'nullable|numeric|min:0',
            'ghi_chu'      => 'nullable|string',
            'nhap_date'    => 'required|date',
        ]);

        $isNew = $request->boolean('is_new', true);

        if ($isNew) {
            $maHang = CodeGeneratorService::generateMaHang();
            $data['ma_hang'] = $maHang;
        } else {
            if (empty($data['ma_hang'])) {
                return response()->json(['success' => false, 'message' => 'Vui lòng chọn mã hàng!']);
            }
            // Lấy tên từ SP cũ nếu không điền
            if (empty($data['ten_hang'])) {
                $existing = Product::where('ma_hang', $data['ma_hang'])->first();
                $data['ten_hang'] = $existing?->ten_hang ?? $data['ma_hang'];
            }
        }

        $product = Product::create($data);
        LogService::log('Nhập hàng', "Nhập [{$data['ma_hang']}] - SL: {$data['so_luong_nhap']}");

        return response()->json(['success' => true, 'message' => 'Nhập hàng thành công!', 'ma_hang' => $data['ma_hang']]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'ten_hang'     => 'required|string|max:255',
            'mo_ta'        => 'nullable|string',
            'so_luong_nhap'=> 'required|numeric|min:0',
            'don_vi_tinh'  => 'nullable|string|max:50',
            'don_gia'      => 'nullable|numeric|min:0',
            'ghi_chu'      => 'nullable|string',
            'nhap_date'    => 'required|date',
        ]);

        $product->update($data);
        LogService::log('Sửa nhập hàng', "Sửa bản ghi nhập #{$product->id} [{$product->ma_hang}]");

        return response()->json(['success' => true, 'message' => 'Đã cập nhật!']);
    }

    public function destroy(Product $product)
    {
        LogService::log('Xóa nhập hàng', "Xóa bản ghi nhập #{$product->id} [{$product->ma_hang}]");
        $product->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa dữ liệu nhập hàng!']);
    }

    public function getStock(string $maHang)
    {
        $stock = InventoryService::getStock($maHang);
        return response()->json(['ma_hang' => $maHang, 'con_lai' => $stock]);
    }

    public function getAllStock()
    {
        return response()->json(InventoryService::getAllStockMap());
    }
}
