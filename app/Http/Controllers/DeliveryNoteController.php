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

        // 1. Time & Search Filters (Apply first for accurate counts)
        if ($request->date_start) {
            $query->whereDate('delivery_date', '>=', $request->date_start);
        }
        if ($request->date_end) {
            $query->whereDate('delivery_date', '<=', $request->date_end);
        }

        if ($kw = $request->get('search')) {
            $query->where(function($q) use ($kw) {
                $q->where('dn_code', 'like', "%{$kw}%")
                  ->orWhere('cto_code', 'like', "%{$kw}%")
                  ->orWhere('ten_kh', 'like', "%{$kw}%")
                  ->orWhere('ma_kh', 'like', "%{$kw}%");
            });
        }

        // 2. Counts by Status
        $countQuery = clone $query;
        $counts = $countQuery->select('trang_thai', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
            ->groupBy('trang_thai')
            ->pluck('total', 'trang_thai')
            ->toArray();
        $counts['all'] = array_sum($counts);

        // 3. Status Filter
        if ($status = $request->get('status')) {
            if ($status !== 'all') {
                $query->where('trang_thai', $status);
            }
        }

        // 4. Sort & Paginate
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
            ->whereDoesntHave('deliveryNote')
            ->orderByDesc('order_date')
            ->get(['id','cto_code','ma_kh','ten_kh']);


        return view('delivery-notes.index', compact('deliveries', 'counts', 'sort', 'availableOrders'));
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

    public function saveItems(Request $request, DeliveryNote $deliveryNote)
    {
        $request->validate([
            'items' => 'required|array',
            'trang_thai' => 'nullable|string',
        ]);

        if ($request->trang_thai) {
            $deliveryNote->update(['trang_thai' => $request->trang_thai]);
            // Đồng bộ trạng thái CTO
            $order = $deliveryNote->order;
            if ($order) {
                $newOrderStatus = match ($request->trang_thai) {
                    'Đang giao'    => 'Đang vận chuyển',
                    'Đã giao xong' => 'Hoàn thành',
                    'Đã hủy'       => 'Đã hủy',
                    default        => $order->trang_thai,
                };
                $order->update(['trang_thai' => $newOrderStatus]);
            }
        }

        foreach ($request->items as $itemData) {
            if (isset($itemData['id'])) {
                $item = \App\Models\OrderItem::find($itemData['id']);
                if ($item && $item->order_id == $deliveryNote->order_id) {
                    $item->update([
                        'ma_lot'      => $itemData['ma_lot'] ?? null,
                        'han_su_dung' => !empty($itemData['han_su_dung']) ? $itemData['han_su_dung'] : null,
                        'quy_cach'    => $itemData['quy_cach'] ?? null,
                        'quy_doi'     => (float)($itemData['quy_doi'] ?? 0),
                        'ghi_chu'     => $itemData['ghi_chu'] ?? null,
                    ]);
                }
            }
        }

        LogService::log('Cập nhật hàng hóa phiếu giao', "Phiếu [{$deliveryNote->dn_code}] - Cập nhật số lô, hsd...");

        return response()->json(['success' => true, 'message' => 'Đã lưu thông tin phiếu giao thành công!']);
    }

    public function destroy(DeliveryNote $deliveryNote)
    {
        LogService::log('Xóa phiếu giao', "Xóa phiếu [{$deliveryNote->dn_code}]");
        $deliveryNote->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa phiếu giao hàng!']);
    }
}
