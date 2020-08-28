<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Developer extends Model
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

    // public function apply(Builder $builder, Model $model)
    // {
    //     $builder->where('role',2);
    // }

    public function scopeHavingRole($query, $role)
    {
        return $query->where('role', $role);
    }
    
    public function users(){
        return $this->belongsTo('App\User','parent_user');
    }

    public function teams(){
        return $this->morphToMany('App\Team','assignable_team');
    }

    public function tasks(){
        return $this->morphToMany('App\Task','assignable_tasks');
    }
}
