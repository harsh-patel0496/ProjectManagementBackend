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

    public function projects(){
        return $this->hasMany('App\Project','team_id','project_id');
    }

    public function tasks(){
        return $this->hasMany('App\Task','team_id');
    }

    public function messages(){
        return $this->morphToMany('App\Message','messagable');
    }
}
