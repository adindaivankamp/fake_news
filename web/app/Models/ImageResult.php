<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageResult extends Model
{
    protected $fillable = ['link_img', 'title'];

    public function stage1Results()
    {
        return $this->hasMany(Stage1Results::class, 'image_results_id');
    }
}
