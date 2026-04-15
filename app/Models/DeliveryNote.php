<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    protected $fillable = [
        'dn_code', 'order_id', 'cto_code', 'customer_id', 'ma_kh', 'ten_kh',
        'trang_thai', 'han_thanh_toan', 'nguoi_tao', 'delivery_date', 'dn_pdf_url',
    ];

    protected $casts = ['delivery_date' => 'date'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getDeadlineAttribute(): ?\Carbon\Carbon
    {
        if (!$this->delivery_date || !$this->han_thanh_toan) return null;
        return $this->delivery_date->addDays($this->han_thanh_toan);
    }

    public function getDaysLeftAttribute(): ?int
    {
        $deadline = $this->deadline;
        if (!$deadline) return null;
        return (int) now()->startOfDay()->diffInDays($deadline, false);
    }
}
