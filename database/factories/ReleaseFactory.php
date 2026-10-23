<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Release>
 */
class ReleaseFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($title),
            'artist' => fake()->name(),
            'title' => $title,
            'released_on' => fake()->date(),
            'min_price' => 50,
            'is_published' => true,
        ];
    }
}
