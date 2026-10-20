# Explainer and launch videos ("what it is, why you want it")

No Image Hover explainer exists yet. The method is the Shop Manager launch video (1:16, 16:9 and 9:16, delivered
2026-10-04) and its feature tour (3:10), both approved by Richard. Read [video-common.md](video-common.md) first.
Pipeline to copy: `/Users/richard/Local Sites/oxilab/shop-manager-launch-video/` (its `README.md` has every command).

## Two formats

| Format | Length | Shape |
| --- | --- | --- |
| Launch / explainer | 60 to 90 s (under 1:30 so the Reels cut fits), every platform format ([video-common.md](video-common.md), Platforms and formats) | Problem, reveal, 3 to 5 feature beats, outcome, call to action |
| Feature tour | 2:30 to 3:10, 16:9 | Every effect type with its use, calmer pace, chapters |

**Hard limit (Richard, 2026-10-16): every explainer video is under 5 minutes**, the final render included (end card
and outro). The lengths above are the targets; if the script grows, cut beats or tighten gaps until the timeline is
4:59 or less. Check the length after building the timeline (before the render) and again on the final file; never
speed the voice above 1.1 to make it fit.

The tour is the long, searchable YouTube video; the launch video and the Short point to it.

## Story for Image Hover (draft to adapt)

1. **Hook and problem** (hook in the first 5 s, problem about 10 s): images on a site just sit there; making them
   react on hover means writing CSS. Open on a flat gallery that does nothing when the mouse moves over it, not on a
   logo ([video-common.md](video-common.md), Hook).
2. **Reveal**: Image Hover Effects, the real Image Hover page with its effect type cards.
3. **Beats** (each one real output or UI, a few seconds): caption and overlay effects on a gallery; flip boxes and
   button effects; before and after comparison being dragged; a lightbox opening; the magnifier zooming; the live
   editor with colors and fonts; desktop, tablet and mobile preview; a block, a shortcode or an Elementor widget to
   place it anywhere; carousel, filterable gallery and post grids.
4. **Outcome**: a page whose images come alive on hover.
5. **Call to action**: "Get Image Hover Effects" plus the WordPress.org page, on the closing screen.

Every beat is checked in the code before it goes into the script. Pro features (Display Post, Carousel, Filter, the
color pickers) are shown as plain features, never labelled ([video-common.md](video-common.md)).

## How it is built (Shop Manager pipeline)

- Script lines in `content/script.json` (one voiceover line per beat); Richard approves the script first.
- `tts.mjs` (ElevenLabs per line, with timestamps) → `vo-measure.mjs` (silence detection) → `build-timeline.mjs`
  (with `content/pacing.json`) → `content/timeline.json` drives every cut, caption and music cue.
- Music: an ElevenLabs Music take shaped in post by `shape-music.mjs` (low-pass muffle before the reveal, a jump back
  at the outcome, open at the call to action). ElevenLabs Music ignores composition sections, so shape in post.
- Sound effects from ElevenLabs sound generation; a click sound on every click.
- Captures with every write request **blocked**: show filled forms and cut, or animate the result as a clear motion
  graphic. Never save data while recording.
- Hover effects are filmed for real with the frame-stepped capture from the tutorial project
  (`image-hover-tutorial-video/scripts/rec/shot.mjs`); never fake an effect in Remotion.
- On-screen headlines: words between `*stars*` get the brand highlight (the pink gradient, see
  [video-common.md](video-common.md) Brand).
- Text never on top of the UI: headlines beside or above the card, push in only after the headline leaves, or crop to
  the panel (`cropTo`).
- Render: 16:9 composition and a vertical 9:16 composition from the same timeline, both mastered to -14 LUFS, plus an
  `.srt` timed to the words.
- Review: storyboard stills (one per scene) and click-check stills (every press frame with its measured target box)
  before the final render.

## Richard's corrections from the Shop Manager videos

- No Free/Pro and no plans or prices.
- Don't explain or read out page addresses; name the screen.
- Real data only: the catalogue was cleaned of 21,032 test products before the tour (with undo files). For Image
  Hover: real images and texts in every style shown, never the layouts' Lorem copy.
- Privacy: no admin name, email, avatar or phone; the capture fails if they appear.
- Slower pace for the tour (speed 1.0, about 0.55 s gaps, 1 s before a section, 0.4 to 0.9 s holds).
- No clipped descenders in gradient headlines.
- One accent color only; no second "pain" color on problem scenes.
- No decorative dash or line before a label.
- Every wp-admin notice removed, count badges removed (the Image Hover WordPress.org choice).

## Deliverables

`out/final/`: `image-hover-effects-launch.mp4` (1920 x 1080), `image-hover-effects-launch-9x16.mp4` (1080 x 1920),
`.srt`, `voiceover/`; for a tour `out/tour/final/`. YouTube pack in `youtube/youtube-upload.md` (order of uploads:
tour, launch, Short; titles with 2 alternatives; descriptions with chapters and links; tags; pinned comment;
thumbnails 1280 x 720 with a 320 x 180 readability preview). Shorts: links in the description aren't clickable, so
pin the comment with the link and set the related video to the tour.
