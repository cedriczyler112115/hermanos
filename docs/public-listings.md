# Public Events / Gallery / Performances: Pagination + Search

## Overview
The public-facing **Events**, **Gallery**, and **Performances** pages support:
- Server-side pagination with configurable page size (default 12; options: 12/24/48)
- Search filtering by `title`, `description` (where applicable), and `tags`
- SEO-friendly query parameters that preserve page state
- Real-time search (300ms debounce) and AJAX pagination with loading + error states

## URL / Query Parameters (API Spec)
All three endpoints support the same query string parameters:

### Endpoints
- `GET /events`
- `GET /gallery`
- `GET /performances`

### Query parameters
- `q` (string, optional): Search query.
- `per_page` (int, optional): Page size. Allowed values: `12`, `24`, `48`. Default: `12`.
- `page` (int, optional): 1-based page index. Default: `1`.

### Response modes
- **HTML (default)**: returns the full page.
- **JSON (AJAX mode)**: when `Accept: application/json` is sent, returns:
  - `html` (string): rendered results HTML (cards/grid + pagination controls)

## User Guide
### Events
1. Go to `/events`.
2. Type in the search box (updates results after ~300ms).
3. Change “per page” to 12/24/48.
4. Use the pagination controls (First/Prev/Next/Last + 5-number window).

### Gallery
1. Go to `/gallery`.
2. Use the same search and per-page controls to filter albums.
3. Click an album to open its photos.

### Performances
1. Go to `/performances`.
2. Search and paginate with the same controls.
3. Click a card to open the playable YouTube modal.

## Screenshot Checklist (Before / After)
Add screenshots to verify layout and behavior across breakpoints.

### Desktop (≥1280px)
- Events: default load, search “fiesta”, page navigation (page 2)
- Gallery: default load, search “outreach”, per_page=48
- Performances: default load, search “special”, open modal

### Tablet (768–1024px)
- Confirm controls stack correctly and pagination remains usable.

### Mobile (<768px)
- Confirm search input and per-page selector remain easy to tap.
- Confirm loading indicator appears during search and pagination.

Suggested filenames (store in your own location):
- `events-desktop-before.png`, `events-desktop-after.png`
- `gallery-mobile-before.png`, `gallery-mobile-after.png`
- `performances-tablet-before.png`, `performances-tablet-after.png`

