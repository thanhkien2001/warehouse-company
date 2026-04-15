<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogService
{
    public static function log(string $action, string $detail = ''): void
    {
        ActivityLog::create([
            'user_id' => Auth::user() ? Auth::user()->id : null,
            'action'  => $action,
            'detail'  => $detail,
        ]);
    }
}
