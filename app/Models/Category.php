<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    public function questions()
    {
        return $this->hasMany('App\Models\Question', 'category_id');
    }
}
