<?php

namespace App\Services;

use App\Models\Product;
use App\Models\OrderItem;
use App\Models\DeliveryNote;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Tính tồn kho cho TẤT CẢ sản phẩm
     * Tồn = Σ Nhập - Σ Xuất (chỉ tính DN đã giao xong)
     */
    public static function getReport(): array
    {
        // Tổng nhập theo mã hàng
        $nhap = Product::select('ma_hang', 'ten_hang',
                DB::raw('SUM(so_luong_nhap) as tong_nhap'),
                DB::raw('MAX(nhap_date) as ngay_cap_nhat'),
                DB::raw('MAX(don_vi_tinh) as don_vi_tinh'),
                DB::raw('MAX(id) as last_id'))
            ->whereNotNull('ma_hang')
            ->groupBy('ma_hang', 'ten_hang')
            ->get()
            ->keyBy('ma_hang');

        // Tổng xuất (chỉ DN đã giao xong)
        $deliveredCtos = DeliveryNote::where('trang_thai', 'Đã giao xong')
            ->pluck('cto_code')
            ->toArray();

        $xuat = collect();
        if (!empty($deliveredCtos)) {
            $xuat = OrderItem::select('ma_hang', DB::raw('SUM(so_luong) as tong_xuat'))
                ->whereIn('cto_code', $deliveredCtos)
                ->whereNotNull('ma_hang')
                ->groupBy('ma_hang')
                ->get()
                ->keyBy('ma_hang');
        }

        $result = [];
        foreach ($nhap as $maHang => $row) {
            $tongXuat = $xuat->get($maHang)?->tong_xuat ?? 0;
            $result[] = [
                'ma_hang'      => $maHang,
                'ten_hang'     => $row->ten_hang,
                'don_vi_tinh'  => $row->don_vi_tinh,
                'tong_nhap'    => (float) $row->tong_nhap,
                'tong_xuat'    => (float) $tongXuat,
                'con_lai'      => (float) $row->tong_nhap - (float) $tongXuat,
                'ngay_cap_nhat' => $row->ngay_cap_nhat,
            ];
        }

        // Thêm hàng chỉ có xuất mà chưa có nhập (edge case)
        foreach ($xuat as $maHang => $row) {
            if (!isset($nhap[$maHang])) {
                $result[] = [
                    'ma_hang'       => $maHang,
                    'ten_hang'      => $maHang,
                    'don_vi_tinh'   => '',
                    'tong_nhap'     => 0,
                    'tong_xuat'     => (float) $row->tong_xuat,
                    'con_lai'       => -(float) $row->tong_xuat,
                    'ngay_cap_nhat' => null,
                ];
            }
        }

        return $result;
    }

    /**
     * Lấy tồn kho của 1 mã hàng cụ thể
     */
    public static function getStock(string $maHang): float
    {
        $nhap = Product::where('ma_hang', $maHang)->sum('so_luong_nhap');

        $deliveredCtos = DeliveryNote::where('trang_thai', 'Đã giao xong')
            ->pluck('cto_code')->toArray();

        $xuat = 0;
        if (!empty($deliveredCtos)) {
            $xuat = OrderItem::whereIn('cto_code', $deliveredCtos)
                ->where('ma_hang', $maHang)
                ->sum('so_luong');
        }

        return max(0, (float)$nhap - (float)$xuat);
    }

    /**
     * Lấy map tất cả tồn kho [maHang => conLai]
     */
    public static function getAllStockMap(): array
    {
        $report = self::getReport();
        $map = [];
        foreach ($report as $item) {
            $map[$item['ma_hang']] = $item['con_lai'];
        }
        return $map;
    }
}
