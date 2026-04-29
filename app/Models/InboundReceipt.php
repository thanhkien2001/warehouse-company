<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InboundReceipt extends Model
{
    protected $fillable = [
        'receipt_code', 'invoice_no', 'receipt_date', 'invoice_date',
        'supplier_name', 'supplier_code', 'warehouse', 'origin',
        'department', 'created_by', 'notes', 'status', 'total_amount',
    ];

    protected $casts = [
        'receipt_date'  => 'date',
        'invoice_date'  => 'date',
        'total_amount'  => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InboundItem::class, 'receipt_id')->orderBy('sort_order');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(InboundAttachment::class, 'receipt_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /** Auto-generate receipt code: NK-YYYY-NNNNN */
    public static function generateCode(): string
    {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->orderByDesc('id')->lockForUpdate()->first();
        $seq  = $last ? ((int) substr($last->receipt_code, -5) + 1) : 1;
        return 'NK-' . $year . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }
}
