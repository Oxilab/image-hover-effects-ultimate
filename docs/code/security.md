# Security model, history and open items

The checklist for new code is in [../rules/coding.md](../rules/coding.md) (Security checklist). This file records how
the plugin protects itself, what was fixed, and what is still open.

## Model

| Area | Protection |
| --- | --- |
| Admin screens | Menu capability = the first capability of the role chosen in Settings → Access (default `manage_options`) |
| Admin AJAX | `image_hover_settings`: the same plugin permission (`get_permissions_check()`) plus the nonce `image_hover_ultimate`; Danger zone actions also need `manage_options` |
| Front end | Shortcodes, block, widgets read a style by integer id with prepared SQL; no public AJAX works for visitors |
| Rawdata | Editor saves: `sanitize_rawdata()` runs `wp_kses_post` on every value; custom CSS with "style" or "script" is refused; `</style>` removed before saving |
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
4. **Load more for visitors (functional, not a hole).** Display Post and dynamic load call `image_hover_ultimate`,
   which since 9.11.5 refuses anyone without the plugin permission, so visitors get nothing. A fix needs a separate
   read-only public endpoint that accepts only a style id and a page number and builds the query on the server
   (today the query arguments come from the browser).
5. The review notice's AJAX (`oxi_image_admin_notice`) checks the nonce but not the capability (low: it only stores
   a dismissal).
6. The role capability is cached for an hour and not cleared when the role changes.
