<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    //protected $perPage = 15;
    //
    protected $guarded = [];
    public $timestamps = false;

    // protected $fillable = [
    //     'client_name', 'login_code', 'client_email', 'client_phone',
    //     'client_tax_id', 'client_address', 'country', 'state', 'city',
    //     'zip_code', 'website', 'total_budget', 'per_site_limit', 'msa_expiration',
    //     'sow_expiration', 'client_tax_id_hash'
    // ];

    public function usertype() {
        return $this->hasOne('App\UserType', 'id', 'user_type_id');
    }

}
