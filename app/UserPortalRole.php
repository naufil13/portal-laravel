<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPortalRole extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'user_portal_role';
}
