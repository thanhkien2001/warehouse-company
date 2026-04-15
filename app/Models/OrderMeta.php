<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderMeta extends Model
{
    protected $table = 'order_meta';
    public $timestamps = false;
    protected $fillable = [
        'order_id', 'cto_code', 'tinh_trang', 'ty_gia', 'ngay_ty_gia', 'vat_percent', 'pdf_url',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
