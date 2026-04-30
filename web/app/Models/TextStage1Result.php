<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextStage1Result extends Model
{
    public function imageResult()
{
    // Nama kolom foreign key-nya 'image_results_id'
    return $this->belongsTo(ImageResult::class, 'image_results_id');
}
}
