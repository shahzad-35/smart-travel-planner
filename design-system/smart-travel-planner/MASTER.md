# Design System Master File

> **LOGIC:** When building a specific page, first check `design-system/pages/[page-name].md`.
> If that file exists, its rules **override** this Master file.
> If not, strictly follow the rules below.

---

**Project:** Smart Travel Planner
**Generated:** 2026-08-15 18:39:56
**Category:** Road Trip Planner
**Design Dials:** Variance 6/10 (Balanced / Modern) | Motion 5/10 (Standard) | Density 5/10 (Standard)

---

## Global Rules

### Color Palette — LOCKED (do not regenerate)

The palette is the project's established hybrid identity, defined as CSS variables in
`resources/css/app.css` and documented in the repo-root **DESIGN.md** (source of truth):

- **Light — "field guide":** primary deep teal `#0F766E`, secondary teal `#14B8A6`,
  accent warm orange `#EA580C`, surface mint-white `#F0FDFA`, borders `#CCFBF1`.
- **Dark — "expedition dusk":** primary amber `#FB923C`, secondary cyan `#22D3EE`,
  accent teal `#2DD4BF`, surface warm near-black `#0F0D0B`, card stone `#1C1917`.

Never hardcode hex in views — use the Tailwind token classes (`bg-primary`, `text-accent`,
`bg-surface-card`, …). One accent emphasis per view.

### Typography — LOCKED

- **Family:** Plus Jakarta Sans (single family; weight + tracking do the hierarchy)
- Page titles `.page-title` (3xl extrabold tight) · section titles `.section-title`
- Labels `.eyebrow` (11px bold uppercase wide) · digits always `tabular-nums`
- Full scale in DESIGN.md.

### Spacing Variables

*Density: 5/10 — Standard*

| Token | Value | Usage |
|-------|-------|-------|
| `--space-xs` | `4px` / `0.25rem` | Tight gaps |
| `--space-sm` | `8px` / `0.5rem` | Icon gaps, inline spacing |
| `--space-md` | `16px` / `1rem` | Standard padding |
| `--space-lg` | `24px` / `1.5rem` | Section padding |
| `--space-xl` | `32px` / `2rem` | Large gaps |
| `--space-2xl` | `48px` / `3rem` | Section margins |
| `--space-3xl` | `64px` / `4rem` | Hero padding |

### Shadow Depths

| Level | Value | Usage |
|-------|-------|-------|
| `--shadow-sm` | `0 1px 2px rgba(0,0,0,0.05)` | Subtle lift |
| `--shadow-md` | `0 4px 6px rgba(0,0,0,0.1)` | Cards, buttons |
| `--shadow-lg` | `0 10px 15px rgba(0,0,0,0.1)` | Modals, dropdowns |
| `--shadow-xl` | `0 20px 25px rgba(0,0,0,0.15)` | Hero images, featured cards |

---

## Component Architecture

Two layers, both already in the repo:

1. **Styling layer** — CSS component classes in `resources/css/app.css`:
   `.btn-primary` `.btn-ghost` `.btn-danger` `.field` `.chip` `.card` `.card-hover`
   `.list-row` `.page-title` `.section-title` `.eyebrow` `.focus-ring` `.aurora-bg` `.fade-up`
2. **Markup layer** — anonymous Blade primitives in `resources/views/components/ui/`:
   `x-ui.page-header` `x-ui.stat-card` `x-ui.empty-state` `x-ui.cover-card`
   `x-ui.alert` `x-ui.chip` `x-ui.modal` `x-ui.skeleton` `x-ui.toast-hub`

Rules: never duplicate markup that a primitive covers; new reusable UI becomes a new
`x-ui.*` component, not an inline block. Livewire wiring (`wire:*`) is passed through
attributes and never hardcoded inside primitives.

## Style Guidelines

**Style:** Modern elegant hybrid — token-driven light/dark (see DESIGN.md identity). Cinematic destination imagery (flag-backdrop covers, morph hero) carries emotion; UI stays quiet around it.

**Keywords:** dark mode, cinematic, ambient light, glassmorphism, deep black, indigo, glow, blur, atmospheric, reanimated, haptic, premium, layered, frosted glass, linear gradient

**Best For:** Developer tools, pro productivity apps, fintech/trading dashboards, media/streaming platforms, AI tool interfaces, high-end gaming companion apps

**Key Effects:** Expo.out Bezier(0.16,1,0.3,1) easing; spring modals (damping:20 stiffness:90); haptic-linked press (Impact Light/Medium); animated ambient light blobs (Reanimated translateX/Y slow oscillation); BlurView glassmorphism headers/nav (intensity 20); scale press 0.97 → 1.0; avoid pure #000000 (OLED smear)

### Page Pattern

**Pattern Name:** Real-Time / Operations Landing

- **Conversion Strategy:** For ops/security/iot products. Demo or sandbox link. Trust signals.
- **CTA Placement:** Primary CTA in nav + After metrics
- **Section Order:** 1. Hero (product + live preview or status), 2. Key metrics/indicators, 3. How it works, 4. CTA (Start trial / Contact)

---

## Motion

**Stagger List** (Standard) — Trigger: load or scroll | Duration: 300-450ms | Easing: `back.out(1.4)`

```js
gsap.from('.grid-item', { opacity: 0, scale: 0.92, y: 16, duration: 0.4, stagger: { each: 0.06, from: 'start', grid: 'auto' }, ease: 'back.out(1.4)' });
```

**Framework notes:** grid: 'auto' lets GSAP infer rows/columns from a CSS grid layout for a natural wave stagger

- ✅ Combine with from: 'center' for a bento-grid layout to draw the eye inward first
- ❌ Don't use back.out on dense data tables; the overshoot reads as sloppy on informational UI
- ⚡ Group DOM writes; avoid interleaving layout reads (getBoundingClientRect) between staggered tweens

---

## Anti-Patterns (Do NOT Use)

- ❌ Inconsistent styling
- ❌ Poor contrast ratios

### Additional Forbidden Patterns

- ❌ **Emojis as icons** — Use SVG icons (Heroicons, Lucide, Simple Icons)
- ❌ **Missing cursor:pointer** — All clickable elements must have cursor:pointer
- ❌ **Layout-shifting hovers** — Avoid scale transforms that shift layout
- ❌ **Low contrast text** — Maintain 4.5:1 minimum contrast ratio
- ❌ **Instant state changes** — Always use transitions (150-300ms)
- ❌ **Invisible focus states** — Focus states must be visible for a11y

---

## Pre-Delivery Checklist

Before delivering any UI code, verify:

- [ ] No emojis used as icons (use SVG instead)
- [ ] All icons from consistent icon set (Heroicons/Lucide)
- [ ] `cursor-pointer` on all clickable elements
- [ ] Hover states with smooth transitions (150-300ms)
- [ ] Light mode: text contrast 4.5:1 minimum
- [ ] Focus states visible for keyboard navigation
- [ ] `prefers-reduced-motion` respected
- [ ] Responsive: 375px, 768px, 1024px, 1440px
- [ ] No content hidden behind fixed navbars
- [ ] No horizontal scroll on mobile
