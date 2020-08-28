<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];

    public function developers(){
        return $this->morphedByMany('App\Developer','assignable_task');
    }

    public function managers(){
        return $this->morphedByMany('App\Managers','assignable_task');
    }

    public function projects(){
        return $this->belongsTo('App\Project','project_id');
    }

    public function teams(){
        return $this->belongsTo('App\Team','team_id');
    }

    public function createdBy(){
        return $this->belongsTo('App\User','created_by');
    }

    public function company(){
        return $this->belongsTo('App\User','company_id');
    }

}
