# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Ossigeno is a WordPress **classic theme** (v3.0) built on the [_tw](https://github.com/_tw) starter and TailwindCSS 4. It is the Snappysnail agency's reusable starter: a single project is forked into new client themes via `bin/new-project.sh`. The repo *is* the theme source — the `theme/` directory is what gets symlinked into a WordPress install as the active theme.

## Critical naming convention

Two distinct token namespaces coexist and must not be confused:

- **`ssnail` / `SSNAIL`** — the agency namespace (text domain helpers, function/constant prefix like `ssnail_setup`, `SSNAIL_VERSION`, `ssnail-post-import-guid`). This is **permanent and must never be renamed**, including by the fork script.
- **`ossigeno` / `Ossigeno` / `ossigeno_`** — the project identity (theme slug, theme name, `@package`, `ossigeno` text domain, `ossigeno/*` block namespace). `bin/new-project.sh` rewrites *only* these tokens when forking to a new client.

When adding new globals (functions, constants, hooks, transients, postmeta keys), prefix them with `ssnail` so they survive the fork. (Note: some block-registration functions historically use the `ossigeno_` prefix — match the surrounding file rather than introducing a third pattern.)

PHPCS enforces this: `WordPress.NamingConventions.PrefixAllGlobals` requires the `ssnail` prefix, and `WordPress.WP.I18n` requires the `ossigeno` text domain.

## Build & development commands

Build outputs (`theme/style.css`, `theme/style-editor.css`, `theme/js/*.min.js`, `tailwind-intellisense.css`) are generated from sources via PostCSS + esbuild. The generated files **are committed** (not gitignored).

```bash
npm install && composer install   # install toolchains
npm run dev                        # one-off build of all CSS + JS
npm run watch                      # rebuild on change (run while developing)
npm run prod                       # minified production build
npm run bundle                     # production build + zip the theme (npm run zip alone just zips)
```

JS/CSS linting:

```bash
npm run lint        # eslint + prettier --check across the repo
npm run lint-fix    # eslint --fix + prettier --write
```

PHP linting (WordPress Coding Standards via PHPCS; targets PHP 7.4+, WP 6.2+):

```bash
composer run php:lint            # full sniff
composer run php:lint:changed    # only changed (unstaged) files — fastest during work
composer run php:lint:autofix    # phpcbf autofix
composer run make-pot            # regenerate theme/languages/ossigeno.pot (needs WP-CLI)
```

There is **no test suite** in this repo.

## Architecture

### Theme bootstrap
`theme/functions.php` is the entry point. It defines `SSNAIL_VERSION` (used for cache-busting; replaced with a base-36 timestamp by `npm run bundle`) and `SSNAIL_TYPOGRAPHY_CLASSES`, sets up theme support, then `require`s the modules in `theme/inc/`: `template-tags.php`, `template-functions.php`, `widget-areas.php`, `customizer.php`, `custom-post-types.php`, `shortcodes.php`, `acf-blocks.php`, `acf-blocks/acf-fields.php`, `admin-import.php`.

### Styling pipeline (Tailwind 4)
- Edit **design tokens** in `tailwind/tailwind-theme.css` (`@theme {}` block) and custom layers under `tailwind/custom/`. You usually don't edit `tailwind.css` (the entry that orchestrates imports).
- Tokens bridge to WordPress: `@theme` color/container variables reference `var(--wp--preset--color--*)` from `theme/theme.json`. Changing a palette means editing **both** `theme.json` (the WP preset) and the Tailwind token if adding new ones.
- `postcss.config.js` builds three targets selected by the `_TW_TARGET` env var: `frontend` (`theme/style.css`), `editor` (`theme/style-editor.css`, Preflight scoped to `.not-prose`), and `intellisense`. Production minification is gated by `_TW_ENV=production`.
- Fonts are dynamic: `ssnail_enqueue_dynamic_fonts()` in `inc/customizer.php` enqueues Google Fonts based on Customizer theme mods and injects a `:root` override so the `--font-headings` / `--font-body` Tailwind variables reflect the user's choice at runtime.

### ACF blocks (the main content system)
Custom blocks live in `theme/inc/acf-blocks/<block-name>/` and depend on **Advanced Custom Fields** being active. Each block is a folder with `block.json` (namespaced `ossigeno/*`, category `ossigeno-sections`), a `<block>.php` render template, and optional `.css`/`.js`.

- `inc/acf-blocks.php` auto-registers every subdirectory containing a `block.json` via `register_block_type()`. The directory list is cached in the `ssnail_acf_block_dirs` transient; the cache is flushed on `switch_theme` / `upgrader_process_complete`. **When adding or removing a block folder, the transient must expire (a day) or be flushed for the change to take effect.**
- ACF field groups are **registered in code**, not the database — see the large `inc/acf-blocks/acf-fields.php` (`acf/include_fields` hook). Field keys/labels are in Italian. Keep field group registration in sync when changing a block's fields.

### Demo data import
A content-seeding system seeds a fresh install (home page, blog, sample posts, menus, ACF options). Two entry points share one orchestrator `ssnail_run_import()` in `theme/inc/import-functions.php`:
- WP-CLI: `wp eval-file bin/import-demo-data.php` (prompts conservative vs. destructive).
- Admin UI: **Tools → Importa Ossigeno** (`inc/admin-import.php`), runs each step as a separate AJAX request, carrying intermediate post IDs in the `ssnail_import_state` transient.

Imported content is tagged with `ssnail-post-import-guid` postmeta. Conservative mode skips already-imported items; destructive mode deletes only posts carrying that key (manual content is never touched). Source images go in `bin/images/`. See `README.md` for the full step/field breakdown.

## Forking to a new client project

`bin/new-project.sh <slug> "<Theme Name>" [target-dir]` copies the starter (excluding `.git`, `node_modules`, `vendor`, `*.zip`), rewrites the `ossigeno`/`Ossigeno` identity tokens (leaving `ssnail` intact), renames matching files, re-inits git, and symlinks `theme/` into `wp/wp-content/themes/<slug>`. The local WordPress install lives under `wp/` (gitignored) and is run via DDEV (`--docroot=wp`, PHP 8.3).
