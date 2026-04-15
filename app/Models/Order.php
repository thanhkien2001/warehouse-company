<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'cto_code', 'customer_id', 'ma_kh', 'ten_kh', 'ghi_chu',
        'trang_thai', 'nguoi_ban', 'sdt_ban', 'nguoi_mua', 'sdt_mua',
        'created_by', 'order_date',
    ];

    protected $casts = ['order_date' => 'date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class)->orderBy('sort_order');
    }

    public function meta()
    {
        return $this->hasOne(OrderMeta::class);
    }

    public function deliveryNote()
    {
        return $this->hasOne(DeliveryNote::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTotalAttribute(): float
    {
        return $this->items->sum('thanh_tien');
    }

    public function getTotalWithVatAttribute(): float
    {
        $vat = $this->meta->vat_percent ?? 8;
        return $this->total * (1 + $vat / 100);
    }
}
