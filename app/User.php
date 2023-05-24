<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    /*protected $fillable = [
        'name', 'email', 'password',
    ];*/

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

    public function usertype()
    {
        return $this->hasOne('App\UserType', 'id', 'user_type_id');
    }

    public function userclient()
    {
        return $this->hasOne('App\Tenant', 'id', 'tenants_id');
    }

    public function modules()
    {
        $SQL = $this->join('user_type_module_rel', 'user_type_module_rel.user_type_id', '=', 'users.id')
            ->join('modules', 'modules.id', '=', 'user_type_module_rel.module_id');
        return $SQL;
    }
}
