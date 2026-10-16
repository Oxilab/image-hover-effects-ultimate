# Admin pages

Every screen of the plugin, checked 2026-10-16. Menu: **Image Hover** (icon `image/dash-icon.svg`), registered in
`Includes/Admin/Menu.php` with the capability of the role chosen in Settings → Access (default `manage_options`).

| Screen | URL (`admin.php?page=...`) | Class | CSS | JS | AJAX |
| --- | --- | --- | --- | --- | --- |
| Image Hover (home) | `oxi-image-hover-ultimate` | `Includes\Admin\Pages\Admin` | bootstrap, admin, settings, home, howto | `home.js` (opens the Pro modal for locked Extensions) | none |
| Template library | `oxi-image-hover-ultimate&effects=<type>` | `Modules\<Type>\<Type>` (extends `Page\Create`) | bootstrap, admin, settings, create, howto | `create.js` | `create_new` |
| Editor | `oxi-image-hover-ultimate&effects=<type>&styleid=<id>` | `Modules\<Type>\Admin\EffectsN` (extends `Page\Admin_Render`) | bootstrap, admin, single_editor_page, editor, howto, iframe-preview, editor libraries | `editors.js`, `preview-controllers.js`, `parent-message-receivers.js`, `overlay-scrollbar.js`, `media-uploader.js`, libraries | see below |
| Preview iframe | `admin-ajax.php?action=oxi_image_hover_preview_frame&styleid=<id>` | `Page\PreviewFrame` | bootstrap, admin, single_editor_page, frontend style, preview-frame | `iframe-button-forwarders.js` | none |
| Shortcode | `oxi-image-hover-shortcode` | `Includes\Admin\Pages\Shortcode` | bootstrap, admin, settings, shortcode, howto | `shortcode.js` (DataTables) | `layouts_clone`, `shortcode_delete`, export link, import form |
| Getting Started | `image-hover-ultimate-getting-started` | `Includes\Admin\Pages\GettingStarted` | settings, admin-menu, getting-started, howto | none | none |
| Settings | `oxi-image-hover-ultimate-settings` | `Includes\Admin\Pages\Settings` | bootstrap, admin, settings, howto | `settings.js` | one `post_<option>` per field, `oxi_image_hover_delete_data_on_uninstall`, `oxi_image_delete_all_data` |
| Account (Freemius) | `oxi-image-hover-ultimate-account` | Freemius template wrapped by `Includes\Admin\Account` | settings, account, admin-menu, howto | Freemius | Freemius |

Getting Started is also the page Freemius opens after activation (`first-path`).

## Image Hover (home)

Hero "Image Hover Effects" with a "Your shortcodes" button and the total count. Two groups of cards:
**Image Effects** (Button, General, Square, Caption, Flipbox, Image Magnifier, Image Comparison, Image Lightbox) and
**Extensions** (Display Post, Carousel Slider, Filter & Sorting). Each card shows the number of layouts (folders in
`Layouts/`), how many styles are saved, a badge (Popular, New, Updated; Pro on free sites for Extensions) and three
links: Templates, Docs, Demo. On free sites the Extensions cards open the Pro modal instead.

## Template library (Create)

Hero "<Type> Effects" (or the Extension name) with "All effect types". One card per layout ("General 6, 3
designs"), each design rendered live from its JSON file. "Use this design" opens the dialog **Create a new style**:
Name (max 50 characters, placeholder "For example: Team gallery"), Cancel, **Create and edit**. On success the
browser goes to the new style's editor. Locked layouts show "Pro layout" and an "Unlock" button.

## Editor

Full screen (WordPress chrome hidden by `editor.css` under `body.oxi-iheu-editor`).

- Header left: Back, Dashboard, Shortcode List, "How to use?". Header right: Upgrade (free sites), Visit Site, the
  name field and **Rename**.
- Left panel: the type name ("General Effect 6"), tabs **Content**, **Style**, **Advanced** (built by the module's
  `register_controls()`), then Rebuild (only for very old rows with a `css` column), **Reload**, **Save**.
- Preview: the shortcode `[iheu_ultimate_oxi id="N"]` with a copy button and a tooltip, device buttons (desktop,
  tablet, mobile), a preview background color, and the iframe. Each item in the preview has Edit, Clone and Delete.
- Item modal: the module's item form (image, title, description, link, button and so on), Close, Submit.
- AJAX from `editors.js` (all `image_hover_settings`): `elements_template_style` (Save), `template_name` (Rename),
  `elements_template_modal_data` (item save), `elements_template_modal_data_edit`, `_clone`, `_delete`,
  `elements_rearrange_modal_data` and `elements_template_rearrange_save_data` (reorder items),
  `elements_template_rebuild_data`, `template_change` (switch design inside the type).

## Shortcode

Hero "Shortcodes" with the count, **Import** and **Create new**. Empty state with "Create your first effect" and
"Import a JSON file". Table: Name (with #id, design and item count), Shortcode (Copy, Copy PHP code), actions Edit,
Clone (dialog "Clone shortcode", name for the copy), Export (downloads `image-hover-effects-ultimateand<id>.json`
through `admin-ajax.php`), Delete (dialog with the shortcode, "Delete shortcode"). Import: a form posted to the page
itself (nonce `image-hover-effects-ultimate-import`, needs `upload_files`, `.json` only), handled in
`Shortcode::manual_import_style()` → `ImageApi::post_json_import()`.

## Settings

Hero "Settings" with "Changes save automatically". Cards:

| Card | Fields (option) |
| --- | --- |
| License | Status pill (Free plan, Trial, Active, Expired), plan, expiry, masked key, Activate or Change license (Freemius dialog), Get Pro or Renew, Manage account |
| Access | Who can edit (`oxi_image_user_permission`, a role) |
| Mobile and touch | Show the effect on first tap (`image_hover_ultimate_mobile_device_key`, off = `normal`) |
| Assets and performance | Font Awesome (`oxi_addons_font_awesome`), Waypoints (`oxi_addons_way_points`), Google Fonts (`oxi_addons_google_font`); off = `no` |
| Advanced | Custom parent class (`oxi_addons_custom_parent_class`), Support message (`oxi_image_support_massage`) |
| Danger zone (admins only) | Delete data when the plugin is deleted (`oxi_image_hover_delete_data_on_uninstall`), Delete all data (type DELETE) |

Side card "Need a hand?": Getting started, Documentation, Live demos, Support forum, version and plan.

## Getting Started

Hero "Welcome to Image Hover Effects" with the YouTube tutorial (`9nqUpIjAFdA`), "Create your first effect" or
"Create a new effect", three steps, "Works with your page builder" (Gutenberg block, Elementor, WPBakery, WordPress
widget, each linking its doc), "Help and resources", and a plan bar (free: Activate license, See Premium plans).

## Plugin menu bar (every page except the editor)

`Admin_helper::oxilab_admin_menu()`: logo, Image Hover, Shortcode, "How to use?" dropdown; right side Upgrade (free
sites), Demo, Docs, Support, Settings icon. Pages call it through the filter `oxi-image-hover-plugin/admin_menu`.
