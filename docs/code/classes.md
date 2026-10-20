# Classes and traits (outside Modules/)

Checked against the code on 2026-10-16. Modules are in [modules.md](modules.md).

## Classes\ImageApi

The one AJAX router. A new instance is made on every request by `init_plugin()`; its constructor sets the table
names and registers the actions (`build_api()`).

| Method | What it does |
| --- | --- |
| `save_action()` | Handler of `wp_ajax_image_hover_settings`. Checks `get_permissions_check()` and the nonce `image_hover_ultimate`, reads `functionname`, `rawdata` (through `sanitize_rawdata()`), `styleid`, `childid`, calls `post_<functionname>()`, then prints the result as kses-cleaned JSON, a URL, or HTML through `wp_kses($allowed_tags)`, and `wp_die()` |
| `ajax_action()` | Handler of `wp_ajax_image_hover_ultimate` and `wp_ajax_nopriv_image_hover_ultimate`. Permission check, nonce, then `new $class` for a class name inside `OXI_IMAGE_HOVER_PLUGINS` with `functionname` `__rest_api_post` only. Nothing in the plugin calls it since 9.12.2 |
| `load_more_action()` | Handler of `wp_ajax(_nopriv)_oxi_image_hover_load_more` (9.12.2): public, read only. Takes `styleid` and `page`; Display Post styles go to `Display\Files\Style_1_Post_Query`, dynamic styles (Pro) to `Dynamic\Post_Query` or `Dynamic\Layouts_Query`, with the query from `load_more_query()` of the render |
| `preview_frame_action()` | Handler of `wp_ajax_oxi_image_hover_preview_frame`: loads `Page/PreviewFrame.php` |
| `get_permissions_check()` | The role from `oxi_image_user_permission` → its first capability (default `manage_options`), cached 1 hour in the transient `oxi_image_user_permission_role`, then `current_user_can()` |
| `sanitize_rawdata()`, `kses_json_array()` | Decode the editor JSON and `wp_kses_post` every key and string value, encode again (raw `wp_kses_post` over the JSON broke attribute quotes in older versions) |
| `decode_rawdata()` | Tolerant decode for old rows (plain, `stripslashes`, repaired quotes) |
| `validate_post()`, `allowed_html()` | Decode `rawdata` and `wp_kses` with the class's allow list |
| `update_image_hover_plugin()` | Re-renders the CSS of every style, then sets `image_hover_ultimate_update_complete` to `done` |
| `post_*` | The admin actions, listed in [hooks-shortcodes.md](hooks-shortcodes.md) |

## Classes\Data_Cleaner

Constants `UNINSTALL_OPTION` (`oxi_image_hover_delete_data_on_uninstall`), `MODULES`, `SETTINGS`, `OPTIONS`,
`SHARED_OPTIONS`, `LICENSE_OPTIONS`, `TRANSIENTS`. `counts()` for the confirmation dialog; `delete_now()` (Danger
zone: deletes rows, not the tables, so ids are never reused, keeps install state and license); `uninstall()` (per
site on multisite, only where the site opted in: drops the two tables and removes every option). Rows of the shared
`oxi_div_import` table are removed only for the types `<module>-ultimate`; the shared options `oxi_addons_font_awesome`
and `oxi_addons_google_font` stay while another Oxilab plugin is installed.

## Classes\Docs

`links()` (5 guides plus "Browse all documentation"), `nav_item()` for the plugin's menu bar, `editor_item()` for the
editor header. Styled by `howto.css`.

## Classes\Installation

`Image_Hover_Database()` creates `image_hover_ultimate_style`, `image_hover_ultimate_list` and `oxi_div_import`
with `dbDelta` and adds `oxi_image_hover_version`. Called on activation and by `shortcode_render()` when a style row
has no `rawdata` column (very old installs).

## Classes\Support_Reviews, Support_Upgrade, Support_Recommended

| Class | Shown when | Dismissal |
| --- | --- | --- |
| `Support_Reviews` | 7 days after `oxi_image_hover_activation_date`, until `oxi_image_hover_nobug` is set; site-wide admin screens | AJAX `oxi_image_admin_notice` (nonce only): `maybe` resets the date, anything else sets `nobug` |
| `Support_Upgrade` | Free sites (`has_pro_access()` false), 10 days after `oxi_image_hover_upgrade_date` (seeded from the install date), until `oxi_image_hover_upgrade_nobug`; only on the plugin's own pages, never in the editor (WordPress.org guideline 11) | AJAX `oxi_image_admin_upgrade_notice` (nonce and `manage_options`) |
| `Support_Recommended` | 1 day after install, until `oxi_image_hover_recommended` is set; its plugin list is empty, so it renders nothing | AJAX `oxi_image_admin_recommended` |

The review and upgrade notices never show together (`User_Reviews()` tries the review first). `admin.css` overrides
on plugin screens are handled by scoped rules in `notice.css`; `admin-notice.js` moves the notices below the plugin's
menu bar.

## Helper\Admin_helper (trait of the main class)

