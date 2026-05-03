<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $table = 'knowledge_base';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'hoax_text',
        'fact_text',
        'category',
        'source_url',
        'url',
        'published_at'
    ];

    protected $casts = [
        'url' => 'array',
        'published_at' => 'date'
    ];

    public function stage1Results()
    {
        return $this->hasMany(Stage1Results::class, 'knowledge_id');
    }
}
