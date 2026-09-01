# Curly Alert Bar

Admin-controlled announcement bar for Curly Sprout sites, migrated from the
Fluent Snippets alert bar. Content is managed in **Alert Bar** in the wp-admin
sidebar and output via the `[alert_bar_text]` shortcode.

## Requires Oxygen 6 (built a specific way)

This plugin controls the alert bar's **content and visibility only**. The bar
itself must be built in Oxygen 6 as:

- An **Oxygen element with class `alert-bar`** (the visible bar, e.g. a Container
  at the top of the page).
- A **close button inside it with class `alert-bar-close`**.
- The **`[alert_bar_text]` shortcode** inside the element (in an Oxygen "Shortcode"
  element or a Code element) to render the admin's text.

Without those classes the plugin has nothing to show/hide.

## What it does

- **Admin page** (top-level "Alert Bar"): enable/disable toggle + a tinyMCE
  editor (300-char cap, bold/italic/links only) with a live character counter.
- **`[alert_bar_text]` shortcode**: outputs the stored text (safe HTML, escaped).
- **Front-end script** (`assets/js/alert-bar.js`, enqueued): hides the `.alert-bar`
  element when disabled, when the text is empty, or when the visitor dismissed it
  this session (`sessionStorage`); wires up the close button.

## Structure

```
curly-alert-bar/
├── curly-alert-bar.php              # Headers + PUC bootstrap
├── uninstall.php                    # Removes the two options on delete
├── assets/js/alert-bar.js           # Front-end visibility script (enqueued)
├── includes/
│   └── class-curly-alert-bar.php    # Admin page, settings, shortcode, enqueue
└── vendor/plugin-update-checker/    # YahnisElsts/plugin-update-checker v5.7
```

## Installation

1. Upload to `wp-content/plugins/` (or install the ZIP from the GitHub release).
2. Activate.
3. **Alert Bar** in the sidebar → enable + enter text → Save.
4. In Oxygen, add the bar element (class `.alert-bar` + a `.alert-bar-close`
   close button) and insert `[alert_bar_text]` inside it.

## Updates

Distributed through **GitHub Releases** via plugin-update-checker (public repo —
no tokens). Bump `Version:` in `curly-alert-bar.php`, then
`git tag v1.0.1 && git push origin v1.0.1`. The Action builds and attaches the
ZIP; installed sites show the update in wp-admin.

## Replacing Fluent Snippets

If the site previously ran this as the Fluent `11-alert-bar.php` snippet,
deactivate/delete that snippet when activating the plugin so the front-end
script and shortcode don't register twice.