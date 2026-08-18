# Smart Travel Planner — Design System

## Identity

**Daylight field guide, dusk expedition.** In light mode: crisp mint-white surfaces with
deep teal authority and a single warm orange accent — the feel of a well-organized travel
journal on a bright morning. In dark mode the identity flips to expedition-at-dusk: warm
near-black stone surfaces where amber-orange leads and cool cyan/teal support — campfire
tones, not inverted teal. Destination photography is the emotional layer; the UI frames it.

## Tokens — Colors (defined in `resources/css/app.css`, consumed via `tailwind.config.js`)

Light (`:root`) — "field guide":
- Primary: deep teal `#0F766E` (`--color-primary`) — actions, links, active nav
- Primary light/dark: `#0D9488` / `#115E59` — hovers, gradients
- Secondary: teal `#14B8A6` — supporting emphasis
- Accent: warm orange `#EA580C` (`--color-accent`) — ONE highlight per view (badges, holiday markers)
- Surface: mint-white `#F0FDFA` / Card: `#FFFFFF` / Muted: `#E8F1F4`
- Foreground: pine `#134E4A`, muted `#4B5563`, subtle `#9CA3AF`
- Border: `#CCFBF1`, strong `#5EEAD4`

Dark (`.dark`) — "expedition dusk":
- Primary: amber `#FB923C` (fg `#431407`) — light/dark `#FDBA74`/`#F97316`
- Secondary: cyan `#22D3EE` (fg `#083344`)
- Accent: teal `#2DD4BF` (fg `#042F2E`)
- Surface: warm near-black `#0F0D0B` / Card: stone `#1C1917` / Muted: `#292524`
- Foreground: `#FAFAF9`, muted `#A8A29E`, subtle `#78716C`
- Border: `#292524`, strong `#44403C`

## Tokens — Typography

- Family: **Plus Jakarta Sans** (UI + headings), system fallbacks. No second display face —
  weight and tracking do the differentiation.
- Weights: 400 body · 500 UI labels · 600 emphasis · 700 card titles · 800 page titles/stats.
- Page titles: `text-3xl font-extrabold tracking-tight`
- Card/section titles: `text-xl font-bold tracking-tight`
- Labels/eyebrows: `text-[11px] font-bold uppercase tracking-wider text-foreground-subtle`
- Body: 16px/1.5, muted for secondary copy; stat digits always `tabular-nums`.
- Headlines get `text-wrap: balance` (or `leading-[1.1]` + manual balance).

## Tokens — Spacing & Shape

- Spacing rhythm: 4 / 8 / 12 / 16 / 24 / 32 / 48 / 96 (sections).
- Radius: inputs & buttons `rounded-xl` (12px) · cards `rounded-xl`/`rounded-2xl` ·
  chips & pills `rounded-full`. Inner elements tighter than containers.
- Shadows: `--shadow-card` (rest) / `--shadow-card-hover`; primary CTAs carry a tinted
  glow `shadow-lg shadow-primary/20-25`. Never plain black heavy shadows.

## Surfaces & Elevation

Three levels, separated by borders first, shadows second:
surface (page) → surface-card (cards, sidebar, topbar) → surface-muted (insets, rows).
Hairline `border-border`; `border-border-strong` on hover/focus. Dark mode raises
elevation by lightening the stone surface, never by piling shadows.

## Components

- **Primary button**: `bg-primary text-primary-foreground rounded-xl font-semibold
  hover:bg-primary-dark shadow-lg shadow-primary/20`, press `scale(0.98)`, focus ring.
- **Ghost/secondary button**: `bg-surface-card border border-border hover:border-border-strong text-foreground`.
- **Card**: `.card` (surface-card, border, radius-xl, shadow-card) + `.card-hover` lift.
- **Stat card**: gradient tint `from-{color}/15 via-{color}/5 to-transparent` +
  faint watermark icon bottom-right, value `text-3xl font-extrabold tabular-nums`.
- **Nav item (sidebar)**: rounded-xl row; active = `bg-primary/10 text-primary` + 1px×20px
  left indicator bar; inactive = muted with hover to foreground.
- **Chip/badge**: `rounded-full text-xs font-semibold bg-{color}/10 text-{color}`.
- **Input**: `rounded-xl border-border bg-surface-card focus:ring-2 ring-primary`,
  visible label above, error inline below in destructive.
- **List row**: `bg-surface-muted rounded-xl` with hover `border-primary/40` + chevron
  that nudges right 2px on hover.
- **Flag imagery**: `flagcdn.com/{cc}.svg`, rounded-lg, shadow-sm — flags are the app's
  recurring identity mark.

## Motion

- Micro-interactions 150–300ms, `ease-out` on entrances, exact properties (never `all`).
- Press feedback `active:scale-[0.98]` on buttons; hover lift on cards (shadow, not size).
- Page-section reveals: `.fade-up` / IntersectionObserver `.reveal` (welcome page only).
- Signature moments: welcome scroll-morph hero; aurora empty states (`.aurora-bg`).
- High-frequency actions (nav clicks, checkbox toggles) never animate beyond color.
- Everything respects `prefers-reduced-motion`.

## Layout

- App shell: fixed 256px sidebar (lg+), slim sticky blurred topbar, mobile slide-over.
- Content `max-w-7xl`, page padding `py-8 px-4 sm:px-6 lg:px-8`.
- Marketing (welcome): full-bleed hero, `min-height` sections, 96px gaps.

## Do's & Don'ts

- DO keep orange (light) / amber (dark) to ONE emphasis moment per view.
- DO use flags + destination photos as the visual layer; the UI stays quiet around them.
- DO give every empty state a composed layout (icon tile + heading + one-line pitch + CTA).
- DO use `tabular-nums` wherever digits align (stats, tables, dates).
- DON'T hardcode hex in views — tokens only (`bg-primary`, not `bg-teal-600`).
- DON'T mix gray families — light mode grays are cool, dark mode grays are warm stone.
- DON'T animate width/height or use `transition: all`.
- DON'T center everything — headers left-aligned in product UI; centered only on marketing.
- DON'T add second sidebars, purple gradients, or emoji-as-icons. Ever.

## Chart palette (Chart.js, read from CSS tokens at runtime)

Series order: primary → secondary → accent → destructive; grid = `--color-border`,
text = `--color-foreground`, doughnut borders = `--color-card`. Charts re-read tokens on
every render so they follow theme/palette automatically.
