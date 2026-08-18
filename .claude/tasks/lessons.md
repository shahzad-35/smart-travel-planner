# Lessons

## Design work: use the referenced source, be bold (2026-08-14)

**Mistake pattern:** User asked for a redesign "using 21st.dev" and expecting striking modern visuals (animated heroes, scroll effects). I used 21st.dev only to pick colors and built a conventional admin-panel shell. User called it "just normal."

**Rules for next time:**
1. When the user names a design source (21st.dev, Dribbble, a reference site), actually mine it: search, download previews, LOOK at them, and port the strongest components — don't just take color inspiration.
2. "Beautiful/modern/appealing" from this user means motion and drama (scroll-driven animation, morphing layouts, marquees) — not clean-but-safe layouts.
3. Landing pages are where boldness belongs; port real animated components (vanilla JS if the stack has no React) and verify each animation phase with screenshots.

## 419 CSRF debugging (2026-08-13)

**Mistake pattern:** Spent 5+ attempts changing redirect styles to fix login/logout 419s — all symptom-level guesses.

**Root cause:** Logout regenerates the session CSRF token, invalidating every previously rendered page (other tabs, history). Fixed by sourcing the token from the XSRF cookie (Livewire hook + middleware preference).

**Rules:**
1. When a fix fails twice, STOP guessing — add diagnostic logging and reproduce in a real browser (headless Chrome via CDP).
2. "Works in my clean test but not for the user" → suspect stale tabs, back-button pages, second sessions.
3. Livewire 3 bundles Alpine; a second `import Alpine` in bootstrap.js is a recurring regression.

## Circuit breaker vs. user typos (2026-08-15)
User-visible bug: after a misspelled city search, even the corrected spelling failed with "city not found". Root cause was NOT the data cache (Laravel `remember` never treats stored null as a hit) — it was `ApiResponseHandler`: a 404 was retried 4x, each retry incremented the circuit-breaker counter, so 2 typos (8 failures ≥ threshold 5) opened the breaker for 60s and blocked ALL weather calls.
Lesson: deterministic 4xx client errors must be returned to the caller immediately — never retried, never counted toward circuit-breaker health. Only 5xx/network/timeout failures indicate the API is unhealthy. When a "wrong cache" symptom is reported, check resilience middleware (breakers, rate limiters, negative caches) before the data cache.

## wire:loading display modifiers + .delay (2026-08-15)
Two Livewire 3 gotchas found while adding loading skeletons:
1. `wire:loading` reveals elements as `display: inline-block` by default — a revealed skeleton wrapper silently loses its grid/flex layout. Always add the display modifier: `wire:loading.delay.grid`, `.flex`, `.block`.
2. Livewire's injected at-rest CSS (`display:none`) only covers the modifier combinations it knows (`.delay`, `.delay.longest`, `.grid`, …) — `.delay.grid`-style combos are NOT in the list, so those elements render permanently until app.css adds matching doubled-specificity rules (see end of resources/css/app.css).
Also: server-side `$isLoading` flags in Livewire can never render as true for single-request actions (the browser only repaints after the round-trip) — they're dead UI; use wire:loading.
