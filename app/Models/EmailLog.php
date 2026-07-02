<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'sender_name',
        'email_subject',
        'message_content',
        'sent_at',
        'recipient_count',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
