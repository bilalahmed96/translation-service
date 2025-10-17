<?php

namespace Database\Factories;

use App\Models\TranslationKey;
use Illuminate\Database\Eloquent\Factories\Factory;
use Locale;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'translation_key_id' => TranslationKey::inRandomOrder()->first()->id ?? TranslationKey::factory(),
            'locale_id' => Locale::inRandomOrder()->first()->id ?? Locale::factory(),
            'content' => $this->faker->sentence(),
            'context' => [
                'source' => $this->faker->randomElement(['web', 'api', 'mobile']),
                'version' => $this->faker->numberBetween(1, 5),
            ],
        ];
    }
}
