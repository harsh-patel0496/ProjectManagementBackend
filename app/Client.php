<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;

class Client extends Model
{
    protected $table = 'users';
    
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


    public function scopeRole($query)
    {
        return $query->where('role', 1);
    }
    
    public function users(){
        return $this->belongsTo('App\User','parent_user');
    }

    public function projects(){
        return $this->hasMany('App\Project','client_id');
    }

}
