<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyType extends Model
{
    protected $guarded = [];

    public function users(){
        return $this->hasMany('App\User','company_type');
    }
}
