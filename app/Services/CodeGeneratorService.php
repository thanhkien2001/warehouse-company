<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\DeliveryNote;
use App\Models\Payment;

class CodeGeneratorService
{
    /**
     * Sinh mã khách hàng: YY + MM + Last3(MST) + Seq3
     * VD: 2604242001
     */
    public static function generateMaKH(string $maSoThue, ?string $date = null): string
    {
        $d = $date ? \Illuminate\Support\Carbon::parse($date) : now();
        $yymm = $d->format('ym'); // YYMM VD: 2604
        $last3mst = substr(preg_replace('/\D/', '', $maSoThue), -3);
        if (strlen($last3mst) < 3) $last3mst = str_pad($last3mst, 3, '0', STR_PAD_LEFT);

        // Tìm tất cả KH trong cùng tháng để lấy sequence lớn nhất hiện tại
        $maxSeq = 0;
        $customersInMonth = Customer::where('ma_kh', 'like', $yymm . '%')->select('ma_kh')->get();
        
        foreach ($customersInMonth as $c) {
            // Lấy 3 số cuối của mã KH làm số thứ tự
            $s = (int) substr($c->ma_kh, -3);
            if ($s > $maxSeq) $maxSeq = $s;
        }

        $seq = $maxSeq + 1;

        return $yymm . $last3mst . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Sinh mã đơn hàng CTO: CTO-{10 ký tự MaKH}{Seq3}
     */
    public static function generateCtoCo(string $maKH): string
    {
        $prefix = 'CTO-' . $maKH;

        $latest = Order::where('cto_code', 'like', $prefix . '%')
            ->orderBy('cto_code', 'desc')
            ->value('cto_code');

        $seq = 1;
        if ($latest) {
            $seq = (int) substr($latest, -3) + 1;
        }

        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Sinh mã phiếu giao DN: DN-{7 ký tự MaKH}-{Seq3}
     */
    public static function generateDnCode(string $maKH): string
    {
        $prefix7 = substr($maKH, 0, 7);
        $prefix  = 'DN-' . $prefix7 . '-';

        $latest = DeliveryNote::where('dn_code', 'like', $prefix . '%')
            ->orderBy('dn_code', 'desc')
            ->value('dn_code');

        $seq = 1;
        if ($latest) {
            $seq = (int) substr($latest, -3) + 1;
        }

        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Sinh mã thanh toán: TT-{MM}{YY}-{Seq3}
     */
    public static function generateMaTT(): string
    {
        $prefix = 'TT-' . now()->format('my') . '-';

        $latest = Payment::where('ma_tt', 'like', $prefix . '%')
            ->orderBy('ma_tt', 'desc')
            ->value('ma_tt');

        $seq = 1;
        if ($latest) {
            $seq = (int) substr($latest, -3) + 1;
        }

        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Sinh mã hàng: YYMM + Seq3
     */
    public static function generateMaHang(): string
    {
        $prefix = now()->format('ym');

        // Tìm số lớn nhất có cùng prefix trong bảng products
        $latest = \App\Models\Product::where('ma_hang', 'like', $prefix . '%')
            ->orderBy('ma_hang', 'desc')
            ->value('ma_hang');

        $seq = 1;
        if ($latest && strlen($latest) >= strlen($prefix) + 3) {
            $seq = (int) substr($latest, -3) + 1;
        }

        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
}
