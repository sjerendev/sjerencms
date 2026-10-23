<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * A track on a release. The MP3 preview is public; the WAV lives on the
 * private `music` disk and never gets a public URL.
 */
class Track extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['release_id', 'position', 'title', 'duration_seconds', 'min_price'];

    protected $attributes = ['min_price' => 20];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'duration_seconds' => 'integer',
            'min_price' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // A sold track keeps its WAV for the buyer. Unpublish it instead.
        static::deleting(fn (Track $track) => $track->isSold() ? false : null);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('preview')->singleFile()->useDisk('public');
        $this->addMediaCollection('wav')->singleFile()->useDisk('music');
    }

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class);
    }

    public function sales(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'purchasable');
    }

    public function isSold(): bool
    {
        return $this->sales()->exists();
    }

    /** The release is published and there is a WAV to hand over. */
    public function isPurchasable(): bool
    {
        return $this->release->is_published && $this->hasMedia('wav');
    }

    /** `m:ss`, or `h:mm:ss` from an hour. */
    public function clock(): string
    {
        $s = $this->duration_seconds;

        return $s >= 3600
            ? sprintf('%d:%02d:%02d', intdiv($s, 3600), intdiv($s % 3600, 60), $s % 60)
            : sprintf('%d:%02d', intdiv($s, 60), $s % 60);
    }

    public function previewUrl(): ?string
    {
        return $this->getFirstMediaUrl('preview') ?: null;
    }
}
