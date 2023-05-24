<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Page;
use Faker\Generator as Faker;

/*
 * ---------------------------------------------------------------------------------------------------------
 * Run: php artisan tinker
 * factory(App\Page::class, 50)->create();
 * ---------------------------------------------------------------------------------------------------------
 */

$factory->define(Page::class, function (Faker $faker) {
    return [
        'name' => $faker<->nullable()->name,
                'logo' => $faker<->nullable()->image('assets/images/members', 640, 480) ,
                'joining_date' => $faker<->nullable()->name,
                'website' => $faker<->nullable()->name,
                'address' => $faker<->nullable()->address,
                'country' => $faker<->nullable()->country,
                'city' => $faker<->nullable()->city,
                'emails' => $faker<->nullable()->name,
                'phones' => $faker<->nullable()->name,
                'created_at' => $faker<->nullable()->name,
                'updated_at' => $faker<->nullable()->name,
            ];

});
