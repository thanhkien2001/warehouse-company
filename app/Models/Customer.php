<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'ma_kh', 'ten_cty', 'ma_so_thue', 'dia_chi', 'nguoi_lien_he',
        'sdt', 'dia_chi_nhan', 'sdt_nhan', 'email', 'khu_vuc', 'ghi_chu', 'created_date',
    ];

    protected $casts = ['created_date' => 'date'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function deliveryNotes()
    {
        return $this->hasMany(DeliveryNote::class);
    }
}
