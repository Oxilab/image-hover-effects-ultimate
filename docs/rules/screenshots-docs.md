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

## Format

These are defaults; change them here if Richard asks.

| Part | Spec |
| --- | --- |
| Capture | Playwright at device scale 2, logged in with `auth-cookies.php`, wp-admin cleaned like the WordPress.org pipeline (no admin bar, no notices, no other plugins' menus, no count badges) |
| Crop | Only the part the step is about (an element screenshot of the card, dialog or panel plus 24 px of context). Show the wp-admin sidebar only for "find the menu" steps |
| Mark | Amber ring `#ffb020`, 4 px, 10 px outside the elements, 12 px corners, light glow; arrow in the same amber (amber stands out from the pink UI; same choice as the WordPress.org set) |
| File | WebP, sharp at 2x, lower case, named `ihe-<topic>-<step>.webp` for new images (example `ihe-lightbox-3.webp`); when a doc's existing image is replaced, keep its old file name |
| Alt text | What the image shows, in plain words; the doc's focus keyword in at least one alt |
| Caption | Optional; when used, it says what to look at, in one short sentence |

## Where the images go: Cloudflare R2

Doc images live on **Cloudflare R2** and load from the CDN, never from the WordPress Media Library. The live Image
Hover docs already do this.

| Part | Rule |
| --- | --- |
| Public URL | `https://cdn.oxilab.dev/imagehover/docs/<file>.webp` (the folder the live docs use, for example `https://cdn.oxilab.dev/imagehover/docs/general-effects.webp`) |
| Local mirror | `/Users/richard/Local Sites/oxilab/r2-uploads/imagehover/docs/` (same file name as the R2 key) |
| Who uploads | **Richard uploads to R2 himself.** Save the file in the mirror folder, write the CDN URL into the doc, and tell him which files to upload |
| Never | Upload doc images with `upload_media` or any other way into WP Media |
| Replace an image | Save the new file under the **same** name and tell Richard; the doc already points at that CDN name, so no doc edit is needed |
| New image | Add the image block with its planned CDN URL, then give Richard the file |

## Pipeline

Reuse the WordPress.org pipeline in `/Users/richard/Local Sites/oxilab/image-hover-tutorial-video/scripts/wporg/`
and the capture helpers in `scripts/rec/lib.mjs` (logged-in context, admin cleanup, privacy scan). For docs, add a
separate script (for example `scripts/wporg/docs.mjs`) that captures element crops, draws the union mark, and writes
WebP to `out/docs/<doc-slug>/`, without the gradient stage. Keep it in that project, never only in the scratchpad.

## Checklist per doc

1. List the steps of the doc and decide which ones need a picture (one per step where the screen changes).
2. Capture, mark, export; look at every image next to its paragraph.
3. Show Richard the set with the doc draft.
4. Save the files in `r2-uploads/imagehover/docs/`, put their CDN URLs and alt text in the doc, and give Richard the
   list of files to upload to R2.
