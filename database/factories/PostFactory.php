<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Posts::class, function (Faker $faker) {
    $id = 1;

    return [
        'title' => $faker->unique()->title,
        'body' => $faker->text(),
        'author_id' => $id,
        'slug' => $faker->text,
        'active' => $faker->boolean,
        'id' => $id,
        'post_id' => $id
    ];
});
