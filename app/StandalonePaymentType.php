<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StandalonePaymentType extends Model
{
    //protected $perPage = 15;
    //
    protected $guarded = [];
    public $timestamps = false;

    public function paymentDetails()
    {
        return $this->hasMany('App\PaymentDetail', 'type', 'payment_type');
    }
}
