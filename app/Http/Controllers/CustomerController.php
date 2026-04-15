<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Services\CodeGeneratorService;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        // Bộ lọc thời gian
        $filter = $request->get('filter', 'all');
        if ($filter === 'month') {
            $query->whereMonth('created_date', now()->month)->whereYear('created_date', now()->year);
        } elseif ($filter === 'year') {
            $query->whereYear('created_date', now()->year);
        } elseif ($filter === 'custom' && $request->date_start && $request->date_end) {
            $query->whereBetween('created_date', [$request->date_start, $request->date_end]);
        }

        // Tìm kiếm
        if ($kw = $request->get('search')) {
            $query->where(function($q) use ($kw) {
                $q->where('ten_cty', 'like', "%{$kw}%")
                  ->orWhere('ma_kh', 'like', "%{$kw}%")
                  ->orWhere('ma_so_thue', 'like', "%{$kw}%")
                  ->orWhere('sdt', 'like', "%{$kw}%")
                  ->orWhere('nguoi_lien_he', 'like', "%{$kw}%");
            });
        }

        // Khu vực
        if ($kv = $request->get('khu_vuc')) {
            $query->where('khu_vuc', $kv);
        }

        // Sắp xếp
        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'oldest' => $query->orderBy('created_date'),
            'az'     => $query->orderBy('ten_cty'),
            'za'     => $query->orderByDesc('ten_cty'),
            default  => $query->orderByDesc('created_date')->orderByDesc('id'),
        };

        $limit = $request->get('limit', 20);
        $customers = $query->paginate($limit)->withQueryString();
        $allCustomers = Customer::orderBy('ten_cty')->get(['id','ma_kh','ten_cty','sdt']);

        return view('customers.index', compact('customers', 'filter', 'sort', 'allCustomers'));
    }

    public function show(Customer $customer, Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json($customer);
        }

        $orders = Order::where('customer_id', $customer->id)
            ->orderByDesc('order_date')
            ->paginate(10);

        return view('customers.show', compact('customer', 'orders'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ten_cty'      => 'required|string|max:255|unique:customers,ten_cty',
            'ma_so_thue'   => 'required|string|max:50|unique:customers,ma_so_thue',
            'created_date' => 'required|date',
            'dia_chi'      => 'nullable|string|max:500',
            'nguoi_lien_he'=> 'nullable|string|max:150',
            'sdt'          => 'nullable|string|max:20',
            'dia_chi_nhan' => 'nullable|string|max:500',
            'sdt_nhan'     => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:150',
            'khu_vuc'      => 'nullable|string',
            'ghi_chu'      => 'nullable|string',
        ], [
            'ten_cty.unique'    => 'Tên công ty đã tồn tại.',
            'ma_so_thue.unique' => 'Mã số thuế đã tồn tại.',
        ]);

        $maKH = CodeGeneratorService::generateMaKH($data['ma_so_thue'], $data['created_date']);
        $data['ma_kh'] = $maKH;

        $customer = Customer::create($data);
        LogService::log('Thêm khách hàng', "Thêm KH [{$maKH}] {$data['ten_cty']}");

        return response()->json(['success' => true, 'message' => 'Thêm khách hàng thành công!', 'ma_kh' => $maKH]);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'ten_cty'      => ['required','string','max:255', Rule::unique('customers','ten_cty')->ignore($customer->id)],
            'ma_so_thue'   => ['required','string','max:50', Rule::unique('customers','ma_so_thue')->ignore($customer->id)],
            'created_date' => 'required|date',
            'dia_chi'      => 'nullable|string|max:500',
            'nguoi_lien_he'=> 'nullable|string|max:150',
            'sdt'          => 'nullable|string|max:20',
            'dia_chi_nhan' => 'nullable|string|max:500',
            'sdt_nhan'     => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:150',
            'khu_vuc'      => 'nullable|string',
            'ghi_chu'      => 'nullable|string',
        ]);

        $customer->update($data);
        LogService::log('Sửa khách hàng', "Sửa KH [{$customer->ma_kh}]");

        return response()->json(['success' => true, 'message' => 'Cập nhật thành công!']);
    }

    public function destroy(Customer $customer)
    {
        if ($customer->orders()->exists()) {
            return response()->json(['success' => false, 'message' => 'Không thể xóa vì khách hàng đã có đơn hàng!']);
        }
        LogService::log('Xóa khách hàng', "Xóa KH [{$customer->ma_kh}] {$customer->ten_cty}");
        $customer->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa khách hàng!']);
    }

    public function checkDuplicate(Request $request)
    {
        $tenCty    = $request->get('ten_cty');
        $maSoThue  = $request->get('ma_so_thue');
        $excludeId = $request->get('exclude_id');

        $q1 = Customer::where('ten_cty', $tenCty);
        $q2 = Customer::where('ma_so_thue', $maSoThue);

        if ($excludeId) {
            $q1->where('id', '!=', $excludeId);
            $q2->where('id', '!=', $excludeId);
        }

        return response()->json([
            'ten_cty_exists'   => $q1->exists(),
            'ma_so_thue_exists' => $q2->exists(),
        ]);
    }
}
