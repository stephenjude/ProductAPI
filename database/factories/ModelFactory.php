<?php


$factory->define(App\Product::class, function (Faker\Generator $faker) {
    return [
        'product_name' => $faker->word,
        'product_description' => $faker->text,
    ];
});
