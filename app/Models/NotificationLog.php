<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'type',
        'recipient',
        'message',
        'status',
        'response',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
