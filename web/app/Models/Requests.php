<?php

namespace App\Models;

use Dom\Text;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requests extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'input_text',
        'clean_text',
        'image_id',
        'final_label',
        'final_confidence',
        'status'
    ];

    public function image()
    {
        return $this->belongsTo(Images::class);
    }

    // Cari fungsi ini, lalu tambahkan 'request_id'
    public function stage1Results()
    {
        return $this->hasMany(Stage1Results::class, 'request_id');
    }

    // Cari fungsi ini juga, lalu tambahkan 'request_id'
    public function stage2Results()
    {
        return $this->hasMany(Stage2Result::class, 'request_id');
    }

    public function imageSearchResults()
    {
        return $this->hasMany(ImageSearchResults::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedbacks::class);
    }

    public function interactions()
    {
        return $this->hasMany(UserInteractions::class);
    }
}
