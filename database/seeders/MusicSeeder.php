<?php

namespace Database\Seeders;

use App\Models\Release;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * The 22 releases from Bandcamp, with covers and tracks but no audio:
 *
 *     php artisan db:seed --class=MusicSeeder
 *
 * Aliases and titles come from music-names.json (the list artboard), not
 * from the raw Bandcamp names, which carry zero-width spaces and prefixes.
 * Safe to run again: releases and tracks are matched on slug and position,
 * and a cover is only added when one is missing.
 */
class MusicSeeder extends Seeder
{
    public function run(): void
    {
        $rows = json_decode(file_get_contents(database_path('seeders/data/music.json')), true);
        $names = json_decode(file_get_contents(database_path('seeders/data/music-names.json')), true);

        foreach ($rows as $row) {
            $slug = Str::afterLast($row['path'], '/');
            [$artist, $title] = $names[$slug];

            $release = Release::updateOrCreate(['slug' => $slug], [
                'artist' => $artist,
                'title' => $title,
                'released_on' => Carbon::parse($row['date'])->toDateString(),
                'min_price' => (int) $row['min'],
                'is_published' => true,
            ]);

            if (! $release->getFirstMedia('cover')) {
                $release->addMedia(database_path("seeders/data/covers/{$slug}.jpg"))
                    ->preservingOriginal()
                    ->toMediaCollection('cover');
            }

            foreach ($row['tracks'] as $i => [$trackTitle, $seconds]) {
                // 20 kr is Bandcamp's minimum per track (spec, decision 8).
                $release->tracks()->updateOrCreate(['position' => $i + 1], [
                    'title' => $trackTitle,
                    'duration_seconds' => $seconds,
                    'min_price' => 20,
                ]);
            }
        }
    }
}
