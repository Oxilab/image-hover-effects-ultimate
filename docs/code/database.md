# Database

Checked against the code on 2026-10-16. Tables are created by `Classes/Installation.php` (`dbDelta`) on activation.

## Tables

### `{prefix}image_hover_ultimate_style`: one row per style (shortcode)

| Column | Type | Content |
| --- | --- | --- |
| `id` | mediumint(5), auto increment | The shortcode id |
| `name` | varchar(50) | The name the user gave it |
| `style_name` | varchar(40) | Type and effect, lower case: `general-6`, `flipbox-3`, `caption-12` (rows from old versions may have `General-6`; the code uses `ucfirst`/`strtolower` where it matters) |
| `rawdata` | longtext | JSON of every editor setting (keys are the control ids, plus `image-hover-style-id`, `image-hover-template`, `image-hover-custom-css`, `image-hover-preview-color`) |
| `stylesheet` | longtext | The generated CSS, rebuilt on every Save (`Admin_Render::template_css_render()`) |
| `font_family` | text | JSON list of Google Fonts the style uses |

### `{prefix}image_hover_ultimate_list`: one row per image item

| Column | Type | Content |
| --- | --- | --- |
| `id` | mediumint(5) | Item id (order of items = id order; Rearrange re-inserts rows to change it) |
| `styleid` | mediumint(6) | The style it belongs to |
| `rawdata` | text | JSON of the item (`image_hover_heading`, `image_hover_description`, `image_hover_image-*`, `image_hover_button_link-*`, `image_hover_button_text`, ...) |

### `{prefix}oxi_div_import`: shared with Flipbox and older Oxilab plugins

| Column | Content |
| --- | --- |
| `type` | `<module>-ultimate` for this plugin (rows written by `Page\Create::pre_active_check()` the first time a template library is opened: the free layouts of that type); `shortcode-addons` rows are read as custom fonts by `import_font_family()` |
| `name` | `<module>-N` |
| `font` | Font name for `shortcode-addons` rows |

Never drop this table: Flipbox uses it too. `Data_Cleaner` removes only the `<module>-ultimate` rows.

## Old rawdata

Rows were saved by many versions in different shapes: plain JSON, JSON with extra backslashes (before `wp_unslash`
was added on save), and JSON whose HTML attribute quotes were broken by `wp_kses_post` running over the whole string.
The readers handle all of them: `ImageApi::decode_rawdata()` (admin), `Public_Render::decode_rawdata_array()` (tries
`stripslashes`, plain, `html_entity_decode`, `repair_attribute_quotes`) and `Public_Render::normalize_child_rawdata()`
(re-encodes items the modules can't decode, skips items nothing can decode). Keep these paths when changing save or
render code.

## Options

| Option | Values | Read by | Removed by |
| --- | --- | --- | --- |
| `oxi_image_user_permission` | A role key; empty = admins (`manage_options`) | Menu, `get_permissions_check()`, `User_Reviews()` | Delete all data, uninstall |
| `image_hover_ultimate_mobile_device_key` | `normal` = tap opens the link at once; anything else loads `touch.js` (first tap shows the effect) | `Public_Render`, Elementor assets | Delete all data, uninstall |
| `oxi_addons_way_points` | `no` = don't load Waypoints | `Public_Render`, Elementor assets | Delete all data, uninstall |
| `oxi_addons_custom_parent_class` | Extra CSS class on every wrapper | `Public_Render::render()` | Delete all data, uninstall |
| `oxi_image_support_massage` | `no` = hide the support message | `SupportAndComments()` (prints nothing today) | Delete all data, uninstall |
| `oxi_addons_font_awesome` | `no` = don't load Font Awesome | renders | Only if no other Oxilab plugin is installed |
| `oxi_addons_google_font` | `no` = don't load Google Fonts | `Public_Render::font_familly*()` | Only if no other Oxilab plugin is installed |
| `oxi_addons_font_awesome_version` | `version||url` | `Public_Helper::icon_font_selector()` | not by this plugin |
| `oxi_image_hover_delete_data_on_uninstall` | `yes` / `no` | `Data_Cleaner::uninstall_site()` | uninstall |
| `oxi_image_hover_version` | Version at install | nothing | uninstall |
| `oxi_image_hover_activation_date` | Timestamp; "Remind me later" resets it | Review and recommended notices | uninstall |
| `oxi_image_hover_nobug` | Set = review notice never again | Review notice | uninstall |
| `oxi_image_hover_upgrade_date`, `oxi_image_hover_upgrade_nobug` | Clock and dismissal of the upgrade notice | Upgrade notice | uninstall |
| `oxi_image_hover_recommended` | Dismissal of the recommended notice | Recommended notice | uninstall |
| `image_hover_ultimate_update_complete` | `done` after the one-off CSS re-render | `index.php` | uninstall |
| `widget_iheu_widget` | Classic widget instances | WordPress | uninstall |
| `image_hover_ultimate_license_status`, `image_hover_ultimate_license_key` | Legacy (before Freemius) license | Pro check, Settings | uninstall |

Freemius keeps its own options (`fs_*`) and handles them itself.

## Transients and cron

| Name | Content |
| --- | --- |
| `oxi_image_user_permission_role` | The capability of the chosen role, 1 hour (not cleared when the role changes) |
| `oxi_image_hover_activation_redirect` | Set on activation for 30 s; nothing reads it (Freemius' first path does the redirect) |
| Cron `image_hover_ultimate_update` | Single event 10 s after load while `image_hover_ultimate_update_complete` is not `done`: re-renders every style's CSS |

## Data flow

```
Create:  layout JSON (style + child) → INSERT style (name, style_name, rawdata) → template_css_render → UPDATE
         stylesheet, font_family → INSERT each child
Save:    rawdata from the form (sanitize_rawdata, </style> removed) → UPDATE rawdata → template_css_render
Items:   INSERT / UPDATE / DELETE in _list; Rearrange re-inserts rows
Render:  SELECT style + children by id → Render class → HTML + queued CSS
Export:  {"style": row, "child": rows} as JSON;  Import: post_json_import() inserts it as a new style
Delete:  DELETE style row and its children (pages that use the shortcode then show a notice to admins and
         nothing to visitors since 9.12.2)
```
