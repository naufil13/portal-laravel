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
        'url' => $faker->unique()->name,
        'title' => $faker->name,
        'content' => $faker->text,
        'thumbnail' => $faker->imageUrl(1200, 250),
        'ordering' => random_int(1, 100),
        'meta_title' => $faker->name, // secret
    ];

});
