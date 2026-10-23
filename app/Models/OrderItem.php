<?php

namespace App\Models;

use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * A release or a track on an order. `purchasable_type` holds the class name:
 * there is no morph map, because a map would change Release::getMorphClass()
 * and orphan the existing media.model_type rows.
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'purchasable_type', 'purchasable_id', 'title', 'amount', 'download_count'];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'download_count' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    /** The track's WAV, or the release's ZIP; null while the ZIP is still being prepared. */
    public function file(): ?Media
    {
        return match (true) {
            $this->purchasable instanceof Track => $this->purchasable->getFirstMedia('wav'),
            $this->purchasable instanceof Release => $this->purchasable->getFirstMedia('zip'),
            default => null,
        };
    }

    public function downloadName(): string
    {
        $artist = $this->purchasable instanceof Track ? $this->purchasable->release->artist : $this->purchasable->artist;
        $extension = $this->purchasable instanceof Track ? 'wav' : 'zip';

        return preg_replace('#[/\\\\:*?"<>|]#', '-', "{$artist} - {$this->title}").".{$extension}";
    }

    public function downloadsLeft(): int
    {
        return max(0, app(GeneralSettings::class)->download_limit - $this->download_count);
    }
}
