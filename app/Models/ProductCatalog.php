<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCatalog extends Model
{
    protected $table = 'product_catalog';

    protected $fillable = [
        'ma_hang', 'ten_hang', 'category_id', 'quy_cach',
        'don_vi_tinh', 'gia_ban', 'gia_nhap', 'vat',
        'nha_cung_cap', 'ma_ncc', 'trang_thai', 'ghi_chu',
    ];

    protected $casts = [
        'gia_ban' => 'float',
        'gia_nhap' => 'float',
        'vat' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
