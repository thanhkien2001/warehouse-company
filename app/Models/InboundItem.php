<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InboundItem extends Model
{
    protected $fillable = [
        'receipt_id', 'product_catalog_id', 'ma_hang', 'ten_hang',
        'category_id', 'don_vi_tinh', 'quy_cach', 'so_luong',
        'don_gia', 'thanh_tien', 'so_lo', 'ngay_san_xuat',
        'han_su_dung', 'kho_nhap', 'ghi_chu', 'sort_order',
    ];

    protected $casts = [
        'ngay_san_xuat' => 'date',
        'han_su_dung'   => 'date',
        'so_luong'      => 'decimal:4',
        'don_gia'       => 'decimal:2',
        'thanh_tien'    => 'decimal:2',
    ];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(InboundReceipt::class, 'receipt_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductCatalog::class, 'product_catalog_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
