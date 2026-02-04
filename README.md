# SJ Shows Listing

A WordPress application that imports and displays show listings from the Official London Theatre API.

## Prerequisites

Before you begin, make sure you have the following installed locally:

- Docker
- [DDEV](https://ddev.readthedocs.io/en/stable/) (v1.22+)
- Node.js (v18+ LTS recommended)
- Composer
- Gulp
- direnv

---

## Setup from Scratch

### 1. Clone & Start DDEV

```bash
git clone <repository-url>
cd SJ-shows-listing
ddev start
```

This will:
- Start Docker containers
- Install Composer dependencies
- Generate local `.env` file
- Set up the database

### 2. Install WordPress

```bash
ddev wp core install \
  --url="https://sj-shows-listing.ddev.site" \
  --title="SJ Shows Listing" \
  --admin_user="admin" \
  --admin_password="admin" \
  --admin_email="admin@example.com"
```

### 3. Activate Plugins

```bash
ddev wp plugin activate advanced-custom-fields
ddev wp plugin activate olt-shows-importer
```

### 4. Import ACF Configuration

1. Go to WordPress Admin: https://sj-shows-listing.ddev.site/wp/wp-admin/
2. Login with `admin` / `admin`
3. Navigate to **ACF → Tools**
4. Click **Import**
5. Upload: `web/app/themes/Farlo/acf-json/acf-export-2026-02-04.json`
6. Click **Import JSON**

This creates the "Shows" custom post type and custom fields.

### 5. Build Theme Assets

```bash
cd web/app/themes/Farlo
npm install
npm run build
cd ../../../..
```

### 6. Activate Theme

```bash
ddev wp theme activate Farlo
```

### 7. Create Shows Archive Page

```bash
ddev wp post create --post_type=page --post_title="Shows" --post_status=publish --page_template=page-shows-archive.php
```

---

## Running the Importer

Import shows from the Official London Theatre API:

```bash
ddev wp import-shows
```

**Expected output:**
```
Starting OLT Shows Import...

Fetching shows from OLT API...
Found 153 shows to process
Fetching media data...
Fetched 153 media items

Success: Import completed!

  Created: 153
  Updated: 0
  Skipped: 0
```

### Re-running the Importer

The importer is **idempotent** - running it again updates existing shows without creating duplicates:

```bash
ddev wp import-shows
```

On subsequent runs: `Updated: 153` instead of `Created: 153`.

---

## Viewing the Site

| Page | URL |
|------|-----|
| Shows Archive | https://sj-shows-listing.ddev.site/shows/ |
| Single Show | https://sj-shows-listing.ddev.site/show/{slug}/ |
| WordPress Admin | https://sj-shows-listing.ddev.site/wp/wp-admin/ |

---

## Project Structure

```
SJ-shows-listing/
├── web/app/
│   ├── plugins/
│   │   └── olt-shows-importer/          # Show importer plugin
│   │       ├── olt-shows-importer.php
│   │       └── includes/
│   │           ├── class-api-client.php
│   │           ├── class-cli-command.php
│   │           ├── class-field-mapper.php
│   │           ├── class-image-handler.php
│   │           └── class-show-importer.php
│   └── themes/
│       └── Farlo/                        # Theme
│           ├── page-shows-archive.php    # Shows grid template
│           ├── single-show.php           # Single show template
│           └── src/assets/scss/
│           └── acf-json/acf-export-2026-02-04.json        # ACF configuration
|
└── README.md
```

---

## Data Mapping

| OLT API Field | Local Field | Transform |
|---------------|-------------|-----------|
| `id` | `olt_show_id` (meta) | Used for deduplication |
| `title.rendered` | `post_title` | HTML entities decoded |
| `content.rendered` | `post_content` | HTML preserved |
| `acf.show_opening_night` | `show_opening_night` (meta) | YYYYMMDD → d/m/Y |
| `acf.show_booking_until` | `end_date` (meta) | Priority over closing_night |
| `acf.show_closing_night` | `end_date` (meta) | Fallback if booking_until empty |
| `acf.show_ticket_urls[]` | `show_ticket_urls` (meta) | Comma-separated URLs |
| `acf.minimum_price` | `minimum_price` (meta) | Stored as string |
| `featured_media` | Featured Image | Sideloaded locally |

---

## Assumptions & Trade-offs

### Assumptions

1. **ACF Free is sufficient** - No ACF Pro features required for the custom fields.

2. **Date storage format** - Dates stored as `d/m/Y` (e.g., "03/03/2026") to match ACF date picker. Display format converted in templates.

3. **Single booking URL on cards** - While all URLs are stored, only the first displays on archive cards. Single show pages could show all.

4. **Local image storage** - Images are sideloaded (downloaded locally) rather than hotlinked, ensuring availability if source changes.

### Trade-offs

1. **Batch media fetching** - Fetches media in batches of 100 (2 requests vs 153). Uses more memory but significantly faster.

2. **On-demand imports** - Runs via WP-CLI command. For production, add a cron job:
   ```bash
   # Daily at 2am
   0 2 * * * cd /path/to/project && ddev wp import-shows
   ```

3. **Graceful error handling** - Individual failures logged but don't stop the import. One bad record won't prevent others.

4. **Fixed pagination** - 12 shows per page. Could be made configurable via ACF options.

---

## Development

### Watch mode for SCSS

```bash
cd web/app/themes/Farlo
npm run watch
```

### Useful Commands

```bash
# List all shows
ddev wp post list --post_type=show

# Delete all shows (testing)
ddev wp post delete $(ddev wp post list --post_type=show --field=ID) --force

# Check show meta
ddev wp post meta list <post_id>

# Rebuild theme
cd web/app/themes/Farlo && npm run build
```

---

## Troubleshooting

### Page shows partial HTML only

DDEV sync issue. Fix:
```bash
ddev restart
```

### SCSS not compiling

Reinstall dependencies:
```bash
rm -rf node_modules
npm install
npm run build
```
