<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Message extends Model
{
    protected $connection = 'mongodb';
    protected $guarded = [];

    public function friends(){
        return $this->morphedByMany('App\User','messagable');
    }

    public function teams(){
        return $this->morphedByMany('App\Teams','messagable');
    }

}
