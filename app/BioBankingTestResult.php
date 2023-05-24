<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BioBankingTestResult extends Model
{
    protected $fillable = ['name'];

    public function childs()
    {
        return $this->hasMany(BioBankingTestResultChild::class, 'parent_id', 'id');
    }
}
