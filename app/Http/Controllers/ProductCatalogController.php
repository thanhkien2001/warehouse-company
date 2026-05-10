<?php

namespace App\Http\Controllers;

use App\Exports\ProductCatalogExport;
use App\Imports\ProductCatalogImport;
use App\Models\Category;
use App\Models\ProductCatalog;
use App\Services\CodeGeneratorService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductCatalogController extends Controller
{
    // ─── LIST ────────────────────────────────────────────────
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $query = ProductCatalog::with('category');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ma_hang', 'like', "%$search%")
                  ->orWhere('ten_hang', 'like', "%$search%");
            });
        }
        if ($catId = $request->get('category_id')) {
            $query->where('category_id', $catId);
        }
        if ($ncc = $request->get('nha_cung_cap')) {
            $query->where('nha_cung_cap', 'like', "%$ncc%");
        }
        if ($status = $request->get('trang_thai')) {
            $query->where('trang_thai', $status);
        }

        $perPage  = $request->get('per_page', 20);
        $sortBy   = $request->get('sort', 'newest'); // newest | ma_hang
        if ($sortBy === 'ma_hang') {
            $query->orderBy('ma_hang', 'asc');
        } else {
            $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
        }
        $products = $query->paginate($perPage)->withQueryString();

        return view('products.catalog', compact('products', 'categories'));
    }

    // ─── STORE ────────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'ma_hang'      => 'nullable|string|max:100|unique:product_catalog,ma_hang',
            'ten_hang'     => 'required|string|max:255',
            'category_id'  => 'nullable|exists:categories,id',
            'quy_cach'     => 'nullable|string|max:100',
            'don_vi_tinh'  => 'nullable|string|max:50',
            'gia_nhap'     => 'nullable|numeric|min:0',
            'gia_ban'      => 'nullable|numeric|min:0',
            'vat'          => 'nullable|integer|in:0,5,8,10',
            'nha_cung_cap' => 'nullable|string|max:255',
            'ma_ncc'       => 'nullable|string|max:100',
            'trang_thai'   => 'nullable|in:Hoạt động,Ngừng hoạt động',
            'ghi_chu'      => 'nullable|string',
        ]);

        // Auto-generate mã hàng if blank
        if (empty($data['ma_hang'])) {
            $data['ma_hang'] = CodeGeneratorService::generateMaHang();
        }

        $data['trang_thai'] = $data['trang_thai'] ?? 'Hoạt động';
        $product = ProductCatalog::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Thêm sản phẩm thành công!',
            'product' => $product->load('category'),
        ]);
    }

    // ─── SHOW ─────────────────────────────────────────────────
    public function show(ProductCatalog $productCatalog)
    {
        return response()->json($productCatalog->load('category'));
    }

    // ─── UPDATE ───────────────────────────────────────────────
    public function update(Request $request, ProductCatalog $productCatalog)
    {
        $data = $request->validate([
            'ten_hang'     => 'required|string|max:255',
            'category_id'  => 'nullable|exists:categories,id',
            'quy_cach'     => 'nullable|string|max:100',
            'don_vi_tinh'  => 'nullable|string|max:50',
            'gia_nhap'     => 'nullable|numeric|min:0',
            'gia_ban'      => 'nullable|numeric|min:0',
            'vat'          => 'nullable|integer|in:0,5,8,10',
            'nha_cung_cap' => 'nullable|string|max:255',
            'ma_ncc'       => 'nullable|string|max:100',
            'trang_thai'   => 'nullable|in:Hoạt động,Ngừng hoạt động',
            'ghi_chu'      => 'nullable|string',
        ]);

        $productCatalog->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật sản phẩm thành công!',
            'product' => $productCatalog->load('category'),
        ]);
    }

    // ─── DESTROY ──────────────────────────────────────────────
    public function destroy(ProductCatalog $productCatalog)
    {
        $productCatalog->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa sản phẩm!']);
    }

    // ─── EXPORT ───────────────────────────────────────────────
    public function export(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'nha_cung_cap', 'trang_thai']);
        $filename = 'danh-muc-san-pham-' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new ProductCatalogExport($filters), $filename);
    }

    // ─── IMPORT ───────────────────────────────────────────────
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new ProductCatalogImport(), $request->file('file'));
            return response()->json(['success' => true, 'message' => 'Import thành công!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi import: ' . $e->getMessage()], 422);
        }
    }

    // ─── TEMPLATE DOWNLOAD ────────────────────────────────────
    public function template()
    {
        // Return a pre-built template with correct column headers
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-san-pham.csv"',
        ];

        $rows = [
            ['ma_hang', 'ten_hang', 'nhom_hang', 'quy_cach', 'dvt', 'gia_ban_vnd', 'vat', 'nha_cung_cap', 'ma_ncc', 'trang_thai', 'ghi_chu'],
            ['HG-001', 'Tên sản phẩm mẫu', 'Chiết xuất', '25 kg/thùng', 'Kg', '1200000', '10', 'Tên NCC', 'NCC-001', 'Hoạt động', ''],
        ];

        $output = "\xEF\xBB\xBF"; // BOM for Excel UTF-8
        foreach ($rows as $row) {
            $output .= implode(',', array_map(fn($c) => '"' . str_replace('"', '""', $c) . '"', $row)) . "\r\n";
        }

        return response($output, 200, $headers);
    }

    // ─── CHECK DUPLICATE MA HANG ──────────────────────────────
    public function checkMaHang(Request $request)
    {
        $exists = ProductCatalog::where('ma_hang', $request->ma_hang)->exists();
        return response()->json(['exists' => $exists]);
    }
}
