# Assets

Plain CSS and JS, no build step. Checked 2026-10-16. Versions: plugin files mostly use `filemtime()` (admin) or
`OXI_IMAGE_HOVER_PLUGIN_VERSION`.

## Admin CSS (`assets/backend/css/`)

| File | Loaded on | Notes |
| --- | --- | --- |
| `global-admin.css` | Every admin page | The Image Hover menu: icon size, the highlighted Getting Started item |
| `bootstrap.min.css` | Image Hover, Shortcode, Settings (and the preview iframe) | Old Bootstrap for the editor and modals |
| `admin.css` | Same pages | Old global styles of the plugin pages; has broad rules (`.notice`, `.oxi-addons-wrapper a`, `img`) that new code must out-specify, not edit |
| `settings.css` | Settings, Shortcode, home, Create, Account, Getting Started | The design tokens (`--iheu-*`) and the `oxi-iheu-set-*` building blocks |
| `home.css` | Image Hover home | Effect type cards |
| `create.css` | Template library | Layout cards, design previews |
| `shortcode.css` | Shortcode page | Table, copy buttons, dialogs |
| `account.css`, `admin-menu.css` | Account (and Getting Started for `admin-menu.css`) | Freemius page restyle, full-width header |
| `getting-started.css` | Getting Started | |
| `howto.css` | Every page with the plugin header | "How to use?" dropdown |
| `single_editor_page.css`, `editor.css` | Editor (`editor.css` scoped to `body.oxi-iheu-editor`) | Editor layout and its 9.12.1 visual layer |
| `iframe-preview.css`, `preview-frame.css` | Editor / preview iframe | Device frame, item toolbar |
| `notice.css` | Where a notice shows | Review, upgrade, recommended notices (scoped against `admin.css`) |
| Libraries | Editor | `jquery.coloring-pick.min.js.css`, `minicolors.css`, `nouislider.css`, `fontawesome-iconpicker.css`, `select2.min.css`, `jquery.fontselect.css`, `overlayscrollbars.min.css` |

## Admin JS (`assets/backend/js/`)

| File | Role |
| --- | --- |
| `editors.js` | Every editor action (Save, Rename, items, rearrange, rebuild, template change), live control handling |
| `preview-controllers.js` | Live CSS into the iframe, device switch, preview background |
| `parent-message-receivers.js`, `iframe-button-forwarders.js` | Edit, Clone, Delete from the iframe to the editor |
| `overlay-scrollbar.js` (+ `overlayscrollbars.browser.es6.min.js`) | Scrollbars in the editor panels |
| `create.js` | "Use this design" dialog → `create_new` |
| `home.js` | Pro modal for locked Extensions |
| `shortcode.js` | DataTables, copy, clone and delete dialogs |
| `settings.js` | Auto-save of each setting, Danger zone dialog |
| `gutenberg-block.js` | The block's editor UI |
| `media-uploader.js` | Media Library picker for image fields |
| `pro-modal.js` | The Pro modal |
| `admin-notice.js`, `admin-recommended.js` | Notice dismissal and placement |
| `vendor.js` | Editor control behaviour: tabs, sections, popovers and the other control widgets |
| Libraries | `bootstrap.min.js`, `jquery.dataTables.min.js`, `dataTables.bootstrap.min.js`, `jquery.coloring-pick.min.js`, `jquery.minicolors.js`, `nouislider.js`, `fontawesome-iconpicker.js`, `jquery.conditionize2.min.js`, `select2.min.js`, `jquery.serializejson.min.js` |

The Google Fonts picker is built from an inline script in `Admin_Render::import_font_family()` (the full font list is
in that method).

## Front end

| File | When |
| --- | --- |
| `assets/frontend/css/style.css`, `animation.css` | Every page with a style |
| `assets/frontend/js/oxi-jquery.js` | Every page with a style (entrance animations, needs jQuery) |
| `assets/frontend/js/waypoints.min.js` | Unless Waypoints is switched off |
| `assets/frontend/js/touch.js` | Unless "Show the effect on first tap" is off |
| `assets/frontend/css/font-awsome.min.css` | When a style shows icons and Font Awesome is on |
| `Modules/<Type>/Files/<type>.css`, `style-N.css` | The effect's own CSS (`Render/EffectsN::public_css()`) |
| Module libraries | See [modules.md](modules.md) (Swiper, Slick, Isotope, TwentyTwenty, GLightbox, image_zoom, ...) |
| `assets/frontend/js/elementor-carousel-init.js`, `elementor-edit-btn.js` | Elementor editor and preview only |
| Google Fonts | `https://fonts.googleapis.com/css?family=<font>` per font, unless Google Fonts is off |

The style's own CSS is not a file: it is queued in `Public_Render::$pending_late_css` and printed in the footer as
`<style id="oxi-iheu-late-css">`. In admin, Elementor editing and block previews it is printed inline next to the
markup instead. All front-end handles are registered on every page (`Assets::register_swiper_scripts()`) so
Elementor can resolve the widget's dependencies; they are only enqueued where a style renders.
