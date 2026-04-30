<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextStage2Result extends Model
{
    protected $fillable = [
    'request_id', 
    'time_credibility', 
    'title_credibility', 
    'mean_entailment', 
    'mean_contradiction', 
    'std_contradiction', 
    'url'
];

protected $casts = [
    'url' => 'array',
];
}
