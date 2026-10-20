# Screenshots for docs (oxilab.dev)

Screenshots inside the Image Hover docs at `oxilab.dev/docs/image-hover-effects/`. The rules come from the Shop
Manager docs (Richard's corrections on docs 503 and 514), the Image Hover WordPress.org set and the Product Layouts
rules.

## The rules

1. **The mark matches the text exactly.** A highlight box covers every element the paragraph names, built as the
   union of their boxes, not just the first one. If the text covers Title, Description and Image, all three are
   inside the box.
2. **No mark beats a wrong mark.** A screenshot that shows a whole screen for an overview has no mark at all.
3. **Arrows only for one small control** the reader has to hunt for (a single button or icon).
4. **The screenshot proves the UI, the code proves the behaviour.** Check both before writing the step
   ([00-general.md](00-general.md) rule 3). If the screen doesn't show what the sentence says, one of them is wrong:
   fix it before publishing.
5. **Current UI only.** Capture the plugin as it is now. The live docs were written before the 9.12.0 and 9.12.1
   redesign (old Create page, old editor header), so a rewritten doc gets new screenshots.
6. **Show the effect.** When the step is about how a design looks, hover one item so the hover effect is visible
   (the hover rule Richard set for screenshots on 2026-10-14). Magnifier: show the zoom on the demo site page, the
   preview card clips it.
7. **Privacy and real data** exactly as in [00-general.md](00-general.md) rules 9 and 10. No license key, not even
   masked. No placeholder copy (Lorem Ipsum captions) in view.
8. **No dashes** in alt text, captions or marks.

## Format: the Shop Manager doc look, in Image Hover colors

Richard, 2026-10-18: every doc image has the same shape as the Shop Manager doc images, in this plugin's colors. (The
first version of this file said "element crop, no gradient stage": that was wrong, and the first images of docs 289
to 295 were made that way and had to be redone.)

