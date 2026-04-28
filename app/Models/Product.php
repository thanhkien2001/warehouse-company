<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'ma_hang', 'ten_hang', 'mo_ta', 'so_luong_nhap',
        'don_vi_tinh', 'don_gia', 'ghi_chu', 'trang_thai', 'nhap_date',
        'category_id'
    ];

    protected $casts = ['nhap_date' => 'date'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
