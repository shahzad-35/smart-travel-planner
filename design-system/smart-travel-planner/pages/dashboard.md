# Page: Dashboard

Overrides MASTER for `/dashboard`.

- Opens with the **Next Adventure banner** (`x-ui.cover-card` pattern, full-width) when an upcoming trip exists.
- Stats load via `<livewire:travel-stats lazy />` with `x-ui.skeleton` placeholder — never block first paint on chart data.
- Stat tiles: gradient-tint pattern (primary/secondary/accent/neutral, one each — no repeats).
- Charts read colors from CSS tokens at render (never hardcoded hex).
- Card grids use `.stagger-grid` entrance; data tables never stagger.
- Empty state: aurora `x-ui.empty-state` with "Plan Your First Trip" CTA.
