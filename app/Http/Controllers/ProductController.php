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
    public function inbound()
    {
        $categories = \App\Models\Category::orderBy('name')->get();
        return view('products.inbound', compact('categories'));
    }

    public function outboundReport(\Illuminate\Http\Request $request)
    {
        $query = \Illuminate\Support\Facades\DB::table('delivery_notes')
            ->join('order_items', 'delivery_notes.order_id', '=', 'order_items.order_id')
            ->leftJoin('inbound_items', function($join) {
                $join->on('order_items.ma_hang', '=', 'inbound_items.ma_hang')
                     ->on('order_items.ma_lot', '=', 'inbound_items.so_lo');
            })
            ->select(
                'delivery_notes.id as dn_id',
                'delivery_notes.order_id',
                'delivery_notes.delivery_date',
                'delivery_notes.cto_code as phieu_xuat',
                'delivery_notes.ten_kh',
                'delivery_notes.dn_code',
                'delivery_notes.trang_thai as dn_status',
                'order_items.ma_hang',
                'order_items.ten_hang',
                'order_items.ma_lot',
                'order_items.han_su_dung',
                'order_items.don_vi_tinh',
                'order_items.so_luong',
                'order_items.don_gia',
                'order_items.thanh_tien',
                'inbound_items.kho_nhap as kho_xuat'
            );

        // Bộ lọc dữ liệu
        if ($request->filled('tu_ngay')) {
            $query->where('delivery_notes.delivery_date', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $query->where('delivery_notes.delivery_date', '<=', $request->den_ngay);
        }
        if ($request->filled('ma_hang')) {
            $query->where('order_items.ma_hang', 'like', '%' . $request->ma_hang . '%');
        }
        if ($request->filled('khach_hang')) {
            $query->where('delivery_notes.ten_kh', 'like', '%' . $request->khach_hang . '%');
        }
        if ($request->filled('so_phieu')) {
            $query->where('delivery_notes.dn_code', 'like', '%' . $request->so_phieu . '%');
        }
        if ($request->filled('so_don_hang')) {
            $query->where('delivery_notes.cto_code', 'like', '%' . $request->so_don_hang . '%');
        }
        if ($request->filled('lot')) {
            $query->where('order_items.ma_lot', 'like', '%' . $request->lot . '%');
        }

        // Lấy tất cả để làm Stat cards
        $allData = $query->get();

        $totalBills = $allData->pluck('dn_code')->unique()->count();
        $totalQty   = $allData->sum('so_luong');
        $totalVal   = $allData->sum('thanh_tien');
        $totalCusts = $allData->pluck('ten_kh')->unique()->count();
        
        $totalWHS   = $allData->pluck('kho_xuat')->filter()->unique()->count();
        // Fallback default fallback to 1 if empty
        if ($totalWHS == 0 && $allData->count() > 0) {
            $totalWHS = 1;
        }

        // Phân trang
        $perPage = $request->input('per_page', 20);
        $rows = $query->orderByDesc('delivery_notes.delivery_date')->paginate($perPage);

        return view('products.outbound_report', compact('rows', 'totalBills', 'totalQty', 'totalVal', 'totalCusts', 'totalWHS'));
    }

    public function stockReport()
    {
        // 1. Lấy dữ liệu nhập kho có kèm bộ lọc
        $query = \Illuminate\Support\Facades\DB::table('inbound_items')
            ->join('product_catalog', 'inbound_items.product_catalog_id', '=', 'product_catalog.id')
            ->leftJoin('categories', 'product_catalog.category_id', '=', 'categories.id')
            ->select(
                'inbound_items.ma_hang',
                'inbound_items.ten_hang',
                'categories.name as category_name',
                'inbound_items.don_vi_tinh',
                'inbound_items.so_lo',
                'inbound_items.han_su_dung',
                'inbound_items.kho_nhap',
                'inbound_items.so_luong',
                'inbound_items.don_gia',
                'inbound_items.thanh_tien'
            );

        if ($maHang = request('ma_hang')) {
            $query->where('inbound_items.ma_hang', 'like', "%{$maHang}%");
        }
        if ($tenHang = request('ten_hang')) {
            $query->where('inbound_items.ten_hang', 'like', "%{$tenHang}%");
        }
        if ($soLo = request('so_lo')) {
            $query->where('inbound_items.so_lo', 'like', "%{$soLo}%");
        }
        if ($nhomHang = request('nhom_hang')) {
            $query->where('categories.name', $nhomHang);
        }
        if ($khoNhap = request('kho_nhap')) {
            $query->where('inbound_items.kho_nhap', $khoNhap);
        }
        if ($hsdTu = request('hsd_tu')) {
            $query->where('inbound_items.han_su_dung', '>=', $hsdTu);
        }
        if ($hsdDen = request('hsd_den')) {
            $query->where('inbound_items.han_su_dung', '<=', $hsdDen);
        }
        if ($tuNgay = request('tu_ngay')) {
            $query->whereDate('inbound_items.created_at', '>=', $tuNgay);
        }
        if ($denNgay = request('den_ngay')) {
            $query->whereDate('inbound_items.created_at', '<=', $denNgay);
        }

        $inboundItems = $query->get();

        // 2. Lấy dữ liệu xuất kho đã giao
        $outboundSummary = \Illuminate\Support\Facades\DB::table('order_items')
            ->select('ma_hang', 'ma_lot', \Illuminate\Support\Facades\DB::raw('SUM(so_luong) as total_out'))
            ->groupBy('ma_hang', 'ma_lot')
            ->get()
            ->groupBy('ma_hang');

        $grouped = $inboundItems->groupBy('ma_hang');
        $products = [];

        // Khởi tạo các biến thống kê
        $statSKU = count($grouped);
        $statLOT = 0;
        $statQty = 0;
        $statVal = 0;
        $statNear = 0;
        $statExpired = 0;

        foreach ($grouped as $maHang => $items) {
            $first = $items->first();
            $sortedItems = $items->sortBy('han_su_dung');

            $lots = [];
            $totalProdQty = 0;
            $totalProdVal = 0;

            foreach ($sortedItems as $item) {
                // Tính số lượng xuất của lô này
                $shippedQty = 0;
                if (isset($outboundSummary[$maHang])) {
                    $lotOut = $outboundSummary[$maHang]->where('ma_lot', $item->so_lo)->first();
                    $shippedQty = $lotOut ? $lotOut->total_out : 0;
                }

                $availableQty = max(0, $item->so_luong - $shippedQty);
                $availableVal = $availableQty * $item->don_gia;

                // Tăng tổng LOT (nếu còn tồn kho)
                if ($availableQty > 0) {
                    $statLOT++;
                    $statQty += $availableQty;
                    $statVal += $availableVal;
                }

                $totalProdQty += $availableQty;
                $totalProdVal += $availableVal;

                $status = 'Còn hạn';
                $statusKey = 'ok';
                $hsd = \Carbon\Carbon::parse($item->han_su_dung);

                if (now()->gt($hsd)) {
                    $status = 'Hết hạn';
                    $statusKey = 'expired';
                    if ($availableQty > 0) $statExpired++;
                } elseif (now()->addMonths(3)->gte($hsd)) {
                    $status = 'Sắp hết hạn';
                    $statusKey = 'near';
                    if ($availableQty > 0) $statNear++;
                }

                $lots[] = [
                    $item->so_lo,
                    $hsd->format('d/m/Y'),
                    $item->kho_nhap,
                    number_format($availableQty, 2, ',', '.'),
                    number_format($item->don_gia, 2, ',', '.'),
                    number_format($availableVal, 2, ',', '.'),
                    $status,
                    $statusKey
                ];
            }

            $products[] = [
                'ma'    => $maHang,
                'ten'   => $first->ten_hang,
                'nhom'  => $first->category_name ?? 'Khác',
                'dvt'   => $first->don_vi_tinh ?? 'Kg',
                'ton'   => number_format($totalProdQty, 2, ',', '.'),
                'gtri'  => number_format($totalProdVal, 2, ',', '.'),
                'lots'  => $lots
            ];
        }

        return view('products.stock_report', compact(
            'products', 
            'statSKU', 
            'statLOT', 
            'statQty', 
            'statVal', 
            'statNear', 
            'statExpired'
        ));
    }

    public function catalog(Request $request)
    {
        $categories = \App\Models\Category::orderBy('name')->get();

        // For demonstration based on user image, showing some products
        // In a real app, this would be a separate Product catalog table
        $products = Product::with('category')
            ->select('ma_hang', 'ten_hang', 'don_vi_tinh', 'don_gia', 'trang_thai', 'category_id')
            ->groupBy('ma_hang', 'ten_hang', 'don_vi_tinh', 'don_gia', 'trang_thai', 'category_id')
            ->paginate(20);

        return view('products.catalog', compact('products', 'categories'));
    }

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
