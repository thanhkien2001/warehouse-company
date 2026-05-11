<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\DeliveryNote;
use Carbon\Carbon;

class DebtService
{
    /**
     * Lấy toàn bộ công nợ (chỉ đơn có DN, còn lại > 0)
     */
    public static function getAll(): array
    {
        $dns = DeliveryNote::with(['order.items', 'order.meta', 'customer'])
            ->whereIn('trang_thai', ['Đã giao xong', 'Đang giao', 'Chờ giao hàng'])
            ->get();

        $result = [];
        foreach ($dns as $dn) {
            $order = $dn->order;
            if (!$order) continue;

            $tongItems = $order->items->sum('thanh_tien');
            $vat = $order->meta->vat_percent ?? 8;
            $tongDon = $tongItems * (1 + $vat / 100);

            $daTra = Payment::where('cto_code', $dn->cto_code)->sum('so_tien');
            $conLai = $tongDon - $daTra;

            if ($conLai < 1000) continue; // Bỏ qua nếu còn lại không đáng kể

            // Tính hạn
            $deadline = null;
            $soNgayHan = $dn->han_thanh_toan;
            $daysLeft = null;
            $tinhTrang = 'Chưa thiết lập hạn';

            if ($dn->delivery_date && $soNgayHan > 0) {
                $deadline = Carbon::parse($dn->delivery_date)->addDays($soNgayHan);
                $daysLeft = (int) now()->startOfDay()->diffInDays($deadline, false);

                if ($daysLeft < 0) {
                    $tinhTrang = 'Quá hạn ' . abs($daysLeft) . ' ngày';
                } elseif ($daysLeft === 0) {
                    $tinhTrang = 'Đến hạn hôm nay';
                } else {
                    $tinhTrang = 'Còn ' . $daysLeft . ' ngày';
                }
            }

            $result[] = [
                'cto_code'   => $dn->cto_code,
                'hd_code'    => $order->hd_code,
                'dn_code'    => $dn->dn_code,
                'ma_kh'      => $dn->ma_kh,
                'ten_kh'     => $dn->ten_kh,
                'tong_don'   => round($tongDon, 0),
                'da_tra'     => round($daTra, 0),
                'con_lai'    => round($conLai, 0),
                'ngay_giao'  => $dn->delivery_date?->format('d/m/Y'),
                'so_ngay_han' => $soNgayHan,
                'deadline'   => $deadline?->format('d/m/Y'),
                'days_left'  => $daysLeft,
                'tinh_trang' => $tinhTrang,
                'is_overdue' => isset($daysLeft) && $daysLeft < 0,
            ];
        }

        usort($result, fn($a, $b) => ($a['days_left'] ?? 999) <=> ($b['days_left'] ?? 999));

        return $result;
    }
}
