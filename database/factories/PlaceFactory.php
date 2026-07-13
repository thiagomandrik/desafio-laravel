<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PlaceFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Praia Mole',
            'Praia da Joaquina',
            'Praia do Campeche',
            'Lagoinha do Leste',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'city' => 'Florianópolis',
            'state' => 'SC',
        ];
    }
}
