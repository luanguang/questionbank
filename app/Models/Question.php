<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function answers()
    {
        return $this->hasMany('App\Models\Choice', 'question_id');
    }
}
