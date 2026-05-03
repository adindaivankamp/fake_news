<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $casts = [
    'url' => 'array', // Biar data JSON di DB langsung jadi array PHP
];
}
