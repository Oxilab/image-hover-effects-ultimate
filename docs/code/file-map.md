# File map

Every folder and file (without `vendor/`, `.git/`, the layout JSON files and images), and where to make a change.
Checked 2026-10-16.

## Root

| File | Role |
| --- | --- |
| `index.php` | Plugin header, Freemius init (`oxilab_iheu_v()`), class `Oxilab_Imagehover`, shortcode `iheu_ultimate_oxi`, page builder loaders, update cron |
| `readme.txt` | WordPress.org readme: description, FAQ, screenshot captions, changelog |
| `CLAUDE.md` | Pointer to these docs (not shipped) |
| `composer.json` / `composer.lock` | PSR-4 autoload, Freemius SDK, dev tools (PHPCS, PHPUnit); not shipped |
| `phpcs.xml`, `phpunit.xml` | Coding standard and test config; not shipped |
| `.gitattributes` | What the release leaves out (`export-ignore`) |
| `.github/workflows/deploy-org.yml` | On a tag: deploy to WordPress.org SVN (10up action) |
| `.github/workflows/update-assets.yml` | On push to `trunk` (no such branch here): readme and assets only |
| `.wordpress-org/` | Banners, icons, the 11 screenshots (not in the zip, uploaded to SVN assets) |
| `docs/` | These docs (not shipped) |

## Classes/

| File | Role |
| --- | --- |
| `ImageApi.php` | The AJAX router and every admin action (`post_*` methods), rawdata sanitizing and decoding, the update job, the preview frame route |
| `Controls.php` | Constants for the editor's control types (`Controls::COLOR`, `TYPOGRAPHY`, `REPEATER`, ...) |
| `Data_Cleaner.php` | Danger zone "Delete all data" and uninstall cleanup; the list of every option the plugin owns |
| `Docs.php` | The "How to use?" menu: doc links, icons, the dropdown for the plugin menu bar and the editor header |
| `Installation.php` | Creates the three tables (`dbDelta`), activation and upgrade hooks |
| `Support_Reviews.php` | Review notice (after 7 days) and its dismissal AJAX |
| `Support_Upgrade.php` | Pro upgrade notice (free sites, after 10 days, plugin screens only) and its dismissal AJAX |
| `Support_Recommended.php` | "Recommended plugin" notice (list is empty in this version, so nothing shows) |

## Helper/ (traits)

| File | Role |
| --- | --- |
| `Admin_helper.php` | Used by the main class: the plugin's admin menu bar (`oxilab_admin_menu`), notice gates, Pro checks, `Admin_Filters()` |
| `Public_Helper.php` | Used by the main class and several pages: `shortcode_render()`, `name_converter()`, kses allow list |
| `CSS_JS_Loader.php` | Used by pages and the editor: `admin_js()` (Bootstrap, nonce objects), `admin_home()`, `admin_elements_frontend_loader()` (editor libraries), media scripts |
| `Sanitization.php` | Used by `Admin_Render`: the editor's control library (`add_control`, `add_group_control`, `add_responsive_control`, `add_repeater_control`, tabs, popovers, every `*_admin_control`) and how each control writes CSS |

## Includes/

| File | Role |
| --- | --- |
| `Assets.php` | Admin CSS per page, the editor body class, Elementor editor assets, registered front-end handles, `print_late_styles()` |
| `Admin.php` | Starts `Admin\Menu`, `Admin\Notice`, `Admin\Account` |
| `Admin/Menu.php` | The WordPress menu (Image Hover, Shortcode, Getting Started, Settings) and the router for the Image Hover page |
| `Admin/Account.php` | Brands the Freemius Account page (header, scoping wrapper, hides tabs) |
| `Admin/Notice.php` | Empty class |
| `Admin/Pages/Admin.php` | The Image Hover home: effect type cards |
| `Admin/Pages/Shortcode.php` | The Shortcode page: list, copy, clone, export, delete, import |
| `Admin/Pages/Settings.php` | Settings: License, Access, Mobile and touch, Assets, Advanced, Danger zone |
| `Admin/Pages/GettingStarted.php` | Getting Started: video, three steps, page builders, help |
| `Installation.php` | **Unused** Flipbox copy (`oxi_div_style` tables); don't call it |
| `Frontend.php` | **Unused**, empty |

## Page/

