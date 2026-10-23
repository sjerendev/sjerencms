<?php

namespace Database\Factories;

use App\Models\Release;
use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

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

    /** A fake master on the `music` disk; the test calls Storage::fake('music') first. */
    public function withWav(): static
    {
        return $this->afterCreating(fn (Track $track) => $track
            ->addMedia(UploadedFile::fake()->create('master.wav', 10, 'audio/wav'))
            ->toMediaCollection('wav'));
    }
}
