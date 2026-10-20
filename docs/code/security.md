# Security model, history and open items

The checklist for new code is in [../rules/coding.md](../rules/coding.md) (Security checklist). This file records how
the plugin protects itself, what was fixed, and what is still open.

## Model

| Area | Protection |
| --- | --- |
| Admin screens | Menu capability = the first capability of the role chosen in Settings → Access (default `manage_options`) |
| Admin AJAX | `image_hover_settings`: the same plugin permission (`get_permissions_check()`) plus the nonce `image_hover_ultimate`; Danger zone actions also need `manage_options` |
| Front end | Shortcodes, block, widgets read a style by integer id with prepared SQL. One public AJAX action, `oxi_image_hover_load_more` (9.12.2): read only, takes only `styleid` and `page` (integers, page 2 to 1000), answers only for styles with Load More on (dynamic styles only with Pro) and builds the query on the server from the saved style (`post_status` publish); no nonce on purpose, a nonce in a cached page would expire |
| Rawdata | Editor saves: `sanitize_rawdata()` runs `wp_kses_post` on every value; custom CSS is refused when a `<` is left after the output steps (`ImageApi::custom_css_is_unsafe()`: `&nbsp;`, `<br>`, entities decoded until stable; editor save and JSON import); `</style>` removed before saving |
| CSS output | Front end: `print_late_styles()` removes `</style>` (exact text only, see open item 1) |
| SQL | `$wpdb->prepare` with `esc_sql` table names |
| Files | Layout JSON read only from inside the plugin (`realpath` check); import reads PHP's temp upload, needs the import nonce and `upload_files`, `.json` only |
| Class names from requests | `Menu::Image_Parent()` checks the row's type against `effects` and `class_exists()`; `ajax_action()` allows only `OXI_IMAGE_HOVER_PLUGINS` classes and `__rest_api_post` |
| Uninstall | Removes data only when the admin switched on "Delete data when the plugin is deleted" |
| Notices | Upgrade notice only on the plugin's pages, no tracking parameters (WordPress.org guidelines 7 and 11) |

## History

