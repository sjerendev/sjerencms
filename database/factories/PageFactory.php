<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(2, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'language' => 'sv',
            'is_published' => true,
            'is_homepage' => false,
            'content' => [],
        ];
    }

    public function homepage(): static
    {
        return $this->state(fn (array $attributes) => ['is_homepage' => true]);
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => ['is_published' => false]);
    }

    public function english(): static
    {
        return $this->state(fn (array $attributes) => ['language' => 'en']);
    }
}
