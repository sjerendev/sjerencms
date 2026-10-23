<?php

use App\Filament\Resources\Releases\Pages\CreateRelease;
use App\Filament\Resources\Releases\Pages\EditRelease;
use App\Filament\Resources\Releases\RelationManagers\TracksRelationManager;
use App\Models\Release;
use App\Models\Track;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('creates a release with a cover', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    Livewire::test(CreateRelease::class)
        ->fillForm([
            'title' => 'Past Voyages', 'slug' => 's-d-l-past-voyages', 'artist' => 'S.D.L.',
            'released_on' => '2026-09-10', 'min_price' => 70, 'is_published' => true,
            'cover' => [UploadedFile::fake()->image('cover.jpg', 700, 700)],
        ])
        ->call('create')->assertHasNoFormErrors();

    expect(Release::sole()->getFirstMedia('cover'))->not->toBeNull();
});

it('rejects a minimum price below zero and a duplicate slug', function () {
    $this->actingAs(User::factory()->create());
    Release::factory()->create(['slug' => 'taken']);

    Livewire::test(CreateRelease::class)
        ->fillForm(['title' => 'X', 'slug' => 'taken', 'artist' => 'A', 'released_on' => '2026-01-01', 'min_price' => -1])
        ->call('create')->assertHasFormErrors(['slug' => 'unique', 'min_price']);
});

it('adds a track with preview and wav from the release page', function () {
    Storage::fake('public');
    Storage::fake('music');
    $this->actingAs(User::factory()->create());
    $release = Release::factory()->create();

    Livewire::test(TracksRelationManager::class, ['ownerRecord' => $release, 'pageClass' => EditRelease::class])
        ->callAction(TestAction::make('create')->table(), [
            'position' => 1, 'title' => 'Big Dripper', 'duration_seconds' => 524, 'min_price' => 20,
            'preview' => [UploadedFile::fake()->create('p.mp3', 10, 'audio/mpeg')],
            'wav' => [UploadedFile::fake()->create('m.wav', 10, 'audio/wav')],
        ])->assertHasNoFormErrors();

    $track = $release->tracks()->sole();
    expect($track->getFirstMedia('preview')->disk)->toBe('public')
        ->and($track->getFirstMedia('wav')->disk)->toBe('music');
});

it('renders the edit page with the tracks table', function () {
    $this->actingAs(User::factory()->create());
    $release = Release::factory()->has(Track::factory(), 'tracks')->create();

    $this->get("/admin/releases/{$release->id}/edit")->assertOk()->assertSee($release->tracks[0]->title);
});

it('rejects a duplicate position and a missing or negative track price as form errors', function () {
    $this->actingAs(User::factory()->create());
    $release = Release::factory()->create();
    $release->tracks()->create(['position' => 1, 'title' => 'A', 'duration_seconds' => 60]);
    Release::factory()->create()->tracks()->create(['position' => 2, 'title' => 'Other release', 'duration_seconds' => 60]);

    $manager = fn () => Livewire::test(TracksRelationManager::class, ['ownerRecord' => $release, 'pageClass' => EditRelease::class]);

    $manager()->callAction(TestAction::make('create')->table(), ['position' => 1, 'title' => 'B', 'duration_seconds' => 60, 'min_price' => null])
        ->assertHasFormErrors(['position' => 'unique', 'min_price' => 'required']);

    $manager()->callAction(TestAction::make('create')->table(), ['position' => 2, 'title' => 'C', 'duration_seconds' => 0, 'min_price' => -1])
        ->assertHasFormErrors(['min_price', 'duration_seconds'])
        ->assertHasNoFormErrors(['position']);
});

it('keeps its own position when a track is edited', function () {
    $this->actingAs(User::factory()->create());
    $release = Release::factory()->create();
    $track = $release->tracks()->create(['position' => 1, 'title' => 'A', 'duration_seconds' => 60]);

    Livewire::test(TracksRelationManager::class, ['ownerRecord' => $release, 'pageClass' => EditRelease::class])
        ->callAction(TestAction::make('edit')->table($track), ['title' => 'A2'])
        ->assertHasNoFormErrors();

    expect($track->fresh()->title)->toBe('A2');
});

it('takes full-length WAV masters, not just Livewire\'s 12 MB default', function () {
    // A 30-minute 16-bit/44.1 kHz stereo WAV is about 320 MB.
    expect(config('livewire.temporary_file_upload.rules'))->toContain('max:1048576')
        ->and(config('livewire.temporary_file_upload.max_upload_time'))->toBeGreaterThanOrEqual(30);
});
