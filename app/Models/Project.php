<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * A client project on the portfolio. Pages pick projects by id (the
 * featured cases and the register); this model carries what they show.
 */
class Project extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'slug', 'client', 'headline_sv', 'headline_en', 'role_sv', 'role_en',
        'services', 'tech', 'url', 'rebuilt', 'position',
    ];

    protected function casts(): array
    {
        return [
            'services' => 'array',
            'rebuilt' => 'boolean',
            'position' => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile()->useDisk('public');
        // Portrait thumbnail (450×526) shown on hover in the register.
        $this->addMediaCollection('preview')->singleFile()->useDisk('public');
        // Full-page screenshot: pins the case and scrolls inside its frame.
        $this->addMediaCollection('tall')->singleFile()->useDisk('public');
    }

    /** @param 'sv'|'en' $language */
    public function headline(string $language): string
    {
        return $this->{"headline_{$language}"};
    }

    /** The role that is set, else derived from the services. @param 'sv'|'en' $language */
    public function role(string $language): string
    {
        $role = $this->{"role_{$language}"};

        if ($role) {
            return $role;
        }

        $designs = collect($this->services)->contains(fn (string $service) => preg_match('/design/i', $service));

        return __($designs ? 'projects.roleDesignDev' : 'projects.roleDev', [], $language);
    }

    public function tech(): string
    {
        return $this->tech ?: implode(' · ', $this->services);
    }

    public function host(): string
    {
        return rtrim(preg_replace('#^https?://(www\.)?#', '', $this->url), '/');
    }

    public function imageUrl(): ?string
    {
        return $this->getFirstMediaUrl('image') ?: null;
    }

    public function previewUrl(): ?string
    {
        return $this->getFirstMediaUrl('preview') ?: null;
    }

    /**
     * The full-page screenshot with its size, which ScrollTrigger needs.
     * ponytail: reads the file on every call and needs a local disk; with R2
     * the size has to go into custom properties on upload instead.
     *
     * @return array{src: string, width: int, height: int}|null
     */
    public function tall(): ?array
    {
        $media = $this->getFirstMedia('tall');
        $size = $media ? @getimagesize($media->getPath()) : false;

        return $size ? ['src' => $media->getUrl(), 'width' => $size[0], 'height' => $size[1]] : null;
    }

    /**
     * The projects with these ids, in this order; ids that no longer exist
     * are skipped. No ids means every project by position.
     *
     * @param  list<int>  $ids
     * @return Collection<int, static>
     */
    public static function chosen(array $ids): Collection
    {
        if ($ids === []) {
            return static::orderBy('position')->orderBy('id')->get();
        }

        return static::whereIn('id', $ids)->get()->sortBy(fn (self $project) => array_search($project->id, $ids))->values();
    }

    /** Fills the {total} placeholder in block copy with the number of projects. */
    public static function withTotal(?string $text): string
    {
        return str_replace('{total}', (string) static::count(), $text ?? '');
    }
}
