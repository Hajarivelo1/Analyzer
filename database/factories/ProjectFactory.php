<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1, // ou Auth::id() si tu veux le rendre dynamique
            'name' => $this->faker->company,
            'base_url' => $this->faker->url,
            'description' => $this->faker->sentence,
            'target_keywords' => implode(',', $this->faker->words(3)),
            'is_active' => true,
            'enable_monitoring' => $this->faker->boolean,
            'analysis_frequency' => $this->faker->randomElement(['weekly', 'monthly', 'quarterly']),
            'competitor_analysis' => $this->faker->randomElement(['basic', 'advanced', 'none']),

        ];
    }
}