| Part | Spec |
| --- | --- |
| Capture | Playwright, viewport **1404 x 862** at device scale 2 (the Shop Manager size), logged in with `auth-cookies.php`, wp-admin cleaned like the WordPress.org pipeline (no admin bar, no notices, no other plugins' menus, no count badges) |
| What is in view | The whole screen, never a tight crop; the step is shown by the amber mark, and the page is scrolled so the marked part is in view. The wp-admin sidebar is hidden, except for "find the menu" steps. A dialog is shown over its page |
| Frame | A macOS window: 46 px light title bar with the three dots (red, yellow, green) and no title text, 14 px corners, soft shadow, on a 90 px backdrop. Final size 3168 x 2176, like the Shop Manager doc images |
| Backdrop | Image Hover colors: deep plum base (`#2e0f44` to `#140620`) with soft glows in magenta (top left), brand pink `#EE326E` (bottom right) and coral (bottom). Gradients and glows only, no grid, lines or dots |
| Code | `image-hover-tutorial-video/scripts/docs/frame.mjs` (frame) and `docs.mjs` (capture, mark, scan, frame, WebP) |
| Mark | Amber ring `#ffb020`, 4 px, 10 px outside the elements, 12 px corners, light glow; arrow in the same amber (amber stands out from the pink UI; same choice as the WordPress.org set). When the marked elements are far apart (a button and a form), draw one ring per element instead of one ring that crosses unrelated UI |
| File | WebP, sharp at 2x, lower case, named `ihe-<topic>-<step>.webp` for new images (example `ihe-lightbox-3.webp`); when a doc's existing image is replaced, keep its old file name |
| Alt text | What the image shows, in plain words; the doc's focus keyword in at least one alt |
| Caption | Optional; when used, it says what to look at, in one short sentence |

## Where the images go: Cloudflare R2

Doc images live on **Cloudflare R2** and load from the CDN, never from the WordPress Media Library. The live Image
Hover docs already do this.

| Part | Rule |
| --- | --- |
| Public URL | `https://cdn.oxilab.dev/imagehover/docs/<file>.webp` (the folder the live docs use, for example `https://cdn.oxilab.dev/imagehover/docs/general-effects.webp`) |
| Local mirror | `/Users/richard/Local Sites/oxilab/r2-uploads/imagehover/docs/<doc id>/<file>.webp`: one folder per doc id, so Richard sees which images belong to which doc (Richard, 2026-10-18). An image used by two docs sits in the folder of the first doc. The id folders are **local only**: on R2 every file goes straight into `imagehover/docs/`, so the doc's image URL has no id in it |
| Who uploads | **Richard uploads to R2 himself.** Save the file in the mirror folder, write the CDN URL into the doc, and tell him which files to upload |
| Never | Upload doc images with `upload_media` or any other way into WP Media |
| Only what is needed | The mirror holds only images Richard has to upload: images of docs that are published (or being published now). Images of a doc not worked on yet, old versions and drafts are deleted, and no second copy is kept anywhere else (Richard, 2026-10-18: "j gulo lagbe na sei gulo remove kore dao") |
| Replace an image | Save the new file under the **same** name and tell Richard; the doc already points at that CDN name, so no doc edit is needed |
| New image | Add the image block with its planned CDN URL, then give Richard the file |

## Pipeline

`/Users/richard/Local Sites/oxilab/image-hover-tutorial-video/scripts/docs/` (built 2026-10-18):

| File | Does |
| --- | --- |
| `docs.mjs <doc-slug> [file ...]` | Runs a doc's shot list: opens the page, acts, scans the visible text (emails, admin identity, keys, version numbers, placeholder copy, dashes), draws the amber marks, frames, writes WebP to `out/docs/<doc-slug>/` and copies it to `r2-uploads/imagehover/docs/` |
| `shots/<doc-slug>.mjs` | The shot list of one doc |
| `h.mjs` | Helpers: element boxes, set columns in the preview, hover a preview item without the editor toolbar, open the item form |
| `frame.mjs` | The window frame and backdrop |
| `identity.php`, `../db/auth-cookies.php` | The identity list and login cookies (write both outside the project) |
| `temp-style.php`, `temp-page.php` | Temporary styles with real Wildlife content and a draft page with a shortcode, recorded in `out/docs/temp-*.json` and deleted afterwards (rule 13) |
| `check.mjs <doc.html> "<keyword>"` | Words, keyword density band, keyword in H2s and alts, link and image status |

The layout designs hover with placeholder copy ("Image Hover Title", "Add Your Description..."), so a hovered example
always uses a style with real content (the Wildlife style, id 50, or a temporary style), never a design in the
template library.

## Checklist per doc

1. List the steps of the doc and decide which ones need a picture (one per step where the screen changes).
2. Capture, mark, export; look at every image next to its paragraph.
3. Show Richard the set with the doc draft.
4. Save the files in `r2-uploads/imagehover/docs/`, put their CDN URLs and alt text in the doc, and give Richard the
   list of files to upload to R2.

## Builder search shots: show the gallery too (Richard, 2026-10-20)

A doc picture of a builder search (block inserter, Widgets, Elementor, WPBakery Add Element) shows the search with the
plugin's item ringed **and a Wildlife gallery already on the page or sidebar behind it**, like the WordPress.org
"Works With" shots. For the block editor and Elementor, open a TEMP page that already holds the gallery
(`temp-page.php`); for Widgets, add the block with Wildlife (#50) in the editor first, never click Update, then search.
Since 9.12.2 the classic widget "Image Hover Effects Ultimate" is listed next to the block in Widgets (Elementor hides
it, so it shows only "Image Hover" there), so locate the block by its exact name (`/^\s*Image Hover Effects\s*$/`), never by a loose "Image Hover Effects" match.
Hide other plugins' editor buttons by selector (WPBakery: `.composer-switch, #wpb-gutenberg-btn`), never by matching
text, which can hide the whole toolbar.

## Background: no grid, no squares (Richard, 2026-10-17)

The stage, frame or canvas behind a screenshot is a plain color or a smooth gradient with soft glows only: **no grid,
squares, lines, dots or any repeated pattern**, not even faint. The same rule covers videos, thumbnails and blog
images. On 2026-10-17 the pipelines `image-hover-tutorial-video/scripts/wporg/frame.mjs` and
`wporg-screenshots/product-layouts/frame.mjs` still draw a 58 px grid ("so the flat areas of the gradient are not
dead space"): remove that layer before the next build, and never copy it into a new pipeline. The installed
WordPress.org sets of Shop Manager, Image Hover and Product Layouts still show a grid until they are rebuilt.

- **No version numbers** (Richard, 2026-10-17): hide every plugin and WordPress version on screen and never say one; see [00-general.md](00-general.md) rule 18.
