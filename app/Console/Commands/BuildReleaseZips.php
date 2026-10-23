<?php

namespace App\Console\Commands;

use App\Models\Release;
use App\Models\Track;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ZipArchive;

#[Signature('music:build-zips')]
#[Description('Build the album ZIP of every release whose tracks all have a WAV and whose ZIP is missing or stale')]
class BuildReleaseZips extends Command
{
    public function handle(): int
    {
        $releases = Release::with('tracks.media')->get()->filter(
            fn (Release $release) => $release->tracks->isNotEmpty()
                && $release->tracks->every(fn (Track $track) => $track->getFirstMedia('wav')),
        );

        foreach ($releases as $release) {
            $fingerprint = sha1($release->tracks->map(
                fn (Track $track) => "{$track->id}:{$track->getFirstMedia('wav')->id}:{$track->position}:{$track->title}",
            )->implode("\n"));

            if ($release->getFirstMedia('zip')?->getCustomProperty('fingerprint') === $fingerprint) {
                continue;
            }

            $this->build($release, $fingerprint);
            $this->line("Built {$release->slug}.zip");
        }

        return self::SUCCESS;
    }

    private function build(Release $release, string $fingerprint): void
    {
        $dir = storage_path('app/tmp');
        File::ensureDirectoryExists($dir);
        $tmp = "{$dir}/release-{$release->id}.zip";
        File::delete($tmp);

        $zip = new ZipArchive;
        $zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($release->tracks as $track) {
            $name = sprintf('%02d %s.wav', $track->position, preg_replace('#[/\\\\:*?"<>|]#', '-', $track->title));
            // ponytail: reads WAVs by local path; a remote disk (R2) needs a temp copy first.
            $zip->addFile($track->getFirstMedia('wav')->getPath(), $name);
            $zip->setCompressionName($name, ZipArchive::CM_STORE);
        }

        $zip->close();

        // addMedia moves the temp file onto the music disk.
        $release->addMedia($tmp)
            ->usingFileName("{$release->slug}.zip")
            ->withCustomProperties(['fingerprint' => $fingerprint])
            ->toMediaCollection('zip');
    }
}
