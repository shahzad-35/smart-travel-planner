# Destination search: cities, capitals, states & provinces

User request: search should cover cities, capitals, provinces and states — not just countries. Scope: everywhere (Destinations page + Create/Edit Trip autocomplete). Results UI: grouped sections (Countries / States & Provinces / Cities).

Data sources (both verified live):
- Cities & capitals: OpenWeather Geocoding API `/geo/1.0/direct` (existing OPENWEATHER_API_KEY, free)
- States/provinces: offline dataset from dr5hn/countries-states-cities (5,308 entries, slimmed to 683KB)
- Countries: existing CountryService (offline dataset / v5 API)

## Todos

- [x] Generate `storage/app/states_offline.json` (slim: name, country_code, country_name, type, latitude, longitude)
- [x] `app/DTOs/DestinationDTO.php` — type (country|state|province|city), name, state, countryCode, countryName, latitude, longitude, flag
- [x] `app/Services/External/LocationService.php` — searchStates() (offline), searchCities() (OpenWeather geocoding, cached 1d, graceful failure), searchDestinations() returning grouped ['countries','states','cities']
- [x] DestinationSearch component + view — grouped sections with type badges; countries keep rich cards; states/cities compact cards; selectPlace() records recent search and opens the country page
- [x] CreateTrip + EditTrip — grouped autocomplete; selectDestination(group, index); city/state selection keeps place name as destination (better weather accuracy) with country_code for holidays
- [x] Recent searches — type stored, badge rendered, dedupe by type+code+name
- [x] Verify: tinker smoke tests, headless-browser searches ("punjab" → provinces, "lahore" → cities, "tokyo" in Create Trip), 15/15 unit tests, npm build

## Follow-up: state → cities drill-down (approved by user)

- [x] `cities` table (migration + `App\Models\City`) seeded with 152,970 cities from `storage/app/cities_offline.json.gz` (dr5hn linkage + GeoNames population, 81% pop-matched) via `CitySeeder`
- [x] `LocationService::getCitiesForState()` — population-ranked top 24 with total count
- [x] `LocationService::searchCities()` now DB-first (offline, ranked); OpenWeather geocoding kept as fallback when the table is unseeded
- [x] Drill-down UI in DestinationSearch, CreateTrip, EditTrip: state card expands to city chips + "Select/Use entire province" action; province stays selectable
- [x] Verified in headless Chrome: Punjab → "top 24 of 212" chips ordered Lahore/Faisalabad/Rawalpindi…; clicking a chip in Create Trip sets destination and advances to dates step; 15/15 unit tests; build clean

New files to commit: cities migration, `app/Models/City.php`, `database/seeders/CitySeeder.php`, `storage/app/cities_offline.json.gz` (2MB), plus earlier untracked files.
Setup on a fresh environment: `php artisan migrate && php artisan db:seed --class=CitySeeder`.

## Review

- New files: `app/DTOs/DestinationDTO.php`, `app/Services/External/LocationService.php`, `storage/app/states_offline.json` (must be committed).
- Modified: `DestinationSearch.php` + view, `CreateTrip.php` + view, `EditTrip.php` + view — `$searchResults` is now grouped (`['countries','states','cities']`); count checks use the `searchResultCount`/`resultCount` computed properties.
- Selecting a city/state in trip forms stores the place name as `destination` and its country code as `country_code`, so weather (city-accurate) and holidays (country-based) both keep working.
- City search degrades to empty (with log entry) if the OpenWeather key is missing/down; states/countries work fully offline.
- Verified in headless Chrome: grouped sections render on /destinations and in the Create Trip dropdown; no page errors; 15/15 unit tests; production build clean.


---

# Design Architecture Pass (2026-08-15, ui-ux-pro-max)

- [x] `design-system/smart-travel-planner/` — MASTER.md (identity locked to DESIGN.md) + page overrides (dashboard/trips/wizard/explore)
- [x] Component library `resources/views/components/ui/` — page-header, stat-card, empty-state, alert, chip, modal, skeleton, toast-hub (+cover pattern in views)
- [x] Global toast hub in app layout; 9 dead `session()->flash` calls → `notify` dispatches
- [x] Lazy `travel-stats` + `holiday-list` with skeleton placeholders; busy states on export/share buttons
- [x] Mobile bottom tab bar (Home/Trips/＋/Explore/Weather, safe-area, active states)
- [x] Grid stagger motion + spring modals + reduced-motion; skip link; aria-labels; token sweep (amber/emerald hardcodes removed)
- [x] Verified: browser suite (skeleton→charts, toast, modal, mobile bar), 15/15 tests, clean builds
- Welcome/guest/login untouched per user instruction.

