<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryView extends Model
{
    protected $table = 'history_view';

    public $timestamps = false;

    protected $fillable = [
        'interaction_id',
        'user_id',
        'request_id',
        'username',
        'input_text',
        'created_at',
        'final_label',
        'final_confidence',
        'status'
    ];

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'id');
    }

    /**
     * Relasi ke model Requests
     */
    public function request()
    {
        return $this->belongsTo(Requests::class, 'request_id', 'id');
    }

    /**
     * Relasi ke model UserInteractions
     */
    public function interaction()
    {
        return $this->belongsTo(UserInteractions::class, 'interaction_id', 'id');
    }

        public function image()
    {
        return $this->belongsTo(Images::class);
    }

    public function stage1Results()
    {
        return $this->hasMany(Stage1Results::class);
    }

    public function stage2Results()
    {
        return $this->hasMany(Stage2Result::class);
    }

    public function imageSearchResults()
    {
        return $this->hasMany(ImageSearchResults::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedbacks::class);
    }
}