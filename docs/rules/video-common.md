# Videos: rules for every video

Applies to tutorials ([video-tutorial.md](video-tutorial.md)), explainers ([video-explainer.md](video-explainer.md))
and ads ([video-ads.md](video-ads.md)). Built from the Image Hover tutorial (three rounds, v3 approved 2026-10-14), the
Flipbox tutorial (2026-10-09) and the Shop Manager launch video and feature tour (2026-10-03/04), where Richard set
each rule.

## Where the project lives

- One Remotion project per video at `/Users/richard/Local Sites/oxilab/image-hover-<kind>-video/` (never inside the
  plugin, never only in the scratchpad). The tutorial project already exists:
  `/Users/richard/Local Sites/oxilab/image-hover-tutorial-video/`.
- Start from the closest pipeline: that tutorial project for tutorials,
  `/Users/richard/Local Sites/oxilab/shop-manager-launch-video/` for explainers and launch videos. Read the project's
  `README.md` first; it lists every script and command.
- Write a `README.md` in a new project: deliverables, how to rebuild, every change made to the site and its undo.

## Order of work

1. **Brief**: purpose, length, audience, platform, aspect ratios. Ask Richard only what the brief and these rules
   don't answer.
2. **Script**: every line checked against the code and the UI ([00-general.md](00-general.md) rule 3). Show it to
   Richard and get approval before recording.
