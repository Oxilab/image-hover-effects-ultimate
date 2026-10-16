# Effect types (modules), effects, designs, Free and Pro

Counted in the code on 2026-10-16. Use these numbers, never the readme's "500+ animations" and "1500+ layouts"
([../rules/00-general.md](../rules/00-general.md) rule 3).

## The numbers

| Type (`effects=`) | Name on the card | Group | Effects (`Admin/EffectsN`) | Designs (JSON files) | Free layouts (`pre_active`) |
| --- | --- | --- | --- | --- | --- |
| `button` | Button Effects | Image Effects | 11 | 33 | 1 to 5 |
| `general` | General Effects | Image Effects | 33 | 99 | 1 to 10 |
| `square` | Square Effects | Image Effects | 22 | 66 | 1 to 10 |
| `caption` | Caption Effects | Image Effects | 31 | 93 | 1 to 10 |
| `flipbox` | Flipbox Effects | Image Effects | 29 | 87 | 1 to 10 |
| `magnifier` | Image Magnifier | Image Effects | 2 | 6 | 1 |
| `comparison` | Image Comparison | Image Effects | 4 | 10 | 1, 2 |
| `lightbox` | Image Lightbox | Image Effects | 2 | 6 | 1 |
| `display` | Display Post | Extension | 1 | 1 | 1 |
| `carousel` | Carousel Slider | Extension | 3 | 3 | 1 |
| `filter` | Filter & Sorting | Extension | 2 | 2 | 1 |
| **Total** | 11 types | | **140** | **406** | **52 layouts** |

"Layout" = one effect number (folder `Layouts/N/`); "design" = one JSON file in it (usually 3 per layout).

## Free and Pro (what the code does)

- **Pro check**: the filter `oxi-image-hover-plugin-version` (legacy license `valid` or Freemius
  `can_use_premium_code()`).
- **Template library**: on free sites only the `pre_active` layouts have "Use this design"; the others show "Pro
  layout" and "Unlock" (`Page\Create::template()`).
- **Extensions**: on free sites the Display Post, Carousel and Filter cards on the Image Hover page are badged Pro and
  open the Pro modal (`Pages\Admin`, `home.js`).
- **Editor**: on free sites the color and gradient fields are labelled "Pro Only" (inline script in
  `Admin_Render::import_font_family()`).
- **Dynamic content** (posts as items, load more, dynamic carousel; the `Dynamic` trait and `Modules\Compailer`)
  renders only with Pro (`shortcode_render()` checks the filter).
- The local test site runs Pro, so everything looks unlocked there.

## How a type is built

```
Modules/General/
├── General.php          template library page (extends Page\Create): JSON_DATA() lists Layouts/, sets pre_active
├── Modules.php          editor controls for every General effect (extends Page\Admin_Render, uses trait Dynamic)
├── Admin/Effects1.php   class Effects1 extends Modules (often only register_effects() differs)
├── Render/Effects1.php  class Effects1 extends Page\Public_Render: public_css() and default_render()
├── Files/general.css, style-1.css ...
└── Layouts/1/image-hover-effects-ultimateand1.json ...
```

`Modules.php` of a type: `$StyleChanger` (the designs offered by "Template Changer"), `register_controls()` opens the
tabs Content (`general-settings`), Style and Advanced, then calls the type's `register_*` sections (content, heading,
description, button, general style, column, effects time, custom CSS) and the item form (`modal_form_data()`,
`modal_opener()`, `Rearrange()`).

A design JSON file: `{"style": {"name", "style_name": "general-1", "rawdata": "<JSON string>", ...}, "child":
[{"rawdata": "<JSON string>"}, ...]}`. `post_create_new()` inserts the style with the chosen name, renders its CSS,
inserts the children and returns the editor URL.

## Rendering a style

`shortcode_render($id)` → row → `style_name` `general-6` → `new Modules\General\Render\Effects6($row, $children,
'user')`. `Public_Render` enqueues `assets/frontend/css/animation.css`, `style.css`, `oxi-jquery.js` (plus
`waypoints.min.js` and `touch.js` unless switched off), then the effect's `public_css()` (`general.css`,
`style-6.css`) and `public_jquery()`, prints the HTML inside
`<div class="oxi-addons-container noLightbox oxi-image-hover-wrapper-<id> <custom parent class>">`, and queues the
saved CSS plus custom CSS for the footer.

Items: each child row's `rawdata` holds keys such as `image_hover_heading`, `image_hover_description`,
`image_hover_image-select` / `-image` / `-url` / `-image-alt`, `image_hover_button_link-url` / `-target`,
`image_hover_button_text`. Text fields go through `text_render()` (`wp_kses_post`, `do_shortcode`; `spBac` = line
break, `spTac` = non-breaking space).

## Libraries per type

| Type | Files and libraries |
| --- | --- |
| Caption | 31 effect families in `Files/` (blinds, block, book, border-reveal, bounce, circle, cube, dive, fade, fall-away, flash, flip, fold, hinge, lightspeed, modal, parallax, pivot, pixel, push, reveal, rotate, shift, shutter, slide, splash, stack, strip, switch, throw, zoom) |
| Comparison | TwentyTwenty (`jquery-twentytwenty.js`, `jquery-event-move.js`), BeerSlider, mbac |
| Lightbox | GLightbox, `Popup.js` / `Popup.css`, `lg` fonts |
| Magnifier | `image_zoom.min.js`, zoomple |
| Carousel | Swiper, Slick, Flipster (the Elementor editor uses `elementor-carousel-init.js`) |
| Filter | Isotope, imagesLoaded |
| Display | `Files/*Query.php`, `style-1-loader.js` (load more) |
| General, Square, Flipbox, Button | Their own `style-N.css` plus the type CSS |

## Demos and docs per type (links on the cards)

Demo pages: `https://demos.oxilab.dev/imagehover/elements-extension/<button-effects|general-effects|square-effects|caption-effects|flipbox-effects|image-magnifier|image-comparison|image-lightbox|display-post|carousel-slider|filter-sorting>/`.
Doc links: the `doc` key of each card in `Includes/Admin/Pages/Admin.php`; Square and Magnifier share one doc.