`oxilab_admin_menu()` (the plugin menu bar: Image Hover, Shortcode, "How to use?", Upgrade on free sites, Demo, Docs,
Support, Settings icon), `User_Reviews()`, `admin_notice()`, `admin_upgrade_notice()`, `admin_recommended()`,
`has_pro_access()` (filterable with `oxi_image_hover_has_pro_access`, only for the notice), `check_current_version()`,
`installation_date()`, `upgrade_notice_date()`, `Admin_Filters()`, `fixed_data()` (hex decode).

## Helper\Public_Helper

`shortcode_render($id, $user)`: validates the id, loads the row (admins see a red message for a missing id or class,
visitors see nothing since 9.12.2), sends Pro dynamic styles to `Modules\Compailer`,
otherwise loads the child rows and runs `Modules\<Type>\Render\EffectsN`. Also `name_converter()`,
`font_familly_charecter()`, `icon_font_selector()`, `allowed_html()`.

## Helper\CSS_JS_Loader

`admin_js()` (Bootstrap, `pro-modal.js`, localizes `ImageHoverUltimate` {root, wp_rest nonce} and
`image_hover_settings` {ajaxurl, nonce `image_hover_ultimate`}), `admin_home()` (jQuery UI, DataTables),
`admin_elements_frontend_loader()` (the editor's libraries: coloring-pick, minicolors, noUiSlider, icon picker,
conditionize, select2, serializejson, fontselect, `vendor.js`, media uploader).

## Helper\Sanitization

The editor's control library used by every `Modules.php`. Pattern:

```
$this->start_controls_section('id', ['label' => ..., 'showing' => true]);
$this->add_control('image_hover_heading_color', $this->style, [
    'label' => 'Color', 'type' => Controls::COLOR, 'default' => '#ffffff',
    'selector' => ['{{WRAPPER}} .oxi-image-hover-heading' => 'color: {{VALUE}};'],
]);
$this->end_controls_section();
```

Each control prints its field and, while `template_css_render()` runs, writes `selector → property` into
`$this->CSSDATA[laptop|tab|mobile]`, with `{{WRAPPER}}` = `.oxi-image-hover-wrapper-<id> .oxi-addons-row` and
`{{VALUE}}` = the saved value **as is** (see [security.md](security.md)). Group controls: typography, background,
border, boxshadow, textshadow, animation, media, url, column. `add_repeater_control`, `add_rearrange_control`,
popovers, responsive switchers, conditions (`forms_condition`).

## Includes\Assets, Admin, Admin\Menu, Admin\Account

Covered in [admin-pages.md](admin-pages.md) and [assets.md](assets.md). `Menu::Image_Parent()` routes the Image
Hover page: with `effects` and `styleid` it checks the row's type matches `effects` and runs
`Modules\<Type>\Admin\EffectsN`; with `effects` only it runs `Modules\<Type>\<Type>` (template library); otherwise
`Pages\Admin`. Unknown classes end in `wp_die('Invalid URL.')`.

## Page\Admin_Render (abstract)

Constructor: table names, `oxiid` from `$_GET['styleid']`, wrapper classes; unless called with `'admin'`, runs
`hooks()` (editor assets, the style row, child rows, `rawdata` decode, font list) and `render()` (preloader, header,
left settings form `#oxi-addons-form-submit` with Rebuild, Reload, Save, the preview iframe `#oxi-preview-iframe`,
device buttons, the item modal `#oxi-addons-list-data-modal`). `template_css_render($style)` builds the CSS from
`register_controls()` and saves `stylesheet` and `font_family`; `inline_template_css_render()` builds it without
saving (admin previews). Free sites: the inline script marks color and gradient fields "Pro Only".

## Page\Public_Render (abstract)

`__construct($dbdata, $child, $admin)`: normalizes child rows, decodes `rawdata` (`decode_rawdata_array()` tries
four ways), sets `WRAPPER` = `oxi-image-hover-wrapper-<id>`, `hooks()`: enqueues base assets and the module's
assets, renders, then adds the inline JS (500 ms delay on the front end, 2 s in admin and dynamic loads) and CSS
(stylesheet + custom CSS + module inline CSS). Front end: CSS to `$pending_late_css`, printed in the footer by
`Assets::print_late_styles()`. Admin and Elementor contexts print `<link>`, `<script>` and `<style>` tags directly.
`$admin === 'admin'` adds the Edit, Clone and Delete toolbar per item.

## Page\Create

Base of the template library: `pre_active_check()` (first visit stores the free layouts in `oxi_div_import`),
`template()` (a card per layout with a live render of each design; free sites get "Use this design" on `pre_active`
layouts and "Unlock" on the others), `create_new()` (the "Create a new style" dialog), `rec_listFiles()`.

## Page\PreviewFrame

Full HTML document for the editor's iframe: loads the style, removes every other plugin's and theme's assets
(`assets_isolation()`), prints the saved stylesheet (`wp_strip_all_tags`), for Carousel and Display also the nested
style's CSS, renders the effect in admin mode, prints footer scripts, exits.
