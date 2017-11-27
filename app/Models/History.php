<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function question()
    {
        return $this->belongsTo('App\Models\Question', 'question_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }
}
