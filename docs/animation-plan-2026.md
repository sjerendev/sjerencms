# Animation Plan 2026

## Scope
This document captures a prioritized motion plan for existing blocks in `sjerencms`.
Goal: raise visual quality toward award-level polish without harming accessibility, performance, or editor usability.

## Current Foundation (already in codebase)
- GSAP + ScrollTrigger in `resources/js/components/blocks/StaggerText.vue`
- Sticky scroll carousel logic in `resources/js/components/blocks/StickyCarousel.vue`
- WebGL distortion composable in `resources/js/composables/useWebGLDistortion.js`

## Top 10 Prioritized Effects

### 1) Motion Foundation (all blocks)
- Impact: 5/5
- Effort: Medium
- Add shared motion presets (`subtle`, `dramatic`, `editorial`) with standard duration/easing/delay tokens.
- Add global reduced-motion behavior and consistent viewport trigger rules.

### 2) In-View Reveal System (text/content blocks)
- Impact: 5/5
- Effort: Medium
- Reusable fade + slight y-offset + light blur reveal on enter-view.
- Apply to `HeadlineText`, `FullText`, `ImageText`, `BlogPosts`, `TeamMembers`, `LogoShowcase`.

### 3) Scroll-Progress Text Reveal
- Impact: 5/5
- Effort: Medium
- Progressive text reveal based on scroll progress (mask/clip + opacity).
- Primary targets: `HeadlineText`, `FullText`, `CallToAction`.

### 4) Image + Caption Sync
- Impact: 4.5/5
- Effort: Medium
- Pair image parallax/zoom drift with progressive caption/description reveal.
- Primary targets: `FullImage`, `ImageText`, `PortfolioItems`.

### 5) StickyCarousel 2.0 (cinematic handoff)
- Impact: 5/5
- Effort: Large
- Add progress rail + stronger title transition between slides.
- Add section handoff transition into next block to avoid hard visual cut.

### 6) Portfolio Hover-Follow Upgrade
- Impact: 4.5/5
- Effort: Medium
- Inertial hover preview with velocity-based tilt.
- Improve transition continuity from listing interaction to destination click.

### 7) Card Interaction Pack
- Impact: 4/5
- Effort: Medium
- Enter cascade by row/column + proximity hover highlight + subtle micro-tilt (desktop only).
- Targets: `ColumnCards`, `ThreeColumnCards`, `IconCards`, `ServiceCards`.

### 8) FAQ Cinematic Accordion
- Impact: 3.5/5
- Effort: Small-Medium
- Expand/collapse with height + opacity + blur.
- Add optional smart auto-scroll so active item remains in comfortable reading area.

### 9) Pricing Conversion Motion
- Impact: 4/5
- Effort: Small-Medium
- Count-up for price values, feature list stagger, and featured-plan spotlight behavior.

### 10) Site-Level Transitions (View Transitions + fallback)
- Impact: 4.5/5
- Effort: Medium-Large
- Introduce cohesive section/page transitions.
- Progressive enhancement strategy with fallback for unsupported browsers.

## Suggested Delivery Phases

### Phase 1 (highest ROI)
- Item 1: Motion Foundation
- Item 2: In-View Reveal System
- Item 3: Scroll-Progress Text Reveal

### Phase 2
- Item 4: Image + Caption Sync
- Item 7: Card Interaction Pack
- Item 8: FAQ Cinematic Accordion

### Phase 3
- Item 5: StickyCarousel 2.0
- Item 6: Portfolio Hover-Follow Upgrade
- Item 9: Pricing Conversion Motion

### Phase 4
- Item 10: Site-Level Transitions
- Final polish + consistency + perf pass

## Definition of Done (per effect)
- Honors `prefers-reduced-motion`.
- No layout shift / visible jump during enter/exit states.
- Meets smoothness target (desktop baseline ~60fps for normal interactions).
- Has graceful fallback when JS-heavy effect cannot run.
- Preserves readability and interaction priority over decoration.

## Practical Notes
- Keep animation opt-in per block via editor fields (preset + intensity + on/off).
- Prefer content-led motion (timing supports hierarchy and scanning).
- Use heavy shaders only where they add clear value; avoid broad, site-wide overuse.

## Implemented foundation
- [x] Added `useMotionPreferences` composable with reactive `prefersReducedMotion` and shared safe motion defaults.
- [x] Added `useInViewReveal` composable with `IntersectionObserver` options (`rootMargin`, `threshold`, `once`, `stagger`) plus configurable initial/revealed classes and data attributes.
- [x] Added reduced-motion bypass + SSR-safe behavior for reveal flows without external dependencies.

## Implemented in this iteration
- [x] In-view reveal rollout started for `BlogPosts`, `TeamMembers`, `LogoShowcase`, and `ImageText` (subtle fade + y-offset, with reduced-motion and no-observer fallback).
- [x] Scroll-progress text reveal added for `HeadlineText`, `FullText`, and `CallToAction` (opt-in via block flags, with configurable start/end if present in block data).
- [x] Shared in-view reveal composable is now wired into the migrated blocks above.
- [x] Image + caption sync baseline added for `FullImage`, `ImageText`, and `PortfolioItems` using shared `useImageCaptionSync` (scroll-progress image drift/zoom + caption reveal, with reduced-motion fallback).
- [x] Card interaction pack baseline added for `ColumnCards`, `ThreeColumnCards`, `IconCards`, and `ServiceCards` via shared `useCardInteractionPack` (in-view cascade, pointer proximity highlight, and subtle desktop-only micro-tilt with motion fallbacks).
- [x] FAQ cinematic accordion upgraded in `FaqAccordion` (dynamic height animation + opacity/blur transitions + optional smart auto-scroll with reduced-motion-safe behavior).
- [x] Portfolio hover-follow upgraded in `PortfolioItems` (inertial cursor follow + velocity-based preview tilt on desktop, with reduced-motion-safe fallback).
- [x] StickyCarousel 2.0 upgraded in `StickyCarousel` (progress rail + clickable steps, stronger slide title handoff transition, and smooth end-of-section handoff veil/transform).
- [x] Pricing conversion motion upgraded in `pricing_tables` (price count-up, feature stagger reveals, and featured-plan spotlight pulse with reduced-motion fallbacks).
- [x] Site-level transitions enabled (View Transitions API progressive enhancement + GSAP fallback for unsupported browsers) via `App.vue`, `entry-client.js`, and `usePageTransitions`.
- [x] Final consistency pass applied to remaining core blocks (`Carousel`, `ColumnsLayout`, `ContactForm`, `Video`, plus reduced-motion/perf polish in `TickerBlock`).
- [ ] Optional: additional editor controls can be added later to expose all motion toggles/settings per block in Filament.
