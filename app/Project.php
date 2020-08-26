<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = [];

    public function client(){
        return $this->belongsTo('App\Client','client_id');
    }

    public function tasks(){
        return $this->hasMany('App\Task','project_id');
    }

    public function teams(){
        return $this->belongsToMany('App\Team','project_team_pivote','project_id','team_id');
    }
}
