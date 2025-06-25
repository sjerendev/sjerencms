<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_published',
        'publish_date',
        'hero_image',
        'list_image',
        'hero_height',
        'hero_headline',
        'hero_subheadline',
        'hero_paragraph',
        'hero_cta_text',
        'hero_cta_url',
        'hero_background_type',
        'hero_background_color',
        'hero_text_color',
        'meta_title',
        'meta_description',
        'headline_text',
        'headline_type',
        'headline',
        'text',
        'excerpt',
        'featured_image',
        'author_id',
        'category_id'
    ];

    protected $casts = [
        'content' => 'array',
        'is_published' => 'boolean',
        'publish_date' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Legacy relationship for backwards compatibility
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get the image to use in list views (blog lists, cards, etc.)
     * Falls back to hero_image if list_image is not set
     */
    public function getListImageAttribute(): ?string
    {
        return $this->attributes['list_image'] ?? $this->attributes['hero_image'] ?? null;
    }
}
