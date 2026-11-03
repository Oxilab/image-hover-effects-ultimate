# Security model, history and open items

The checklist for new code is in [../rules/coding.md](../rules/coding.md) (Security checklist). This file records how
the plugin protects itself, what was fixed, and what is still open.

## Model

| Area | Protection |
| --- | --- |
| Admin screens | Menu capability = `ImageApi::plugin_capability()` (9.12.3): `manage_options` when Settings → Access is Administrator or nothing is saved, `edit_others_posts` for any other role, so it never goes below Editor (CVE-2026-118056). No cache |
| Admin AJAX | `image_hover_settings`: the same plugin permission (`get_permissions_check()`) plus the nonce `image_hover_ultimate`; Danger zone actions and "Who can edit" also need `manage_options`, and "Who can edit" takes only a role from `ImageApi::allowed_edit_roles()` (roles with `manage_options` or `edit_others_posts`) |
| Preview frame | `oxi_image_hover_preview_frame` (9.12.3): the plugin permission plus the nonce `image_hover_ultimate`, which the editor puts in the iframe link (`Admin_Render`) and the reload keeps |
| Inline JavaScript | Saved values written into an effect's inline script go through `Public_Render::js_string()` (JSON string, `<` `>` as `<` `>`) or `Public_Render::js_number()` (the saved number, or a fallback), never plain concatenation (9.12.3, CVE-2026-118056). URLs also go through `esc_url_raw()` first |
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
| next (9.12.3, not released) | **CVE-2026-118056** (Wordfence, reported 2026-11 by Kamil B., CVSS 4.9): stored XSS through inline JavaScript, reachable by a subscriber when "Who can edit" was Subscriber. Three causes, all fixed 2026-11-03: (1) `get_permissions_check()`, the menu and the review notice used the role's first capability (`read` for Subscriber), now `plugin_capability()` with an Editor floor; (2) any plugin user could change "Who can edit", now only `manage_options` and only to an allowed role; (3) Comparison 1 and 2, Lightbox 2, Magnifier 1 and 2, Carousel 1, 2 and 3 and the dynamic carousel (`Compailer`) concatenated saved values into inline scripts, now `js_string()` and `js_number()`; plus the preview frame had no capability or nonce check. Style rows still have no owner: an editor can edit every style, like editors edit every post. Tests: `cve-2026-118056.php` 268 of 268 (old code: 113 failed: `alert()` ran in 89 render cases, `</script>` left in the output in 7, 7 access, 6 setting and 4 preview frame checks, a subscriber overwrote the administrator's style): 6 string and 4 number payloads in every JavaScript field of 9 layouts, 5 roles times 5 "Who can edit" values over HTTP for the editor save and the admin page, the setting refused for editors, lower roles, unknown roles and payloads, the preview frame as visitor, subscriber, without nonce, with another user's nonce; `cve-2026-118056-browser.mjs` 17 of 17 (editor preview and reload as admin and editor, Settings list, save, locked for editors); `layout-js-dump.php` all 407 layout files and `render-js-dump.php` all 17 saved styles byte for byte identical HTML, CSS and inline JavaScript before and after; `bugfix-9122.php` 40 of 40, `widget-vc-attacks.php` 38 of 38, `builder-registration.php` all passed, `php8-fuzz.php` 0 fatal |
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
2. ~~Who can change "Who can edit"~~ FIXED IN CODE for 9.12.3 (CVE-2026-118056): `manage_options` only, allowed
   roles only.
3. ~~Preview frame without checks~~ FIXED IN CODE for 9.12.3 (CVE-2026-118056): plugin permission plus nonce.
4. ~~Load more for visitors~~ FIXED IN CODE for 9.12.2: the public read-only `oxi_image_hover_load_more` described in
   the model. The old `image_hover_ultimate` action is unchanged and still refuses visitors; nothing in the plugin
   calls it any more.
5. The review notice's AJAX (`oxi_image_admin_notice`) checks the nonce but not the capability (low: it only stores
   a dismissal).
6. ~~The role capability is cached for an hour~~ FIXED IN CODE for 9.12.3: `plugin_capability()` has no cache.
7. `post_elements_template_style()` builds the editor class from the posted `image-hover-template` and runs
   `new $cls` without `class_exists()`, so a bad template name ends that request with a fatal error (found
   2026-11-03, editors and administrators only, nothing is stored wrongly). Suggested: check `class_exists()`.
8. The License card on Settings shows the masked license key to everyone with the plugin permission, editors too
   (found 2026-11-03 in the browser test). Suggested: show the card only with `manage_options`, like the Danger zone.
9. `security-tests/image-hover-effects-ultimate/load-more-attacks.php` needs the ids of its TEMP Load More styles as
   arguments; without them 6 of 24 checks fail on old and new code alike (2026-11-03). Make the styles first.
