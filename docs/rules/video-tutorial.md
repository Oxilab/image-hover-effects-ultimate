# Tutorial videos (YouTube, "how to use")

The method of the Image Hover Effects tutorial v3 (4:59, 2026-10-14), which Richard approved after three rounds, and
of the Flipbox guide (4:55, 2026-10-09) whose notes became the rules. Read [video-common.md](video-common.md) first.
Pipeline: `/Users/richard/Local Sites/oxilab/image-hover-tutorial-video/` (cut `v3`, `IH_CUT=v3`, composition
`GuideV3`, script `content/v3/script.mjs`, shots `public/shots/v-*`, final files in `out/final/`).

## History (what Richard rejected, so it isn't repeated)

1. The long cut (12:35): too long, too slow, over-explained.
2. The quick start (2:47): fine pace, but the voice and the screen did not match, so users couldn't follow.
3. The guide (5:07): the "Effect types" chapter showed only cards, no demos; Richard wanted each type's demo.
4. v3 (4:59, current): rebuilt on the 9.12.1 redesign with the Flipbox guide's rules below. Approved.

## Richard's rules for tutorials

1. **Follow a brand new user's order.** A new user has no styles yet, so never open on the Shortcode page. Start at
   the Dashboard, the Image Hover menu, the Image Hover page; create one; show where styles are kept at the end.
2. **Show what you say, at the word.** Whatever the voice names is clicked or hovered on screen at that moment: tabs,
   settings lists, the item form. Clicking is enough, no need to change or save. For each sentence ask: is the thing
   it names on screen at that word? If not, add the action or drop the sentence.
3. **Hover is important.** Scroll through designs and hover several items so viewers see the effects; show a live
   demo for every effect type, kept quick (the Effect types chapter is about 60 s).
4. **Do it the way a user would.** Pick a color by dragging in the picker, never by typing a code. Copy the shortcode
   where the user is (the copy button above the editor's preview), not by detouring to another page.
5. **Mention every way to use the result, then show the main ones**: the Shortcode block, the Image Hover Effects
   block, then name Elementor, WPBakery, Divi and widgets.
6. **Not too long, not too slow, not over-explained.** A getting started guide is 4 to 5 minutes in about 10
   chapters; a quick start is 2 to 3 minutes. Skip what a beginner doesn't need (top menu bar, Settings, every card
   button).
7. End on a short "Thanks for watching", with no tail after it.

## The v3 story (use it as the template)

| Time | Chapter | On screen |
| --- | --- | --- |
| 0:00 | Hook and introduction | The result first, then three bullets: find the plugin and its effect types; create a gallery and design it; add it to a page ([video-common.md](video-common.md), Hook) |
| 0:15 | Find the plugin | Dashboard, the Image Hover menu (only the Image Hover and Shortcode items named), the Image Hover page |
| 0:26 | Effect types | Each type with a short live demo (Templates pages for the 8 Image Effects, demo site pages for the 3 Extensions, whose Templates pages have no preview) |
| 1:20 | Pick a design | General Effects templates, hover several designs |
| 1:39 | Create | "Use this design", type a name in the "Create a new style" dialog, "Create and edit", the editor opens |
| 1:54 | Add images | Content tab, add an image with title, description and an image from the Media Library; four in a row; open an item's Edit from the preview toolbar |
| 2:50 | Style and Advanced | Style tab: change the overlay color by dragging; Advanced tab briefly |
| 3:22 | Add it to a page | Copy the shortcode above the preview; a page with the Shortcode block; the Image Hover Effects block (pick the style, live preview); name Elementor, WPBakery, Divi and widgets; the live page with hovers |
| 4:30 | Your galleries | Shortcode page: every style and its shortcode |
| 4:47 | Wrap up | Short thanks |

Verified facts to use (true on 2026-10-16, check again before a new script):
- Shortcode `[iheu_ultimate_oxi id="N"]`; the editor shows it above the preview with a copy button.
- Block: "Image Hover Effects" (`iheu/image-hover`, Media category) with a style picker and a server-side preview.
- Elementor widget: "Image Hover" (General category) with a "Shortcode" select and an "Edit Shortcode" button.
- WPBakery element: "Image Hover Ultimate" (Content category). Widget: "Image Hover Effects Ultimate" with a
  "Style ID" field.
- The naming dialog is "Create a new style" with a Name field and "Create and edit".
- Use a **free** design for the walkthrough: General 1 to 10 are free (each type's `pre_active` list in
  [../code/modules.md](../code/modules.md)); the local site runs Pro, so check the code, never the screen. Don't say
  which designs are free (no Free/Pro in videos).

## Pacing and sync (the values that worked)

`gap 0.4`, `clickHold 0.9`, `hoverHold 0.35`, moves 0.65 to 0.95 s, camera 0.85 s, chapter card 1.9 s, typing
0.05 s per character, scroll 800 px/s. Voice Jerry B. at 1.0 speaks fast (160 to 200 words per minute), so the calm
pace comes from gaps and holds, not from a slower voice. v3 shipped with a worst lag of 0.58 s. Accept a render only
when the sync report shows about 0.6 s or less and no silent tails over 2 s; build labelled still sheets
(`sync-sheet.mjs`) for review.

## Image Hover capture gotchas

- Create New lists every design; free ones are each type's `pre_active` list.
- The Magnifier preview card clips its zoom window: film the zoom on the demo site page.
- The Create New Lightbox preview has `noLightbox`, and the demo site popup caption is Lorem: show only the zoom icon
  hover for Lightbox.
- The Media Library holds personal photos and Flipbox photos: wait for the tutorial item, never the first `li`.
- Headless Chrome needs clipboard permission, or Copy shows "Press Ctrl+C".
- The color field is a coloring-pick popup closed by its check button (`.col_select_btn`); the cross clears colors.
- Square 2 Effects Direction has only Top and Bottom.
- WordPress autosave hides "Save draft" (set `autosaveInterval` high); the block editor Page tab shows the author name
  and Astra's "Upgrade Now" (hide them); the inserter's block directory shows "Pro" text (hide that panel).
- The Astra header shows the site title with "Premium": frame below y 81 on front-end pages.
- Lower Flipbox layouts carry Lorem, WooCommerce and "Buy Now" copy: avoid or crop.
- Returning to the Image Hover page after a demo needs `cam: 'full'`, or the old zoom carries over.
- The front end must be filmed on Astra (narrow themes clip the hover text).

## Existing videos

| Video | Where |
| --- | --- |
| Getting started tutorial (v3, 4:59) | `out/final/image-hover-effects-tutorial.mp4`, `.srt`, thumbnail, `youtube.md`, `QA.md`. The plugin's Getting Started page and the readme embed `youtu.be/9nqUpIjAFdA` |
| Older cuts | `out/guide/` (5:07), `out/short/` (2:47), `out/long/` (12:35); keep, don't publish |

Suggested next, not made yet: an SEO demo video ("11 Image Hover Effects for WordPress (Live Demo)") plus a Short.

## Deliverables (`out/final/`)

`image-hover-effects-tutorial.mp4`, `.srt`, `image-hover-effects-thumbnail.png`, `youtube.md` (title, 2
alternatives, description with chapters, links, tags), `QA.md`.
