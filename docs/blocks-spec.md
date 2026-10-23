# Block Specification: StickyCarousel & Column Cards Enhancement

## Overview

This specification defines two block implementations for the sjerencms Filament CMS:

1. **StickyCarousel** - A new full-screen horizontal scroll-hijacking gallery with WebGL hover effects
2. **ColumnCards** - Enhancement of existing ThreeColumnCardsBlock with 2-column option and hover effects

Both blocks share a WebGL magnetic distortion effect via a shared Vue composable.

---

## 1. StickyCarousel Block

### Description
A full-screen image showcase that "sticks" to the viewport while the user scrolls. Vertical scroll is translated into horizontal image navigation until the last image is reached, at which point normal scrolling resumes.

### Behavior

#### Desktop (≥768px)
- Section sticks to viewport when scrolled into view
- Vertical scroll translates to horizontal image movement
- Continuous scroll (no snapping), images flow as a seamless strip
- Each image gets ~100vh of scroll distance (auto-calculated based on image count)
- After last image, section smoothly releases with fade-out transition
- Images display edge-to-edge (no gap between images)

#### Mobile (<768px)
- Falls back to standard touch-swipeable horizontal carousel
- No scroll-hijacking behavior
- Native swipe gestures for navigation

#### Keyboard Navigation
- Left/right arrow keys navigate between images when section is focused

### Visual Elements

#### Section Height
- Configurable via Filament input field
- Supports values: `px`, `%`, `vh`, `svh`
- Example values: `100vh`, `80svh`, `600px`

#### Progress Indicator
- Fraction counter format: `2/7`
- Position: Bottom-right
- Animation: Numbers slide/fade when changing

#### Title Display
- Position: Bottom-left
- Behavior: Fades in/out as images and titles change
- Only visible when hovering the section

#### Skip Button
- Small "Skip" button appears after brief delay
- Allows users to bypass the scroll-hijacking and jump to next section

### Hover Effect: Magnetic Distortion

#### Technology
- Three.js for WebGL rendering
- Pronounced fish-eye/wave distortion effect
- Effect follows cursor position

#### Behavior
- Activates when cursor hovers over image
- Distortion intensity is pronounced (clearly visible)
- Disabled on touch devices (static images only)

### Filament Schema

```php
// StickyCarouselBlock.php
Forms\Components\TextInput::make('heading')
    ->label('Section Heading (Optional)')
    ->placeholder('e.g., Selected Works'),

Forms\Components\TextInput::make('subheading')
    ->label('Subheading (Optional)'),

Forms\Components\TextInput::make('section_height')
    ->label('Section Height')
    ->placeholder('100vh')
    ->default('100vh')
    ->helperText('Supports px, %, vh, svh values'),

Forms\Components\Repeater::make('images')
    ->schema([
        Forms\Components\FileUpload::make('image')
            ->image()
            ->disk('public')
            ->directory('sticky-carousel')
            ->visibility('public')
            ->required(),
        Forms\Components\TextInput::make('title')
            ->label('Project Title')
            ->required(),
        Forms\Components\TextInput::make('url')
            ->label('Link URL')
            ->url()
            ->placeholder('https://...')
            ->required(),
    ])
    ->collapsible()
    ->reorderable()
    ->minItems(3),

Forms\Components\TextInput::make('section_class')
    ->label('Section Class Name')
    ->placeholder('Add custom CSS classes'),
```

### Vue Component Structure

```
resources/js/
├── components/
│   └── blocks/
│       └── StickyCarousel.vue
├── composables/
│   └── useWebGLDistortion.js    # Shared composable
```

### SSR Handling
- Server-side: Render static images (SEO-friendly, good initial paint)
- Client-side: Hydrate with WebGL effects after Vue mounts
- Three.js lazy-loaded when block enters viewport via dynamic import

---

## 2. ColumnCards Block (Enhanced ThreeColumnCardsBlock)

### Description
Rename existing `ThreeColumnCardsBlock` to `ColumnCardsBlock` and expand column options to include 2 columns.

### Changes from Current Implementation

#### Column Options
- **Current**: 3, 4, 5 columns
- **New**: 2, 3, 4, 5 columns

#### Block Name
- **Current**: `three_column_cards` / "Three Column Cards"
- **New**: `column_cards` / "Column Cards"

#### New Fields
- Optional URL per card (entire card becomes clickable)

### Hover Effect

#### Images Present
- Same magnetic distortion effect as StickyCarousel
- **Scaled down** intensity (proportional to smaller card size)
- Effect only triggers when hovering over the image portion
- Disabled on touch devices

#### No Image
- No hover effect on text-only cards

### Image Handling

#### Aspect Ratio
- Enforced 4:3 ratio for all card images
- Images cropped to fit via `object-cover`

#### Click Behavior
- If URL present: Entire card is clickable link
- If no URL: Card is not clickable, just informational

### Responsive Behavior

Column collapse pattern as viewport shrinks:
- 5 columns → 3 → 1
- 4 columns → 2 → 1
- 3 columns → 2 → 1
- 2 columns → 1

Breakpoints: Use Tailwind's default `sm`, `md`, `lg` breakpoints

### Updated Filament Schema

