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
        'task' => $faker<->nullable()->name,
                'project_id' => $faker<->nullable()->name,
                'start_date' => $faker<->nullable()->name,
                'end_date' => $faker<->nullable()->name,
                'estimate_time' => $faker<->nullable()->name,
                'status' => $faker<->nullable()->name,
                'creator_id' => $faker<->nullable()->name,
                'assign_to' => $faker<->nullable()->name,
                'description' => $faker<->nullable()->paragraph(3),
                'created_at' => $faker<->nullable()->name,
                'updated_at' => $faker<->nullable()->name,
            ];

});
