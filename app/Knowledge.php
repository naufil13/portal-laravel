<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Knowledge extends Model
{
    //protected $perPage = 15;
    //
    protected $guarded = [];
    protected $table = 'knowledges';
    public $timestamps = false;

    public function usertype()
    {
        return $this->hasOne('App\UserType', 'id', 'user_type_id');
    }
}
