<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EproParticipant extends Model
{
    protected $table = "users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
          "user_type_id",
          "first_name",
          "last_name",
          "email",
          "username",
          "tenants_id",
          "is_standalone",
          "login_code"
    ];

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
