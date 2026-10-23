<?php

namespace Database\Factories;

use App\Models\Release;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Track>
 */
class TrackFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'release_id' => Release::factory(),
            'position' => 1,
            'title' => fake()->words(2, true),
            'duration_seconds' => 300,
        ];
    }
}
