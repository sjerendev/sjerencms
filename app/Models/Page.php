<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Page extends Model implements HasMedia
{
	use HasFactory, InteractsWithMedia;

	protected $fillable = [
		'title',
		'slug',
		'language',
		'content',
		'is_published',
		'is_homepage',
		'hero_image',
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
		'parent_id',
		'sort_order',
		'show_in_main_nav',
		'show_in_footer_nav'
	];

	protected $casts = [
		'content' => 'array',
		'is_published' => 'boolean',
		'is_homepage' => 'boolean',
		'hero_image' => 'array',
		'show_in_main_nav' => 'boolean',
		'show_in_footer_nav' => 'boolean'
	];

	protected static function booted(): void
	{
		// A replaced CV leaves the disk after the save, not when it is removed
		// in the form, so a page left unsaved still has its file.
		static::updated(fn (Page $page) => $page->deleteCvFiles(
			array_diff(self::cvFiles($page->getOriginal('content')), self::cvFiles($page->content)),
		));
		static::deleted(fn (Page $page) => $page->deleteCvFiles(self::cvFiles($page->content)));
	}

	/** Deletes the given CVs from the disk, except those another page links to. */
	private function deleteCvFiles(array $files): void
	{
		if ($files === []) {
			return;
		}

		$used = Page::whereKeyNot($this->getKey())->get(['id', 'content'])->flatMap(fn (Page $other) => self::cvFiles($other->content));
		Storage::disk('public')->delete(array_values(array_diff($files, $used->all())));
	}

	/** The CV paths in a page's hero blocks. */
	private static function cvFiles(?array $content): array
	{
		return collect($content)->where('type', 'hero')->pluck('data.cv_file')->filter()->all();
	}

	public function parent()
	{
		return $this->belongsTo(Page::class, 'parent_id');
	}

	public function children()
	{
		return $this->hasMany(Page::class, 'parent_id');
	}

	public function level(): int
	{
		$level = 0;
		$page = $this;
		
		while ($page->parent_id) {
			$level++;
			$page = $page->parent;
		}
		
		return $level;
	}

	public function scopePublished($query)
	{
		return $query->where('is_published', true);
	}

	public function scopeLanguage($query, $language)
	{
		return $query->where('language', $language);
	}

	public function scopeSwedish($query)
	{
		return $query->where('language', 'sv');
	}

	public function scopeEnglish($query)
	{
		return $query->where('language', 'en');
	}
}
