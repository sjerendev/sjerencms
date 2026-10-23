<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile()->useDisk('public');
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class)->orderBy('position');
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
