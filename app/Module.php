<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    //protected $perPage = 15;
    //
    protected $guarded = [];


    public function usertype() {
        return $this->hasOne('App\UserType', 'id', 'user_type_id');
    }

}
