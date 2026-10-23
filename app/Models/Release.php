<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * A release on the music page. The alias is plain text in `artist`; the
 * filter on the list is built from its distinct values (spec, decision 4).
 */
class Release extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['slug', 'artist', 'title', 'released_on', 'description', 'min_price', 'is_published'];

    protected function casts(): array
    {
        return [
            'released_on' => 'date',
            'min_price' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // A sold release keeps its tracks, or the buyer loses the WAV. This runs
        // first: returning false stops the delete before any track goes.
        // The foreign key would cascade past the tracks' own deleting event,
        // where medialibrary removes their preview and WAV files.
        static::deleting(function (Release $release) {
            if ($release->isSold()) {
                return false;
            }

            $release->tracks->each->delete();
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile()->useDisk('public');
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class)->orderBy('position')->chaperone();
    }

    /** Sales of the whole release; tracks bought one by one are on the tracks. */
    public function sales(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'purchasable');
    }

    /** Bought as an album, or any of its tracks was bought. */
    public function isSold(): bool
    {
        return $this->sales()->exists()
            || OrderItem::where('purchasable_type', Track::class)
                ->whereIn('purchasable_id', $this->tracks()->reorder()->select('tracks.id'))
                ->exists();
    }

    /** Published, with tracks, and every track has a WAV to hand over. */
    public function isPurchasable(): bool
    {
        return $this->is_published
            && $this->tracks->isNotEmpty()
            && $this->tracks->every(fn (Track $track) => $track->hasMedia('wav'));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function scopeNewest(Builder $query): void
    {
        $query->orderByDesc('released_on')->orderByDesc('id');
    }

    public function totalSeconds(): int
    {
        return (int) $this->tracks->sum('duration_seconds');
    }

    public function minutes(): int
    {
        return intdiv($this->totalSeconds(), 60);
    }

    public function aliasSlug(): string
    {
        return Str::slug($this->artist);
    }

    public function coverUrl(): ?string
    {
        return $this->getFirstMediaUrl('cover') ?: null;
    }

    /**
     * What the player needs to queue this release: only tracks with a preview.
     * Null when nothing can be played, and then no play control is rendered.
     *
     * @return array{slug: string, alias: string, title: string, cover: ?string, url: string, tracks: list<array<string, mixed>>}|null
     */
    public function playlist(): ?array
    {
        $tracks = $this->tracks
            ->filter(fn (Track $track) => $track->previewUrl())
            ->map(fn (Track $track) => [
                'id' => $track->id,
                'position' => $track->position,
                'title' => $track->title,
                'seconds' => $track->duration_seconds,
                'clock' => $track->clock(),
                'price' => $track->min_price,
                'preview' => $track->previewUrl(),
                'buyable' => $track->setRelation('release', $this)->isPurchasable(),
            ])
            ->values();

        return $tracks->isEmpty() ? null : [
            'slug' => $this->slug,
            'alias' => $this->artist,
            'title' => $this->title,
            'cover' => $this->coverUrl(),
            'url' => route('music.show', ['release' => $this]),
            'tracks' => $tracks->all(),
        ];
    }

    /**
     * The aliases of published releases, most releases first.
     *
     * @return Collection<int, array{slug: string, name: string, count: int}>
     */
    public static function aliases(): Collection
    {
        return static::published()
            ->selectRaw('artist, count(*) as aggregate')
            ->groupBy('artist')
            ->orderByDesc('aggregate')
            ->orderBy('artist')
            ->get()
            ->map(fn ($row) => ['slug' => Str::slug($row->artist), 'name' => $row->artist, 'count' => (int) $row->aggregate])
            ->values();
    }
}
