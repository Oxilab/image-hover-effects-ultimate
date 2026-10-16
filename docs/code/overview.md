# Overview

Checked against the code on 2026-10-16 (version 9.12.1).

## Identity

| Field | Value |
| --- | --- |
| Plugin name (header) | Image Hover Effects Ultimate |
| Main file | `index.php` (final class `Oxilab_Imagehover`, singleton via `oxilab_imagehover()`; uses the traits `Helper\Public_Helper` and `Helper\Admin_helper`) |
| Version | `9.12.1` (header, `OXI_IMAGE_HOVER_PLUGIN_VERSION`, readme `Stable tag`); released, tag `v9.12.1` |
| Text domain | `image-hover-effects-ultimate` |
| Namespace | `OXI_IMAGE_HOVER_PLUGINS\` → plugin root (Composer PSR-4) |
| Minimums | PHP 7.4, WordPress 6.2 (readme); tested up to 7.1 |
| Freemius | `oxilab_iheu_v()`, id 20097, slug `oxi-image-hover-ultimate`, first path `admin.php?page=image-hover-ultimate-getting-started`, `has_paid_plans` true, `is_premium` false (Pro is a license on the same code), SDK in `vendor/freemius/wordpress-sdk` |
| Pro check | filter `oxi-image-hover-plugin-version` → `Admin_helper::check_current_version()`: legacy option `image_hover_ultimate_license_status` is `valid`, or `oxilab_iheu_v()->can_use_premium_code()` |
| License | GPLv2 or later |

## What it does

A builder for CSS image hover effects. The site owner picks an effect type (General, Caption, Flipbox, Button, Square,
Magnifier, Comparison, Lightbox, and the Extensions Display Post, Carousel, Filter), starts from a ready design, adds
image items (image, title, description, link, button) and styles them in a full-screen editor with a live preview.
Each saved style is a shortcode `[iheu_ultimate_oxi id="N"]`, also placed with a Gutenberg block, an Elementor
widget, a WPBakery element or a classic widget.

## Constants (`index.php`, `define_constance()`)

| Constant | Value |
| --- | --- |
| `OXI_IMAGE_HOVER_FILE` | Main file path |
| `OXI_IMAGE_HOVER_BASENAME` | `image-hover-effects-ultimate/index.php` |
| `OXI_IMAGE_HOVER_PATH` | Plugin folder path, with a trailing slash |
| `OXI_IMAGE_HOVER_URL` | Plugin URL **with** a trailing slash (`plugins_url('/', __FILE__)`), so assets use `OXI_IMAGE_HOVER_URL . 'assets/...'` |
| `OXI_IMAGE_HOVER_PLUGIN_VERSION` | `9.12.1` |
| `OXI_IMAGE_HOVER_TEXTDOMAIN` | `image-hover-effects-ultimate` |

## Boot sequence

1. `index.php`: Composer autoload, Freemius init (`oxilab_iheu_v()`, fires `oxilab_iheu_v_loaded`), Freemius
   `after_uninstall` → `Classes\Data_Cleaner::uninstall`.
2. `Oxilab_Imagehover::__construct()`: constants, activation hook (`Classes\Installation::plugin_activation_hook()`:
   creates the tables, sets the transient `oxi_image_hover_activation_redirect`), empty deactivation hook, fires
   `image-hover-effects-ultimate/before_init`, `init` priority 20 → `init_plugin()`.
3. `init_plugin()`:
   - `new Includes\Assets()` (admin and front-end assets, late CSS printer)
   - `new Classes\ImageApi()` (registers every AJAX action)
   - when `is_admin()`: `new Includes\Admin()` (Menu, an empty Notice, Account) and `User_Reviews()` (recommended
     plugins notice after 1 day, then the review notice after 7 days, or else the Pro upgrade notice after 10 days)
   - `Admin_Filters()`: registers `oxi-image-hover-support-and-comments`, `oxi-image-hover-plugin-version`,
     `oxi-image-hover-plugin/admin_menu` (written as hex in the code)
   - `Shortcode_loader()`: `[iheu_ultimate_oxi]`, `Modules\Visual_Composer`, `Modules\Elementor`,
     `Modules\Gutenberg`, `Modules\Widget`, and `widget_text` → `do_shortcode`
   - `Public_loader()`: table names on the main object
   - `register_image_hover_ultimate_update()`: while the option `image_hover_ultimate_update_complete` is not `done`,
     schedules the one-off cron `image_hover_ultimate_update`, which re-renders the CSS of every style
     (`ImageApi::update_image_hover_plugin()`).

## Request flow

```
Admin: Image Hover page → effect type (Templates) → "Use this design" → name dialog
       → AJAX image_hover_settings, functionname create_new (reads Modules/<Type>/Layouts/N/*.json)
       → editor (?page=oxi-image-hover-ultimate&effects=<type>&styleid=<id>)
       → Save → functionname elements_template_style → rawdata (JSON) + stylesheet (CSS) + font_family
       → items: elements_template_modal_data → rows in image_hover_ultimate_list

Front: [iheu_ultimate_oxi id="N"] / block / Elementor / WPBakery / widget
       → Oxilab_Imagehover::shortcode_render(N) → style row, style_name "general-6"
       → Modules\General\Render\Effects6 (extends Page\Public_Render)
       → HTML now, module CSS/JS enqueued, the style's CSS queued in Public_Render::$pending_late_css
       → Assets::print_late_styles() prints it in wp_footer (priority 5) as <style id="oxi-iheu-late-css">
       (Pro dynamic content and load more go through Modules\Compailer instead)
```

## Links

| What | URL |
| --- | --- |
| WordPress.org | https://wordpress.org/plugins/image-hover-effects-ultimate/ |
| Docs | https://oxilab.dev/docs/image-hover-effects/ |
| Demos | https://demos.oxilab.dev/imagehover/demos/ |
| Pricing | https://oxilab.dev/image-hover-effects/pricing/ |
| Support | https://wordpress.org/support/plugin/image-hover-effects-ultimate/ |

## Code docs in this folder

| File | Contents |
| --- | --- |
| [file-map.md](file-map.md) | Every folder and file, and where to make a change |
| [classes.md](classes.md) | Every class and trait outside `Modules/` |
| [admin-pages.md](admin-pages.md) | Every admin screen: URL, class, CSS, JS, AJAX |
| [modules.md](modules.md) | Effect types, effects, designs, Free and Pro, how a style renders |
| [hooks-shortcodes.md](hooks-shortcodes.md) | Hooks, AJAX functions, shortcodes, block, widgets, localized JS objects |
| [database.md](database.md) | Tables, options, transients, cron, data flow |
| [assets.md](assets.md) | CSS, JS, libraries, what loads where |
| [security.md](security.md) | Security model, audit history, open items |
