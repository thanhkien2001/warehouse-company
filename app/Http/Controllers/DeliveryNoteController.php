<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\Order;
use App\Models\Customer;
use App\Services\CodeGeneratorService;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryNoteController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryNote::with('customer');

        $filter = $request->get('filter', 'all');
        if ($filter === '7days') {
            $query->where('delivery_date', '>=', now()->subDays(7));
        } elseif ($filter === 'month') {
            $query->whereMonth('delivery_date', now()->month)->whereYear('delivery_date', now()->year);
        } elseif ($filter === 'year') {
            $query->whereYear('delivery_date', now()->year);
        } elseif ($filter === 'custom' && $request->date_start && $request->date_end) {
            $query->whereBetween('delivery_date', [$request->date_start, $request->date_end]);
        }

        if ($kw = $request->get('search')) {
            $query->where(function($q) use ($kw) {
                $q->where('dn_code', 'like', "%{$kw}%")
                  ->orWhere('cto_code', 'like', "%{$kw}%")
                  ->orWhere('ten_kh', 'like', "%{$kw}%")
                  ->orWhere('ma_kh', 'like', "%{$kw}%");
            });
        }

        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'az'      => $query->orderBy('ten_kh'),
            'za'      => $query->orderByDesc('ten_kh'),
            'oldest'  => $query->orderBy('delivery_date'),
            default   => $query->orderByDesc('delivery_date')->orderByDesc('id'),
        };

        $limit = $request->get('limit', 20);
        $deliveries = $query->paginate($limit)->withQueryString();
        $availableOrders = Order::whereNotIn('trang_thai', ['Đã hủy'])
            ->orderByDesc('order_date')
            ->get(['id','cto_code','ma_kh','ten_kh']);

        return view('delivery-notes.index', compact('deliveries', 'filter', 'sort', 'availableOrders'));
    }

    public function show(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load(['order.items', 'customer']);
        return view('delivery-notes.show', ['delivery' => $deliveryNote]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cto_code'        => 'required|string|exists:orders,cto_code',
            'han_thanh_toan'  => 'nullable|integer|min:0',
            'delivery_date'   => 'required|date',
        ]);

        // Kiểm tra đã có phiếu chưa
        if (DeliveryNote::where('cto_code', $data['cto_code'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng này đã có phiếu giao!']);
        }

        $order   = Order::where('cto_code', $data['cto_code'])->firstOrFail();
        $dnCode  = CodeGeneratorService::generateDnCode($order->ma_kh);
        $user    = Auth::user();

        $dn = DeliveryNote::create([
            'dn_code'         => $dnCode,
            'order_id'        => $order->id,
            'cto_code'        => $order->cto_code,
            'customer_id'     => $order->customer_id,
            'ma_kh'           => $order->ma_kh,
            'ten_kh'          => $order->ten_kh,
            'trang_thai'      => 'Chờ giao hàng',
            'han_thanh_toan'  => $data['han_thanh_toan'] ?? 0,
            'nguoi_tao'       => $user->display_name ?? $user->username,
            'delivery_date'   => $data['delivery_date'],
        ]);

        // Đồng bộ CTO → Đang vận chuyển
        $order->update(['trang_thai' => 'Đang vận chuyển']);

        LogService::log('Tạo phiếu giao', "Tạo phiếu [{$dnCode}] cho đơn [{$order->cto_code}]");

        return response()->json(['success' => true, 'message' => "Tạo phiếu [{$dnCode}] thành công!", 'dn_code' => $dnCode]);
    }

    public function updateStatus(Request $request, DeliveryNote $deliveryNote)
    {
        $request->validate(['trang_thai' => 'required|string']);

        $deliveryNote->update(['trang_thai' => $request->trang_thai]);

        // Đồng bộ trạng thái CTO
        $order = Order::where('cto_code', $deliveryNote->cto_code)->first();
        if ($order) {
            $newOrderStatus = match ($request->trang_thai) {
                'Đang giao'    => 'Đang vận chuyển',
                'Đã giao xong' => 'Hoàn thành',
                'Đã hủy'       => 'Đã hủy',
                default        => $order->trang_thai,
            };
            $order->update(['trang_thai' => $newOrderStatus]);
        }

        LogService::log('Cập nhật phiếu giao', "Phiếu [{$deliveryNote->dn_code}] → {$request->trang_thai}");

        return response()->json(['success' => true, 'message' => 'Đã cập nhật trạng thái!']);
    }

    public function destroy(DeliveryNote $deliveryNote)
    {
        LogService::log('Xóa phiếu giao', "Xóa phiếu [{$deliveryNote->dn_code}]");
        $deliveryNote->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa phiếu giao hàng!']);
    }
}
