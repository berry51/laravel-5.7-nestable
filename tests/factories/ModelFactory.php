<?php


$factory->define(Nestable\Tests\Model\Menu::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->sentence(2),
        'pid' => rand(0, 10),
        'url' => $faker->slug,
    ];
});