## Weather auto-location (2026-08-15)
- [x] Add `GeoIpService` (ipwho.is, keyless) resolving visitor IP → city; private/reserved IPs fall back to server egress IP; 6h cache
- [x] `WeatherCard::mount` uses visitor's `request()->ip()`, queries OpenWeather as `City,CC`, displays "City, Country" + "Your location" chip
- [x] Fallback messaging when detection or weather fetch fails
- [x] Verified: unit tests 15/15; headless Chrome light+dark shows "Lahore, Pakistan · 29°" with 6-day forecast
- [x] Manual city search on weather page: `search()` action + form with busy spinner; "My location" reset button (re-detects via GeoIP); not-found message for unknown cities
- [x] Verified: Tokyo search → 24° w/ forecast; "Zzxqwv" → not-found message; My location → back to Lahore chip
- [x] Fix: typo'd city search poisoned later correct searches — 404s were retried 4x and tripped the ApiResponseHandler circuit breaker (open 60s blocks all weather calls). 4xx now returns to caller immediately: no retry, no breaker increment. Verified: Tokyoo → Tokyioo → Tokyo works instantly (24°); searches also faster (no pointless 404 retries).

## Loading-feedback pass (2026-08-15)
- [x] New x-ui.spinner primitive (literal size map for JIT)
- [x] Skeleton swaps: weather card (untargeted), weather-comparison add/units, destination search, create/edit-trip destination search + selected-destination, holiday-list dates, travel-stats refresh
- [x] Dim + "Updating…" row: trip-list (all filters/sort/pagination), country-info tabs, comparison unit switch
- [x] Busy buttons: export/share/delete (trip-details, packing), pack/unpack-all, add-item, status-change confirm, profile/password/delete-account/preferences saves, send-verification
- [x] Removed dead server flags: $isLoading (DestinationSearch, WeatherComparison), $isSearching (CreateTrip, EditTrip), $isUpdating (TripStatusManager)
- [x] Fixed mis-targeted wire:loading on createTrip/updateTrip buttons ("Updating…" no longer fires while typing)
- [x] app.css: at-rest hide rules for wire:loading.delay.{block,flex,grid,inline-flex}
- [x] Verified: view:cache clean, build clean, 15/15 unit tests, throttled-network CDP screenshots on weather/trips/destinations/stats/create-trip

## Hero scroll-back + creation overlay (2026-08-15)
- [x] welcome.blade.php: window scroll listener resets `virtual` to 0 when the page returns to top (scrollbar/keyboard/momentum paths the hero-only wheel handler never saw); smoothing animates cards back to the circle. Verified: card at [231,0] → arc [-217,2066] → back to [246,0].
- [x] create-trip + edit-trip: full-screen wire:loading overlay on createTrip/updateTrip (backdrop blur, spinner, "Creating your trip…"/"Saving your changes…"). Verified with 3s latency: overlay flex-visible mid-request, trip created and redirected (test trip deleted after).
- [x] Fix: creation overlay flashed — wire:loading hid it as soon as the server responded, before the (slow) navigation to trips.show. Now Alpine-armed on click and never hidden on success (persists through redirect); hidden by server events on validation failure ('trip-create-failed'), edit interruptions ('trip-save-interrupted': confirmation prompt, conflicts, exceptions), and a global 'livewire-request-failed' safety net in app.js. Verified: real click on step-4 → overlay visible whole time → /trips/N; failed create hides overlay + shows errors; test trips deleted.

## Wizard rebuild — split-panel + live trip ticket (2026-08-15)
- [x] New partials: wizard/stepper (glow active, clickable completed steps via goToStep, animated connectors), wizard/ticket (flag cover, boarding-pass notch divider, live rows: dates/duration/type/travelers/budget/weather/notes), wizard/type-icon (5 consistent SVGs)
- [x] create-trip.blade.php + edit-trip.blade.php rewritten: max-w-6xl split grid, mobile summary strip, richer grouped results, icon type cards w/ descriptions, travelers stepper, PKR budget field, review sections w/ Edit buttons, accent CTA
- [x] All contracts preserved: Alpine overlays, loading skeletons/spinners/targets, wire:keys, computed searchResultCount, deferred budget, backward-only goToStep, edit confirmation modal + saving events
- [x] CSS: .ticket-divider (notched boarding-pass rule)
- [x] Verified end-to-end: japan search → select (auto-advance) → dates → details → review → create → trips/49 → edit prefilled ticket; screenshots light/dark/mobile 390px; 15/15 tests; test trip deleted
- [x] Fix: welcome hero images overlapped hero text — (1) intro circle radius (min 350px) was narrower than the ~672px headline block, side cards sat on the copy → now an ellipse (rx min(W*0.42,470), ry min(H*0.40,340)) framing the text; (2) arc apex at 25% of hero height parked cards across the arc-phase headline/CTA → lowered to 55%. Verified screenshots: circle + arc states both clear of all text at 1280x900 and 1280x700.
- [x] Hero overlap, round 2: intro filmstrip now parades in the measured band between the subtitle and the CTAs (md:mt-32 opens it); ring is a TRUE circle (rx === ry) sized from per-LINE text measurement, not block widths. Added a render-time keep-out guard: each card is nudged out of any line's box using its exact rotated/scaled footprint, so no frame can paint a card on the copy. Fixed along the way: a slice edit had deleted the ResizeObserver + measureIntro() calls (geometry never ran); boxes now re-measured on font swap/late layout via element observers + a 5s settle poll.
- [x] Verified per-FRAME (rAF watcher, not sampling) across 1280x900/1280x720/1512x860/1024x768/1440x820: worst-overlap = 0 everywhere; strip visible in band; scroll morph + scroll-back-to-top reset still work; dark mode OK.
