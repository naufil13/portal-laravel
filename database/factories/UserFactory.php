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
        'user_type_id' => $faker<->nullable()->name,
                'first_name' => $faker<->nullable()->firstName(),
                'last_name' => $faker<->nullable()->lastName,
                'email' => $faker<->nullable()->email,
                'phone' => $faker<->nullable()->phoneNumber,
                'address' => $faker<->nullable()->address,
                'city' => $faker<->nullable()->city,
                'photo' => $faker<->nullable()->image('assets/images/users', 640, 480) ,
                'status' => $faker<->nullable()->name,
                'email_verified_at' => $faker<->nullable()->name,
                'username' => $faker<->nullable()->unique()->name,
                'password' => $faker<->nullable()->unique()->password,
                'data' => $faker<->nullable()->unique()->name,
                'remember_token' => $faker<->nullable()->unique()->name,
                'created_at' => $faker<->nullable()->unique()->name,
                'updated_at' => $faker<->nullable()->unique()->name,
            ];

});
