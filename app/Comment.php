<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $guarded = [];

    public function projects(){
        return $this->morphedByMany('App\Project','commentables');
    }

    public function tasks(){
        return $this->morphedByMany('App\Task','commentables');
    }

    public function users(){
        return $this->belongsTo('App\User','user_id');
    }
}
