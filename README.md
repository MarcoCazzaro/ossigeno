Ossigeno
========

Version: 3.0
WordPress theme based on TailwindCSS 4, powered by \_tw

---

## Demo data import

The theme ships with a content-import system that seeds a fresh WordPress installation with a complete working demo: home page, blog archive, sample posts, navigation menus, and global ACF options.

### Entry points

Two ways to run the import:

**WP-CLI** (recommended for local and staging environments):

```bash
wp eval-file bin/import-demo-data.php
```

Note that the bin folder path must be specified with the path where the code is placed, not the WordPress theme path.
You will be prompted to choose between conservative and destructive mode (see below).

**WordPress admin UI**: Tools → Importa Ossigeno

Fill in the path to `bin/images/`, choose a mode, and click *Avvia importazione*. Each step runs as a separate AJAX request so the page never times out, and thumbnails are regenerated in a final batch after all content has been imported.

---

### Required images

Place the following files in `bin/images/` before running the import:

| File | Purpose | Notes |
|------|---------|-------|
| `ossigeno-logo.svg` | Site logo (`custom_logo`) | Already present — copied from `theme/images/` |
| `ossigeno-placeholder.webp` | Placeholder image, cover, post thumbnails, section image | Already present — copied from `theme/images/` |
| `site-icon.png` | Site icon / favicon (512 × 512 px) | Add manually; skipped with a warning if missing |

The two `.webp`/`.svg` files are pre-seeded in `bin/images/` from the theme assets. The import logs a warning for any missing file and continues; no step is fatal.

---

### Import modes

**Conservative (default)** — each step checks whether content identified by its import GUID already exists. If it does, the step is skipped and logged as `[skip]`. Safe to re-run on a site that already has imported content without creating duplicates.

**Destructive** — a cleanup pass runs first and permanently deletes all content previously created by the import script (posts, pages, attachments, menus, ACF options). A fresh import is then performed from scratch. Use this to reset a demo site to a known state.

Content is tracked via a `ssnail-post-import-guid` postmeta key. The cleanup function targets only posts that carry this key, so manually created content is never touched.

---

### What gets imported

| Step | Content |
|------|---------|
| Site identity | Logo → `custom_logo` theme mod; site icon → `site_icon` option |
| ACF Options | Phone, WhatsApp number + CTA label, email, 1 sample office (city / address / Maps embed URL), placeholder image, scroll-to-top toggle |
| Sample posts | 3 published blog posts with featured images and placeholder text |
| Home page | Draft page set as static front page, built from 4 ACF blocks: `ossigeno/splash-cover`, `ossigeno/image-with-text`, `ossigeno/posts-grid`, `ossigeno/contact` (anchor: `#contatti`) |
| Blog page | Published page set as static posts page (`page_for_posts`) |
| Placeholder pages | Privacy Policy, Cookie Policy (empty, publish status) |
| Navigation menus | **Primary** (`menu-1`): Home, Blog, Contatti — **Footer** (`menu-2`): Privacy Policy, Cookie Policy |

All placeholder values (phone number, email, office address) are fictional and must be replaced with real client data before going live.

---

### Implementation files

| File | Role |
|------|------|
| `bin/import-demo-data.php` | WP-CLI entry point — prompts for mode, calls `ssnail_run_import()` |
| `bin/images/` | Directory of source images read during import |
| `theme/inc/import-functions.php` | All import logic: helpers, section functions, orchestrator |
| `theme/inc/admin-import.php` | Admin UI entry point — AJAX step runner and render function |

The shared logic lives entirely in `import-functions.php`; both entry points call the same `ssnail_run_import()` orchestrator. The admin entry point uses a transient (`ssnail_import_state`) to carry intermediate post IDs (home page, news page, placeholder pages) across AJAX requests.
