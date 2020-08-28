<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function companyType(){
        return $this->belongsTo('App\CompanyType','company_type');
    }

    public function clients(){
        return $this->hasMany('App\Client','parent_user');
    }

    public function employee(){
        return $this->hasMany('App\Employee','parent_user');
    }

    public function teams(){
        return $this->hasMany('App\Team','company_id');
    }

    public function managers(){
        return $this->hasMany('App\Managers','parent_user');
    }

    public function developers(){
        return $this->hasMany('App\Developer','parent_user');
    }

    public function projects(){
        return $this->hasMany('App\Project','company_id');
    }

    public function comments(){
        return $this->hasMany('App\Comment','user_id');
    }

    public function tasks(){
        return $this->hasMany('App\Task','company_id');
    }

    public function createdTasks(){
        return $this->hasMany('App\Task','created_by');
    }
}
