<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $client = fake()->unique()->company();

        return [
            'slug' => Str::slug($client),
            'client' => $client,
            'headline_sv' => fake()->sentence(),
            'headline_en' => fake()->sentence(),
            'services' => ['Wordpress'],
            'url' => 'https://'.Str::slug($client).'.se',
            'rebuilt' => false,
            'position' => fake()->unique()->numberBetween(1, 1000),
        ];
    }

    public function rebuilt(): static
    {
        return $this->state(['rebuilt' => true]);
    }

    public function withRole(): static
    {
        return $this->state(['role_sv' => 'Design & frontend', 'role_en' => 'Design & frontend']);
    }
}
