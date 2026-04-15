<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'ma_tt', 'order_id', 'cto_code', 'customer_id', 'ma_kh',
        'so_tien', 'nguoi_thu', 'ghi_chu', 'payment_date',
    ];

    protected $casts = ['payment_date' => 'datetime', 'created_at' => 'datetime'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
