<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppUserAuthentication extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'app_user_authentication';
}
