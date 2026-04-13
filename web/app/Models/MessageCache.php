<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageCache extends Model
{
    protected $table = 'message_cache';

    protected $fillable = [
        'sender_number',
        'latest_message'
    ];
}