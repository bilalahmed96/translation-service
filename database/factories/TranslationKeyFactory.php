<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TranslationKey>
 */
class TranslationKeyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tkey' => $this->faker->unique()->slug(3),
            'namespace' => $this->faker->randomElement(['web', 'app', 'admin']),
        ];
    }
}
