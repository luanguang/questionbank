<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    protected $guarded = [];

    public function question()
    {
        return $this->belongsTo('App\Models\Question', 'question_id');
    }
}
