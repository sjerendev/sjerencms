# Bilingual CMS Implementation Plan

## Overview
Implement bilingual functionality with Swedish as the default language (no URL prefix) and English with `/en/` prefix.

## Database Changes

### 1. Add Language Column Migration
```php
// Create migration: add_language_to_pages_and_posts_tables.php
Schema::table('pages', function (Blueprint $table) {
    $table->string('language', 2)->default('sv')->after('slug');
    $table->dropUnique(['slug']);
    $table->unique(['slug', 'language']);
});

Schema::table('posts', function (Blueprint $table) {
    $table->string('language', 2)->default('sv')->after('slug');
    $table->dropUnique(['slug']);
    $table->unique(['slug', 'language']);
});
```

### 2. Update Model Fillable Arrays
- Add `'language'` to fillable arrays in `Page.php` and `Post.php`
- Add language scope methods for filtering

## Admin Panel Changes

### 1. Update Filament Resources

#### PageResource.php
- Add language selector in form (Select field with 'sv' and 'en' options, default 'sv')
- Add language column in table view
- Add language filter dropdown in table filters (All/Swedish/English)
- Simple per-content language management

#### PostResource.php
- Same changes as PageResource
- Language filter for easy content organization
- No global admin language switching required

### 2. Admin Language Management
- Language selection per page/post (no global switcher needed)
- Table filters for language-based content filtering
- Clean, simple approach without session-based language context

## Frontend Changes

### 1. Route Structure
```
Swedish (default):
/                    -> Home (Swedish)
/om-oss             -> About page (Swedish)
/blogg              -> Blog (Swedish)
/blogg/artikel-slug -> Blog post (Swedish)

English:
/en                 -> Home (English)
/en/about-us        -> About page (English)
/en/blog            -> Blog (English)
/en/blog/article-slug -> Blog post (English)
```

### 2. Vue.js Router Updates

#### router/index.js
```javascript
// Add language detection middleware
// Create routes with optional language prefix
// Handle fallbacks for missing translations

const routes = [
  {
    path: '/:lang(en)?',
    component: Layout,
    children: [
      {
        path: '',
        name: 'home',
        component: Home
      },
      {
        path: 'blog',
        name: 'blog',
        component: Blog
      },
      // ... other routes
    ]
  }
]
```

### 3. API Endpoint Updates

#### Laravel Routes (web.php/api.php)
```php
// Update API routes to include language parameter
Route::get('/api/pages/{slug}', function($slug, Request $request) {
    $language = $request->route()->parameter('lang') ?? 'sv';
    return Page::where('slug', $slug)
               ->where('language', $language)
               ->where('is_published', true)
               ->firstOrFail();
});
```

### 4. Language Detection & Switching

#### Create Language Composable
```javascript
// composables/useLanguage.js
export function useLanguage() {
  const currentLanguage = computed(() => {
    return route.params.lang || 'sv'
  })
  
  const switchLanguage = (newLang) => {
    // Handle language switching logic
  }
  
  return { currentLanguage, switchLanguage }
}
```

### 5. Language Switcher Component
```vue
<!-- components/LanguageSwitcher.vue -->
<template>
  <div class="language-switcher">
    <button @click="switchTo('sv')" :class="{ active: currentLang === 'sv' }">
      Svenska
    </button>
    <button @click="switchTo('en')" :class="{ active: currentLang === 'en' }">
      English
    </button>
  </div>
</template>
```

## Implementation Steps

### Phase 1: Database & Models (1-2 hours)
1. Create and run language migration
2. Update Page and Post models
3. Add language scopes and relationships

### Phase 2: Admin Panel (3-4 hours)
1. Update PageResource and PostResource forms
2. Add language filters and columns to tables
3. Implement language selection per content item
4. Add table filtering by language (simpler than global context)

### Phase 3: Frontend Routing (3-4 hours)
1. Update Vue router configuration
2. Implement language detection middleware
3. Update API routes with language parameter
4. Handle route fallbacks

### Phase 4: Language Switching (2-3 hours)
1. Create language composable
2. Build language switcher component
3. Implement cross-language page linking
4. Add language meta tags for SEO

### Phase 5: Content Migration (1-2 hours)
1. Set all existing content to 'sv'
2. Test admin functionality
3. Create sample English content for testing

## Technical Considerations

### SEO Optimization
- Add `hreflang` meta tags
- Update sitemap generation for multiple languages
- Implement proper canonical URLs

### URL Structure Benefits
- Swedish content maintains current URLs (no breaking changes)
- English content clearly identified with `/en/` prefix
- Clean, SEO-friendly structure
- Easy to understand for users

### Fallback Strategy
- If English page doesn't exist, redirect to Swedish equivalent
- Show language switcher only when both versions exist
- Graceful handling of missing translations

## Testing Checklist

- [ ] Admin can create pages in both languages
- [ ] Admin can filter content by language
- [ ] Swedish URLs work without prefix
- [ ] English URLs work with `/en/` prefix
- [ ] Language switcher functions correctly
- [ ] API returns correct language content
- [ ] SEO meta tags include language information
- [ ] Navigation works in both languages

## Future Enhancements

1. **Content Linking**: Link related pages across languages
2. **Translation Status**: Track which content needs translation
3. **Bulk Operations**: Copy content structure between languages
4. **Language-specific Menus**: Different navigation for each language
5. **Auto-translation Integration**: Optional AI translation assistance

## Estimated Total Time: 10-15 hours

This approach maintains backward compatibility while providing a clean, scalable bilingual solution that aligns with your requirements of keeping Swedish as the default language without URL prefixes.