<?php

namespace App;

//use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];

    public function friends(){
        return $this->morphedByMany('App\User','messagable');
    }

    public function teams(){
        return $this->morphedByMany('App\Team','messagable');
    }

}
