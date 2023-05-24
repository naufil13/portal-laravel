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
                'designation' => $faker<->nullable()->name,
                'emails' => $faker<->nullable()->name,
                'phones' => $faker<->nullable()->name,
                'comment' => $faker<->nullable()->name,
                'created_at' => $faker<->nullable()->name,
                'updated_at' => $faker<->nullable()->name,
            ];

});
