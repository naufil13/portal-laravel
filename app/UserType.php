<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserType extends Model
{
    //protected $perPage = 15;

    protected $guarded = [];

    /**
     * The applications that belong to the UserType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(Application::class, ApplicationRole::class, 'application_id', 'allowed_role_id');
    }
}
