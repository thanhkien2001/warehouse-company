<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'cto_code', 'ma_hang', 'mo_ta', 'ten_hang', 'mo_ta_phu',
        'so_luong', 'don_vi_tinh', 'don_gia', 'thanh_tien',
        'ma_lot', 'han_su_dung', 'quy_cach', 'quy_doi', 'ghi_chu', 'sort_order',
    ];

    protected $casts = ['han_su_dung' => 'date'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