| Version | Fixed |
| --- | --- |
| 9.11.5 | Path traversal in `post_create_new()` and `post_web_import()` (`realpath` checks); missing permission check in `ajax_action()`; `</style>` removed before saving rawdata and before printing the footer CSS |
| 9.11.7 | Old rows saved with extra backslashes decode again in the edit and rearrange forms |
| next (after 9.12.1, not released) | Not a security fix: the classic widget and the WPBakery element registered too late and never appeared (2026-10-18). Security tested because the fix makes the widget reachable again: `security-tests/image-hover-effects-ultimate/widget-vc-attacks.php`, 38 of 38 passed (10 payloads in the widget's Style ID stored and rendered as visitor and admin, array input, REST encode as logged out 401, subscriber 403, admin 200, `[iheu_oxi_VC]` with 6 bad ids as visitor and admin). No new input or output path; Style ID is `wp_strip_all_tags` on save and `absint` on render. Same section (2026-10-19): the Custom CSS check (refused `font-style`, any CSS with the words "style" or "script") became `custom_css_is_unsafe()`, which refuses any `<` left after the same steps the output runs, so `&lt;`, `&#60;`, `&#x3c;`, double encoded and `<br>` split forms are all caught; Looping now outputs `type infinite` only for "yes"; a missing style shows visitors nothing. Tests: `bugfix-9122.php` 40 of 40 (11 attacks refused, 7 safe CSS allowed, 20000 random strings with no `<` reaching the page, save through `save_action()` refused for subscriber, author, editor and logged out), `editor-bugfix.mjs` 7 of 7 in the browser, render hashes 12 of 12 unchanged. Open item 1 still applies to the other style values. Same section (2026-10-19): Load More for visitors (open item 4) through the new public `ImageApi::load_more_action()`, the query built by `Display\Render\Effects1::load_more_query()` and `Compailer::load_more_query()` (shared with the page render); `Dynamic\Post_Query` returns only the empty marker when no posts are left. Tests: `load-more-attacks.php` 24 of 24 over HTTP as a visitor (draft, private and pending posts in the same category never returned, browser sent query arguments ignored, 12 bad ids and pages refused, dynamic styles silent without Pro, old action still refused), `load-more.mjs` visitor and admin (button and infinite: 3 to 6, 2 to 4 to 6, then the button goes), page 1 render identical apart from the button attributes, render hashes 12 of 12 unchanged |
| next (9.12.2), PHP 8 | Not a security fix but a page stopper (found 2026-10-20 from a Flipbox customer whose WPBakery page died after the flip box on PHP 8; Flipbox fixed it in 3.1.1). PHP 8 throws TypeError for arithmetic with an empty or text value and DivisionByZeroError for / 0. Image Hover had it in Lightbox 2 z-index, Comparison 4 strip count, Filter 2 item widths and the 140 calculations in old_render() (pre 9.3 styles; a style with empty rawdata crashed). Fix: `Public_Render::num()` and `::percent()` (empty or text counts as 0, like PHP 7) and `Filter\Render\Effects2::item_columns()`. Tests: `php8-fuzz.php` (all 140 layouts, original / empty / text / missing values, editor save and visitor render: 560 versions, 0 fatal; the old Lightbox 2 and Comparison 4 files fail it), `php8-num-check.php` (140 pairs, 5600 identical outputs with real numbers), `php8-old-style.php` (old General 1 DivisionByZeroError, fixed renders), a real page with an empty Lightbox 2 Z-index (old: cut after the gallery, fatal in debug.log; fixed: complete page), render hashes 12 of 12 unchanged. Keep using `num()` for any new calculation with a saved value |
| 9.12.0 | Item data sanitized value by value (`wp_kses_post` over the whole JSON had broken attribute quotes and crashed the render; damaged rows are repaired on read); Danger zone: delete all data needs `manage_options` and `confirm=DELETE` on the server; uninstall cleanup only on opt in |

## Open items (found while writing these docs, 2026-10-16, by reading the code; nothing was run or changed)

1. **Style CSS can close its `<style>` tag.** Control values go into the CSS as they are (`{{VALUE}}` in
   `Helper/Sanitization.php`). `wp_kses_post` keeps `&lt;/style &gt;` as text, `Public_Render::hooks()` turns it back
   into `</style >` with `html_entity_decode()`, and `print_late_styles()` removes only the exact `</style>`, so a
   value like `&lt;/style &gt;&lt;script&gt;...` would end the style tag and run a script for every visitor. Admin,
   Elementor and block preview output removes nothing at all. Who could do it: users with the plugin's role (admins by
   default, but Settings → Access can give it to Editors or Authors) and anyone who imports a crafted JSON file
   (`post_json_import()` stores the file's rawdata without sanitizing, only the custom CSS is checked). The Product
   Layouts fix for the same pattern (1.4.6): write every `</` as `<\/` in the CSS at each output point
   (`Assets::print_late_styles()`, the direct output in `Public_Render::hooks()`, `Modules/Gutenberg.php`, the nested
   styles in `Page/PreviewFrame.php`). **Waiting for Richard's go**; prove it with an attack before and after, as in
   Product Layouts.
2. **Who can change "Who can edit".** `post_oxi_image_user_permission()` only needs the plugin permission, so a user
   given access can widen it to a lower role. With item 1 this matters. Suggested: require `manage_options` for that
   setting, like the Danger zone.
3. **Preview frame without checks.** `wp_ajax_oxi_image_hover_preview_frame` has no capability or nonce check: any
   logged-in user (a subscriber too) can render any style by id in admin mode. The content is the same as the public
   shortcode, but the admin markup and the unfiltered nested CSS come with it. Suggested: the plugin permission check.
4. ~~Load more for visitors~~ FIXED IN CODE for 9.12.2: the public read-only `oxi_image_hover_load_more` described in
   the model. The old `image_hover_ultimate` action is unchanged and still refuses visitors; nothing in the plugin
   calls it any more.
5. The review notice's AJAX (`oxi_image_admin_notice`) checks the nonce but not the capability (low: it only stores
   a dismissal).
6. The role capability is cached for an hour and not cleared when the role changes.
