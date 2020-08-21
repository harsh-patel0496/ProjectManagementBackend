<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $guarded = [];

    public function developers(){
        return $this->morphedByMany('App\Developer','assignable_team');
    }

    public function managers(){
        return $this->morphedByMany('App\Managers','assignable_team');
    }
}