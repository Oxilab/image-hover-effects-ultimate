# Hooks, AJAX, shortcodes, block, widgets, JS objects

Checked against the code on 2026-10-16.

## Shortcodes

| Shortcode | Registered in | Output |
| --- | --- | --- |
| `[iheu_ultimate_oxi id="N"]` | `index.php` `Shortcode_loader()` | The style N (`shortcode_render($id, 'user')`) |
| `[iheu_oxi_VC id="N"]` | `Modules/Visual_Composer.php` | The same, used by the WPBakery element |

PHP use (the Shortcode page's "Copy PHP code"): `<?php echo do_shortcode('[iheu_ultimate_oxi id="N"]'); ?>`.
`widget_text` runs `do_shortcode`, so the shortcode works in Text widgets.

## Block, widgets

| Integration | Name in the UI | Details |
| --- | --- | --- |
| Gutenberg block | "Image Hover Effects" (`iheu/image-hover`, category Media, icon images-alt2) | `assets/blocks/image-hover/block.json`, attribute `styleId` (string); `Modules/Gutenberg.php` server render (`[iheu_ultimate_oxi]`), and in REST previews it inlines the CSS files and the queued style CSS; editor script `assets/backend/js/gutenberg-block.js` with `iheuBlockData` {shortcodes, editUrls, createUrl}; placeholder "Choose a style" |
| Elementor widget | "Image Hover" (`iheu_image_hover`, General category) | `Modules/Elementor.php`: control "Shortcode" (select of every style, default the newest), "Edit Shortcode" button (`elementor-edit-btn.js`); declares every module's style and script handles as dependencies; `editor_icon_style()` shows `image/logo.png` as the icon. The classic widget is left out of Elementor's own list (`elementor/widgets/black_list`, keyed by its object hash in `$wp_widget_factory`) and registered again as `Image_Hover_Classic_Elementor_Widget` (same name `wp-widget-iheu_widget`, `hide_on_search()` true), so it never shows in the panel but pages that use it still render |
| WPBakery element | "Image Hover Ultimate" (base `iheu_oxi_VC`, Content category) | `Modules/Visual_Composer.php`, dropdown "Select Image Hover Style" |
| Classic widget | "Image Hover Effects Ultimate" (`iheu_widget`) | `Modules/Widget.php`, one field "Style ID" (stored as `title`) |

## AJAX actions

| Action | Handler | Who |
| --- | --- | --- |
| `image_hover_settings` | `ImageApi::save_action()` → `post_<functionname>()` | Plugin role (`get_permissions_check()`) plus nonce `image_hover_ultimate` |
| `image_hover_ultimate` (also `nopriv`) | `ImageApi::ajax_action()` → `new <class>` with `__rest_api_post` (old load more, unused since 9.12.2) | Plugin role plus nonce; logged-out visitors are refused, see [security.md](security.md) |
| `oxi_image_hover_load_more` (also `nopriv`) | `ImageApi::load_more_action()` (Load More of Display Post and dynamic styles) | Public and read only: `styleid` and `page` only, query built on the server, see [security.md](security.md) |
| `oxi_image_hover_preview_frame` | `ImageApi::preview_frame_action()` | Any logged-in user (no capability or nonce check, see [security.md](security.md)) |
| `oxi_image_admin_notice` | `Support_Reviews::ajax_action()` | Nonce only (registered for `manage_options` users) |
| `oxi_image_admin_upgrade_notice` | `Support_Upgrade::ajax_action()` | Nonce plus `manage_options` |
| `oxi_image_admin_recommended` | `Support_Recommended::ajax_action()` | Nonce plus `manage_options` |

### `post_<functionname>` methods (`image_hover_settings`)

| Function name | Does | Called from |
| --- | --- | --- |
| `create_new` | New style from a layout JSON path (`realpath` checked inside the plugin), returns the editor URL | `create.js` |
| `elements_template_style` | Saves the editor settings (`rawdata`), rejects custom CSS containing "style" or "script", renders `stylesheet` | `editors.js` Save |
| `template_name` | Renames a style | `editors.js` Rename |
| `template_change` | Changes `style_name` (another design of the type) | `editors.js` |
| `elements_template_modal_data` | Inserts or updates an item | `editors.js` |
| `elements_template_modal_data_edit` | Returns an item's data for the form | `editors.js` |
| `elements_template_modal_data_clone` | Copies an item | `editors.js` |
| `elements_template_modal_data_delete` | Deletes an item | `editors.js` |
| `elements_rearrange_modal_data` / `elements_template_rearrange_save_data` | Lists items / saves a new order (re-inserts rows in the new order) | `editors.js` |
| `elements_template_rebuild_data` | Re-renders an old style | `editors.js` Rebuild |
| `elements_template_render_data` | Renders a style from unsaved settings | not called by the current JS |
| `layouts_clone` | Copies a style with its items under a new name | `shortcode.js` |
| `shortcode_delete` | Deletes a style and its items | `shortcode.js` |
| `shortcode_export` | Streams the style and items as a JSON file | Export link (GET) |
| `shortcode_active` / `shortcode_deactive` | Adds or removes a layout row in `oxi_div_import` | not called by the current JS |
| `web_import` | Imports from a `template/` folder that doesn't exist | old buttons, gone |
| `json_import` | Called directly by the import form, not over AJAX | `Shortcode::manual_import_style()` |
| `oxi_image_user_permission`, `image_hover_ultimate_mobile_device_key`, `oxi_addons_font_awesome`, `oxi_addons_way_points`, `oxi_addons_google_font`, `oxi_addons_custom_parent_class`, `oxi_image_support_massage` | Save one setting | `settings.js` |
| `oxi_image_hover_delete_data_on_uninstall` | Danger zone switch (`manage_options`) | `settings.js` |
| `oxi_image_delete_all_data` | Deletes all data, needs `{"confirm":"DELETE"}` and `manage_options` | `settings.js` |
| `notice_dissmiss` | Broken (reads an unset property); the notices use their own actions | none |

## Hooks the plugin adds

| Hook | Where | For |
| --- | --- | --- |
| `init` (20) | `index.php` | `init_plugin()` |
| `admin_menu` | `Includes/Admin/Menu.php` | Menu |
| `admin_enqueue_scripts`, `admin_body_class` | `Includes/Assets.php`, `Includes/Admin/Account.php` | Admin CSS, `oxi-iheu-editor`, `oxi-iheu-menu-page` |
| `wp_footer` (5) | `Includes/Assets.php` | `print_late_styles()` |
| `wp_enqueue_scripts`, `admin_enqueue_scripts` | `Includes/Assets.php` | `register_swiper_scripts()` (registers every front-end handle) |
| `elementor/editor/*`, `elementor/frontend/*`, `elementor/preview/*` | `Includes/Assets.php`, `Modules/Elementor.php` | Elementor editor and preview assets |
| `elementor/widgets/register` | `Modules/Elementor.php` | The widget, and the hidden classic widget |
| `elementor/widgets/black_list` | `Modules/Elementor.php` | Leaves the classic widget out of the Elementor panel |
| `enqueue_block_editor_assets` | `Modules/Gutenberg.php` | Block editor script and CSS |
| `vc_before_init` | `index.php` constructor → `register_vc_element()` | `vc_map` of the WPBakery element (hooked at plugin load, because WPBakery fires it on `init` priority 9) |
| `widgets_init` | `index.php` constructor → `register_widget()` | The classic widget (hooked at plugin load, because WordPress fires it on `init` priority 1) |
| `widget_text` | `index.php` `Shortcode_loader()` | Shortcodes in Text widgets |
| `admin_notices`, `admin_footer` | `Classes/Support_*`, `Settings.php` | Notices, Freemius license dialog |
| `image_hover_ultimate_update` (cron, single) | `index.php` | Re-render every style's CSS once |
| Freemius `after_uninstall`, `hide_account_tabs`, `templates/account.php` | `index.php`, `Includes/Admin/Account.php` | Cleanup, Account page |

## Hooks the plugin fires or offers

| Hook | Type | Use |
| --- | --- | --- |
| `image-hover-effects-ultimate/before_init` | action | Before `init_plugin()` is hooked |
| `oxilab_iheu_v_loaded` | action | Freemius is ready |
| `oxi-image-hover-plugin-version` | filter (hex `6f78692d696d6167652d686f7665722d706c7567696e2d76657273696f6e`) | `true` when Pro is active |
| `oxi-image-hover-plugin/admin_menu` | filter (hex `...2f61646d696e5f6d656e75`) | Prints the plugin menu bar |
| `oxi-image-hover-support-and-comments` | filter (hex `...737570706f72742d616e642d636f6d6d656e7473`) | Support message (prints nothing today) |
| `oxi_image_hover_has_pro_access` | filter | QA override for the upgrade notice only |

Hex strings in the code decode as: `image_hover_ultimate_license_status` (the legacy license option), `valid`,
`SupportAndComments`, `check_current_version`, `oxilab_admin_menu`.

## Localized JS objects

| Object | Script | Contents |
| --- | --- | --- |
| `image_hover_settings` | `oxilab-bootstrap` (all admin pages using `admin_js()`) | `ajaxurl`, `nonce` (`image_hover_ultimate`) |
| `ImageHoverUltimate` | `oxilab-bootstrap` | REST `root`, `nonce` (`wp_rest`); not used by the current admin JS |
| `oxi_image_admin_notice` | `admin-notice.js` | `ajaxurl`, `nonce` |
| `oxi_image_admin_recommended` | `admin-recommended.js` | `ajaxurl`, `nonce` |
| `iheuBlockData` | `gutenberg-block.js` | `shortcodes`, `editUrls`, `createUrl` |
| `oxi_image_dynamic_loader` | `Modules/Dynamic/Files/dynamic-loader.js` | `ajaxurl`, `nonce` (front end, Pro dynamic load) |
| `oxi_image_style_1_loader` | `Modules/Display/Files/style-1-loader.js` | `ajaxurl` (Display Post load more; the `nonce` is no longer used) |

## Editor iframe messages

The preview iframe (`iframe-button-forwarders.js`) posts `edit`, `clone` and `delete` messages to the editor
(`parent-message-receivers.js`), which runs the matching `editors.js` handlers; `preview-controllers.js` pushes live
CSS changes into `<style id="ih-dynamic-styles">` and switches device widths.
