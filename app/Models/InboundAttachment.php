<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InboundAttachment extends Model
{
    protected $fillable = [
        'receipt_id', 'original_name', 'stored_name', 'file_path', 'mime_type', 'file_size',
    ];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(InboundReceipt::class, 'receipt_id');
    }

    /** Icon class based on mime_type */
    public function getIconAttribute(): string
    {
        if (str_contains($this->mime_type ?? '', 'pdf')) return 'fa-file-pdf text-danger';
        if (str_contains($this->mime_type ?? '', 'image')) return 'fa-file-image text-success';
        if (str_contains($this->mime_type ?? '', 'excel') || str_contains($this->mime_type ?? '', 'spreadsheet')) return 'fa-file-excel text-success';
        return 'fa-file text-secondary';
    }
}