| File | Role |
| --- | --- |
| `Admin_Render.php` | Abstract base of every effect editor: data loading, the editor page, header, preview iframe, item modal, CSS generation (`template_css_render`), font list |
| `Public_Render.php` | Abstract base of every front-end render: loading, wrapper, asset output, inline CSS and JS, helpers (`media_render`, `url_render`, `column_render`, `text_render`, ...), tolerant rawdata decoding |
| `Create.php` | Base of each effect type's template library page (cards, "Use this design", the naming dialog) |
| `PreviewFrame.php` | The editor's preview iframe document (`admin-ajax.php?action=oxi_image_hover_preview_frame`) |

## Modules/

| Path | Role |
| --- | --- |
| `<Type>/<Type>.php` | Template library page of the type (extends `Page\Create`, sets `pre_active`, the free layouts) |
| `<Type>/Modules.php` | Editor controls shared by the type's effects (extends `Page\Admin_Render`, uses the `Dynamic` trait) |
| `<Type>/Admin/EffectsN.php` | Editor of effect N (usually only `register_effects()` differs) |
| `<Type>/Render/EffectsN.php` | Front-end HTML of effect N (extends `Page\Public_Render`, `public_css()` and `default_render()`) |
| `<Type>/Files/` | CSS (`<type>.css`, `style-N.css`) and the libraries some types need |
| `<Type>/Layouts/N/*.json` | Ready designs for effect N (`style` row plus `child` rows) |
| `Dynamic.php` | Trait: Pro dynamic content, carousel and load more controls |
| `Dynamic/Post_Query.php`, `Dynamic/Layouts_Query.php`, `Dynamic/Files/` | Pro dynamic render and its load more |
| `Compailer.php` | Render used when a style has dynamic content, dynamic load or dynamic carousel on (Pro) |
| `Display/Files/*Query.php`, `style-1-loader.js` | Display Post queries and load more |
| `Gutenberg.php` | The "Image Hover Effects" block (server render, editor data) |
| `Elementor.php` | The "Image Hover" Elementor widget |
| `Visual_Composer.php` | The WPBakery element and its `[iheu_oxi_VC]` shortcode |
| `Widget.php` | The classic widget "Image Hover Effects Ultimate" |

Types: Button, Caption, Carousel, Comparison, Display, Filter, Flipbox, General, Lightbox, Magnifier, Square
([modules.md](modules.md)).

## assets/

| Path | Role |
| --- | --- |
| `assets/backend/css/` | Admin CSS (see [assets.md](assets.md)) |
| `assets/backend/js/` | Admin JS: editor, create, home, shortcode, settings, block, notices, preview bridge, vendor libraries |
| `assets/blocks/image-hover/block.json` | Block definition (`iheu/image-hover`) |
| `assets/frontend/css/` | `style.css` (base), `animation.css` (entrance animations), `font-awsome.min.css` |
| `assets/frontend/js/` | `oxi-jquery.js` (animations), `touch.js`, `waypoints.min.js`, Elementor helpers |
| `assets/frontend/webfonts/` | Font Awesome fonts |

## image/

Logos (`logo.png`, `sm-logo.svg`, `dash-icon.svg`) and the effect type icons in `image/icons/`.

## Where to make a change

| Change | File |
| --- | --- |
| A new admin AJAX action | `Classes/ImageApi.php`: a `post_<name>()` method, called with `functionname: '<name>'` |
| A setting on the Settings page | `Includes/Admin/Pages/Settings.php` (markup), `ImageApi::post_<option>()` (save), `assets/backend/js/settings.js`, `Data_Cleaner::SETTINGS` |
| An editor control for all effects of a type | `Modules/<Type>/Modules.php` |
| One effect's markup | `Modules/<Type>/Render/EffectsN.php` and its `Files/style-N.css` |
| A new ready design | `Modules/<Type>/Layouts/N/<file>.json` (export a style from the Shortcode page as the starting point) |
| Which designs are free | `Modules/<Type>/<Type>.php`, `pre_active` |
| A doc link in the plugin | `Classes/Docs.php` and the `doc` keys in `Includes/Admin/Pages/Admin.php` |
| Plugin header and menu bar | `Helper/Admin_helper.php::oxilab_admin_menu()`; editor header `Page/Admin_Render.php::oxi_admin_edit_page_header()` |
