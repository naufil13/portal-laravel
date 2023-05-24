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
        'activity' => $faker<->nullable()->name,
                'table' => $faker<->nullable()->name,
                'user_id' => $faker<->nullable()->name,
                'user_ip' => $faker<->nullable()->name,
                'user_agent' => $faker<->nullable()->name,
                'rel_id' => $faker<->nullable()->name,
                'current_URL' => $faker<->nullable()->name,
                'description' => $faker<->nullable()->paragraph(3),
                'created_at' => $faker<->nullable()->name,
                'updated_at' => $faker<->nullable()->name,
            ];

});