3. **Site data**: real, realistic data on every screen; record every change with an undo file.
4. **Capture**, **voice**, **timeline**, **checks**, **render**, **master**, **captions**, **thumbnail**, **upload
   pack**, in that order (commands in the project's README).
5. Show Richard review stills or a draft before the final render; after his corrections, update these rules.

## Words on screen and in the voice

- English, international. No dashes anywhere (also no decorative line before a label).
- **No Free/Pro, plans or prices** in voice, captions, on-screen text or thumbnails. Pro features (Display Post,
  Carousel, Filter, the color pickers) appear as plain features.
- Don't read out admin URLs or page parameters (`admin.php?page=...`); name the menu and the button. The address bar
  may be visible.
- Every sentence is true of the plugin today. No numbers or claims that can't be shown: not the readme's "500+
  effects" or "1500+ layouts" (see [00-general.md](00-general.md) rule 3).
- It is the **Image Hover Effects Ultimate** plugin; never show or name the separate Flipbox plugin. The "Flipbox
  Effects" type inside Image Hover is fine.

## Every video opens with a hook (Richard, 2026-10-15)

The first seconds decide whether people keep watching, so no video starts with a logo, a slow "Welcome" or a list
of chapters.

| Video | Hook length | What the hook does; every platform and its format: see Platforms and formats below |
| --- | --- | --- |
| Ad | 0 to 2 s | Movement plus one short line; an image turning into a hover effect in the first second ([video-ads.md](video-ads.md)) |
| Explainer or launch | 0 to 5 s | The viewer's problem in one line over real output (flat images that do nothing), then the reveal ([video-explainer.md](video-explainer.md)) |
| Tutorial | 0 to 8 s | The finished result first (a gallery on a live page, the mouse moving over items so the effects play) and one sentence promising it, then "here's how" ([video-tutorial.md](video-tutorial.md)) |

Rules for the hook:
- It shows real UI and a real result, never a mock-up.
- It promises only what the video delivers.
- The first frame already shows the effect working (it doubles as the thumbnail frame on Shorts and Reels).
- Spoken hook and on-screen text say the same thing; the on-screen line is short enough to read in 2 seconds.

## Brand (Image Hover), from the tutorial's `src/theme.ts`

| Part | Value |
| --- | --- |
| Main color | Pink `#EE326E`, strong `#C41D55`, soft `#FDECF2`, light background `#FFF7FA` |
| Gradient | `#FF6B98` → `#EE326E` for highlighted words and pills |
| Text | Ink `#101828`, muted `#475467` |
| Dark stage (thumbnails, ads) | The WordPress.org stage: `#33104a → #150722` with magenta, pink and coral glows ([screenshots-wporg.md](screenshots-wporg.md)) |
| Pointing (rings, arrows) | Amber `#ffb020` in stills; in videos follow the v3 tutorial's own click highlight |
| Logo | `image/logo.png` (mark), `.wordpress-org/icon-256x256.png`, wordmark "Image Hover" plus "Effects Ultimate" |

One brand accent only (pink), never a second "pain" color for problem scenes (Shop Manager rule, applied to every
Oxilab video).

## Capture (real UI only)

- Playwright, 1920 x 1080 at device scale 2, logged in with a signed cookie
  (`image-hover-tutorial-video/scripts/db/auth-cookies.php`, no password).
- Clean wp-admin at capture time: no admin bar identity, no notices (review notice `.oxi-iheu-review-notice`,
  upgrade notice `.oxi-iheu-upgrade-notice`, recommended plugins `.oxi-addons-admin-notifications`), no third-party
  menus, no update count badges.
- A scanner checks every captured state and **fails** on: any non example.com email, phone numbers, `sk_`/`pk_`
  keys, any user identity string (whole words), license keys, placeholder copy (lorem, ipsum, "Far far away", "Now
  Its Working Free"), and the words free, pro, premium, upgrade, license, pricing, buy, purchase
  (`scripts/privacy-check.mjs`). Template pages may show the plugin's own sample text only where a rule allows it,
  with the reason written down.
- Real hovers and transitions, frame-stepped (`scripts/rec/shot.mjs`: `document.getAnimations()` paused and seeked at
  60 fps). Native select lists don't render headless: draw the real options under the field (Flipbox `openList()`).
- Writes: explainers never write data while recording (block write requests, show the filled form, cut). Tutorials
  may create one item on camera when the story needs it, with a cleanup script that restores the site.

## Voice, sync and clicks

- ElevenLabs, `eleven_multilingual_v2`. The key is read at runtime by the project's `el-key.mjs`; never print it.
  It still points at the deleted `elevenlabdapi.txt`: switch it to the `.env` value first
  ([00-general.md](00-general.md) rule 14).
- Tutorial voice: **Jerry B.** `tCgAUbeV0tdD1S2yFoCx`, speed 1.0 (Richard confirmed it for Image Hover). Explainer
  voice: **Jesse** `sXcl1NoQHcU7NRh6Na5D`, speed 1.0 to 1.1. On 2026-10-16 Claude gave Richard 19 alternative voices
  to listen to (`/Users/richard/Local Sites/oxilab/voice-samples/previews/`); if he picks others, write them here.
- **The screen shows what the voice says, when it says it.** Every action is anchored to its word
  (`at: 'word'`); the timeline builder writes `sync-report.json`. Accept only: worst lag about 0.6 s, no silent tail
  over 2 s where the screen keeps working after the voice stopped.
- Every click lands on the real button (DOM-measured target, `click-check.mjs`), with a click sound.
- Pace: calm. Gaps of about 0.4 to 0.55 s between lines, about 1 s before a new section, holds of 0.4 to 0.9 s
  after a click so the screen can be read. Richard found the first fast cuts hard to follow.

## Backgrounds: no grid, no squares (Richard, 2026-10-17)

Richard, pointing at the ThankRedirect explainer: no square shapes or marks in the background. So, in every video
(every scene, light and dark backdrops, intro, outro, thumbnails, ad frames):

- **No grid, no squares, no lines, no dots, no checkerboard, no tile or repeated pattern** behind the UI or the text,
  not even faint or faded out with a mask.
- Allowed: a plain color, smooth gradients and soft blurred glows in the brand colors, and the real UI itself.
- Check a still of every scene type at full size before rendering: the background must be clean.
- In code a grid looks like `linear-gradient(<color> 1px, transparent 1px)` with a `backgroundSize` in px,
  `repeating-linear-gradient`, `repeating-radial-gradient` or a pattern SVG: remove the layer, don't just fade it.
- On 2026-10-17 these still draw one: `src/components/Backdrop.tsx` (light and dark backdrop) in
  `thankredirect-explainer-video` and `shop-manager-launch-video`. Remove it before their next render, and never copy
  it into a new project.

## Layout and type

- **No text, counter or graphic on top of the UI.** Headlines and pills sit beside or above the screen; push in only
  after the headline has left, or crop to the relevant panel.
- No clipped glyphs: descenders (g, y, p) must never be cut, also in gradient text. Check a frame before rendering.
- One camera matrix drives both the screenshot transform and the cursor, so the cursor stays on target through zooms.

## Output

| Item | Spec |
| --- | --- |
| Video | 1920 x 1080 (and 1080 x 1920 for vertical cuts), 60 fps, H.264 High, CRF 14 to 16 (the tutorial master: 12 Mbps); every platform and its format: see Platforms and formats below |
| Audio | AAC 48 kHz, mastered to -14 LUFS (`master.mjs`) |
| Captions | `.srt` timed to the spoken words |
| Thumbnail | 1280 x 720, under 2 MB, real UI, one short line of text, readable at 320 x 180, no text on top of the UI |
| Upload pack | `youtube.md`: title plus 2 alternatives, description with chapters and links, tags (see YouTube tags below), pinned comment, settings |
| QA | `QA.md`: every check and its result (privacy, clicks, sync, loudness, glyphs) |

Links for descriptions: docs `https://oxilab.dev/docs/image-hover-effects/`, WordPress.org
`https://wordpress.org/plugins/image-hover-effects-ultimate/`, demos `https://demos.oxilab.dev/imagehover/demos/`.
YouTube settings: category Science & Technology, not made for kids, English captions uploaded from the `.srt`, end
screen with Subscribe and a related video. Channel: `https://www.youtube.com/@oxilabdev`.

## YouTube tags (Richard, 2026-10-17)

Richard, on the ThankRedirect explainer: the tags came to 278 characters, too few; they must use the 500 character
limit. So, for every YouTube upload (tutorial, explainer, Short):

- **Fill the tags to 480 to 495 characters** of YouTube's 500, counted the way YouTube counts: each tag's length,
  **plus 2 for a tag with a space** (YouTube wraps it in quotes), plus 1 for each comma. Plain "comma space" text
  comes out shorter, so it is not the measure. Check it before writing the pack:
  `node -e 'const t=["tag one","tag2"];console.log(t.reduce((a,x)=>a+x.length+(x.includes(" ")?2:0),0)+t.length-1)'`
- **Every tag true and relevant to the video**: the main keyword first, then its close variations, then the features
  the video shows, then broad ones (woocommerce plugin, wordpress plugin), then the plugin name. No unrelated or
  trending words (YouTube treats misleading tags as spam), no plan or price words.
- In the upload pack: the tags on **one line, ready to paste**, with the count written next to it
  (example: `thankredirect-explainer-video/out/final/upload-pack.md`, 16 tags, 489).

## Platforms and formats (Richard, 2026-10-17)

Richard posts every explainer and launch video on **YouTube, YouTube Shorts, Facebook Reels, X, LinkedIn and
Reddit**, so it is rendered for all six from one timeline. Paid ads follow [video-ads.md](video-ads.md); tutorials
are YouTube only (16:9) unless Richard asks for more.

| Platform | Who sees it there | File | Size | Length limit | Also deliver |
| --- | --- | --- | --- | --- | --- |
| YouTube | Store owners searching how to do something | `youtube/<name>.mp4` | 1920 x 1080 (16:9) | none that matters | Thumbnail 1280 x 720, `.srt`, chapters, tags (see YouTube tags) |
| YouTube Shorts | Phone viewers scrolling | `youtube-shorts/<name>-shorts.mp4` | 1080 x 1920 (9:16) | under 3:00 | Title ends with #Shorts; the link goes in a pinned comment (description links are not clickable everywhere) |
| Facebook Reels | Store owners on Facebook, on phones | `facebook-reels/<name>-reels.mp4` | 1080 x 1920 (9:16) | **under 1:30** | The link goes in the first comment |
| X | The WordPress and developer crowd | `x/<name>-x.mp4` and `x/<name>-x-square.mp4` | 1920 x 1080, plus 1080 x 1080 for the phone feed | under 2:20, under 512 MB | Post text under 280 characters |
| LinkedIn | Agencies, freelancers, store owners at work | `linkedin/<name>-linkedin.mp4` | 1080 x 1080 (1:1) | under 10:00 | `.srt` (LinkedIn plays muted) |
| Reddit | WordPress and WooCommerce communities | `reddit/<name>-reddit.mp4` | 1080 x 1350 (4:5) | under 15:00, under 1 GB | Title and one paragraph body, the subreddit's title tag (below) |

How to make them:

1. **One timeline, four renders**: the 16:9 master (`Launch`) and three social cuts (`Social-9x16`, `Social-1x1`,
   `Social-4x5`), H.264 yuv420p with AAC, each mastered to -14 LUFS. Copy them into the six folders above
   (`out/final/<platform>/`, APFS clones so they take no extra space).
2. **Length**: keep an explainer **under 1:30**, so the same cut fits Reels and every other platform. A longer video
   (a feature tour) goes to YouTube, LinkedIn and X (if under 2:20) only, unless Richard asks for a short cut.
3. **Social cut layout**: the 16:9 video as a big rounded card, the logo and the WordPress.org link above it, big
   word synced captions burned in below it (feeds play muted), on the clean brand background. In 9:16 keep every text
   out of the top 14% and the bottom 35% (Reels and Shorts put their buttons there); 1:1 and 4:5 use the same layout,
   tighter.
4. **Check before delivery**: a still of each format, the length on the final files, file sizes under each limit,
   -14 LUFS on each.
5. **Upload pack** (`out/final/upload-pack.md`): per platform the title, the post text and where the link goes; every
   text on one line so a copy carries no hard line breaks; every claim checked in the code.
6. **Reddit title tags**: r/WordpressPlugins removes posts without one of [FREE], [PREMIUM], [FREEMIUM], [PROMOTION],
   [REVIEW], [DISCUSSION], [REQUEST] or [HELP] at the start of the title. All five Oxilab plugins are free on
   WordPress.org with a Pro license, so use **[FREEMIUM]** (and the Freemium flair). Read every subreddit's rules
   before posting; r/woocommerce and r/Wordpress limit self promotion.

Worked example and template: `/Users/richard/Local Sites/oxilab/thankredirect-explainer-video/` (`src/Vertical.tsx`
LAYOUTS, `scripts/render-all.sh`, `scripts/platforms.sh`, `out/final/upload-pack.md`).

- **No version numbers** (Richard, 2026-10-17): hide every plugin and WordPress version on screen and never say one; see [00-general.md](00-general.md) rule 18.