```php
// ColumnCardsBlock.php (renamed from ThreeColumnCardsBlock)
Forms\Components\Select::make('columns')
    ->label('Number of Columns')
    ->options([
        2 => '2 Columns',
        3 => '3 Columns',
        4 => '4 Columns',
        5 => '5 Columns',
    ])
    ->default(3)
    ->required(),

Forms\Components\Repeater::make('cards')
    ->schema([
        Forms\Components\FileUpload::make('image')
            ->image()
            ->disk('public')
            ->directory('card-images')
            ->visibility('public'),
        Forms\Components\Grid::make(3)
            ->schema([
                MarkdownEditor::make('text')
                    ->required()
                    ->toolbarButtons([
                        'heading',
                        'bold',
                        'italic',
                        'strike',
                        'link',
                        'blockquote',
                        'bulletList',
                        'orderedList',
                        'codeBlock',
                        'table',
                    ]),
            ]),
        Forms\Components\TextInput::make('url')
            ->label('Link URL (Optional)')
            ->url()
            ->placeholder('https://...'),
    ])
    ->collapsible()
    ->reorderable(),

Forms\Components\TextInput::make('section_class')
    ->label('Section Class Name')
    ->placeholder('Add custom CSS classes'),
```

### Vue Component Updates

```vue
<!-- Updated grid classes -->
const getColumnClass = computed(() => {
    switch (props.block.columns) {
        case '2':
            return 'grid-cols-1 sm:grid-cols-2';
        case '3':
            return 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3';
        case '4':
            return 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4';
        case '5':
            return 'grid-cols-1 sm:grid-cols-3 lg:grid-cols-5';
        default:
            return 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3';
    }
});
```

---

## 3. Shared Composable: useWebGLDistortion

### Location
`resources/js/composables/useWebGLDistortion.js`

### API

```javascript
export function useWebGLDistortion(options = {}) {
    // Options
    const {
        intensity = 1.0,      // 1.0 for carousel, ~0.6 for cards
        containerRef,         // Vue ref to container element
        imageSelector,        // CSS selector for images within container
    } = options;

    // Returns
    return {
        init,                 // Call on mounted
        destroy,              // Call on unmounted
        isSupported,          // Boolean: WebGL available
    };
}
```

### Features
- Lazy-loads Three.js on first use
- Detects touch devices and disables effect
- Handles SSR gracefully (no-op on server)
- Cleans up WebGL context on destroy

### Effect Description
- Pronounced magnetic distortion (fish-eye/bulge effect)
- Effect center follows cursor position
- Uses custom GLSL shader for distortion
- Smooth easing on effect intensity

---

## 4. File Structure Summary

### New Files
```
app/Filament/Forms/Components/Blocks/
└── StickyCarouselBlock.php

resources/js/
├── components/blocks/
│   └── StickyCarousel.vue
└── composables/
    └── useWebGLDistortion.js
```

### Modified Files
```
app/Filament/Forms/Components/Blocks/
└── ThreeColumnCardsBlock.php → ColumnCardsBlock.php

app/Filament/Forms/Components/
└── ContentBlocksSection.php  (update block registration)

resources/js/components/blocks/
└── ThreeColumnCards.vue → ColumnCards.vue

resources/js/components/
└── BlockRenderer.vue (update block mapping)
```

---

## 5. Dependencies

### New NPM Package
```bash
npm install three
```

### Bundle Optimization
- Three.js loaded via dynamic import
- Only loaded when StickyCarousel or ColumnCards (with images) blocks are present
- Estimated addition: ~150kb gzipped (only when needed)

---

## 6. Migration Notes

### Database
No migration needed - block data structure is compatible.

### Existing Content
- Existing `three_column_cards` blocks will need the block type updated in the database
- Run a simple SQL update or Laravel migration to rename block type

```sql
UPDATE pages
SET content = REPLACE(content, '"type":"three_column_cards"', '"type":"column_cards"')
WHERE content LIKE '%three_column_cards%';
```

---

## 7. Testing Checklist

### StickyCarousel
- [ ] Scroll-hijacking engages/releases smoothly
- [ ] Mobile swipe carousel works
- [ ] Keyboard navigation with arrow keys
- [ ] Skip button appears and functions
- [ ] Fraction counter animates on image change
- [ ] Title fades in/out correctly
- [ ] WebGL effect renders and follows cursor
- [ ] Links navigate to correct URLs
- [ ] SSR renders static images without errors
- [ ] Three.js lazy-loads correctly

### ColumnCards
- [ ] 2-column layout displays correctly
- [ ] Responsive column collapse works
- [ ] Image aspect ratio enforced at 4:3
- [ ] WebGL effect works on card images (scaled intensity)
- [ ] Entire card clickable when URL present
- [ ] No hover effect on cards without images
- [ ] Existing 3/4/5 column layouts still work

### Cross-Browser
- [ ] Chrome/Edge
- [ ] Firefox
- [ ] Safari
- [ ] Mobile Safari
- [ ] Mobile Chrome

---

## 8. Accessibility Considerations

- Keyboard navigation for carousel
- Skip button for scroll-hijacking escape
- Alt text from title field for images
- `aria-label` on navigation elements
- `prefers-reduced-motion` media query to disable WebGL effects for users who prefer reduced motion
