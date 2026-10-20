# WordPress.org screenshots

The screenshot set on `wordpress.org/plugins/image-hover-effects-ultimate/`. Made on 2026-10-14 and approved by
Richard; installed in `.wordpress-org/` (11 shots) and shipped with 9.12.1. Same format as the Shop Manager set, in
Image Hover colors. The Product Layouts set copied this format later.

## Approval flow

1. Build **one** screenshot, show it (open it in Preview), wait for Richard's word.
2. Then build the rest and show the full set.
3. Only after his go: copy into `.wordpress-org/`, rewrite the readme captions, remove temporary data.
Nothing touches the plugin folder before step 3. Back up the old screenshots to the scratchpad first.

## The look (approved)

| Part | Spec |
| --- | --- |
| Capture | Playwright, 1500 px wide, device scale 2, height per shot (860 to 992) |
| Output | 3256 px wide PNG, the Shop Manager width |
| Stage | `linear-gradient(148deg, #33104a, #220b38, #150722)`, magenta glow top left `rgba(214,40,160,.72)`, pink glow bottom right `rgba(238,50,110,.92)`, coral glow along the bottom `rgba(255,128,64,.62)` (taken from `.wordpress-org/banner-1544x500.png`), no grid or pattern (see Background below), padding 64 |
| Window | macOS window: 42 px bar with traffic lights, 15 px corners, deep shadow |
| Logo pill | Top left: the mark `.wordpress-org/icon-256x256.png` plus a two-line wordmark, "Image Hover" over "Effects Ultimate" in pink `#ff7aa2` |
| Title | Centred, 800 weight, 62 px, white, no dashes, about 30 characters. The frame shrinks a long title to fit between the logo and its mirror, and the build fails if the gap to the logo is under 24 px |
| Highlight | Ring and curved arrow in amber `#ffb020` (stands out from the plugin's pink). Use `arrow: false` where an arrow would cross the preview images |

## Content rules

- **Hover an item** in shots that show a design, so the effect is visible (Richard, 2026-10-14).
- Real data only ([00-general.md](00-general.md) rule 10). Editor shots use the style "Wildlife" (General 6, id 50
  on the test site); its images and texts are real, not the layout's Lorem copy.
- Clean wp-admin: no admin bar, no notices, no menu count badges, only core menus plus Image Hover.
- Privacy: the build fails a shot if the page text has a non example.com email or a user's identity string (whole
  words).
- Left out on purpose: Carousel (#5 showed "Style Data not found") and the Filter and Lightbox previews (weak). Check
  them again before using them.
- Captions in `readme.txt` (`== Screenshots ==`) match the screenshot numbers and say only what the image shows.

## Pipeline

Folder: `/Users/richard/Local Sites/oxilab/image-hover-tutorial-video/scripts/wporg/` (outside the plugin, so it
never ships, and outside the scratchpad, so it isn't lost).

| File | Role |
| --- | --- |
| `frame.mjs` | The stage HTML (colors, logo pill, title fit, ring and arrow) |
| `build.mjs` | `SHOTS` list: `n`, `h`, `url`, `title`, `wait`, `settle`, `act`, `ring`, `arrow`; uses `scripts/rec/lib.mjs` for the logged-in context and admin cleanup, removes the admin bar and badges, privacy scan, frame, save to `out/wporg/screenshot-N.png` |
| `../db/auth-cookies.php` | Login cookies for Playwright (no password) |

Run (PHP with `-d memory_limit=1024M -d mysqli.default_socket=<socket>`, see [00-general.md](00-general.md) rule 15):

```
php scripts/db/auth-cookies.php <scratchpad>/cookies.json
(identity list: every user's display name, email and login as a JSON array in <scratchpad>/ih-banned.json)
IH_COOKIES=<scratchpad>/cookies.json IH_BANNED=<scratchpad>/ih-banned.json node scripts/wporg/build.mjs [n ...]
```

Both helper files live in the scratchpad, never in the project.

## The current set (9.12.1)

| # | Title | Screen |
| --- | --- | --- |
| 1 | Every Effect Type in One Place | Image Hover home with every effect type card |
| 2 | Start From a Ready Made Design | General template library |
| 3 | Name It, Then Start Designing | "Create a new style" dialog, name typed, never submitted |
| 4 | Design It With a Live Preview | Editor, Wildlife style |
| 5 | Add Images, Titles and Links | Item form, opened from the preview toolbar (Lion item) |
| 6 | Style Every Detail, See It Live | Style tab with a hovered item |
| 7 | Typography With Google Fonts | Typography popover, ring, no arrow |
| 8 | Preview on Every Screen Size | Tablet preview, ring on the device switcher, no arrow |
| 9 | Guides Just One Click Away | "How to use?" menu open |
| 10 | Your Shortcodes, Ready to Copy | Shortcode list |
| 11 | Get Started in Three Steps | Getting Started page |

To change one shot later: edit its entry in `build.mjs`, rebuild only that number, show Richard, then replace only
that file and its caption.

## Background: no grid, no squares (Richard, 2026-10-17)

The stage, frame or canvas behind a screenshot is a plain color or a smooth gradient with soft glows only: **no grid,
squares, lines, dots or any repeated pattern**, not even faint. The same rule covers videos, thumbnails and blog
images. On 2026-10-17 the pipelines `image-hover-tutorial-video/scripts/wporg/frame.mjs` and
`wporg-screenshots/product-layouts/frame.mjs` still draw a 58 px grid ("so the flat areas of the gradient are not
dead space"): remove that layer before the next build, and never copy it into a new pipeline. The installed
WordPress.org sets of Shop Manager, Image Hover and Product Layouts still show a grid until they are rebuilt.

- **No version numbers** (Richard, 2026-10-17): hide every plugin and WordPress version on screen and never say one; see [00-general.md](00-general.md) rule 18.
