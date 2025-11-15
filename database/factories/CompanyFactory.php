<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'size' => fake()->randomElement(['1-10', '11-50', '51-200', '201-1000', '1000+']),
            'industry' => fake()->randomElement([
                'Technology',
                'Healthcare',
                'Finance',
                'Education',
                'Retail',
                'Manufacturing',
                'Marketing',
                'Consulting',
                'Other',
            ]),
        ];
    }
}
