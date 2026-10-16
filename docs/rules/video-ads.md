# Ad videos: Facebook and Instagram, TikTok, Google Ads (YouTube)

No Image Hover ad has been made yet. This file combines Richard's video rules ([video-common.md](video-common.md),
which all apply) with each platform's format. **On the first ad, confirm the open points marked "Ask Richard", then
record his answers here.** Platform specs change: before export, check the current safe zones and lengths in each
platform's ads manager and correct this file if they moved.

## What an ad must do

Stop the scroll in the first seconds, show the effects working on real output, and send people to one place. Built
from the explainer pipeline (`/Users/richard/Local Sites/oxilab/shop-manager-launch-video/`) plus the real hover
capture of the tutorial project, cut short and vertical first. Hover effects are visual, so they suit short ads well.

| Time | Beat | Notes |
| --- | --- | --- |
| 0 to 2 s | Hook | Movement plus a short line: a flat image turning into a hover effect, a comparison slider being dragged. The effect shows in the first second |
| 2 to 8 s | Show | Two or three real actions: pick a design, add an image, change a color, the result on a page |
| 8 to 12 s | Proof | Something true and checkable: 11 effect types, works with the block editor and Elementor, the WordPress.org page |
| last 2 to 3 s | Call to action | Logo, product name, one action ("Try it free on WordPress.org" or "Get Image Hover Effects"), the site |

## Rules for every ad

- **Sound off by default**: every spoken line is also burned-in captions (large, high contrast, inside the safe
  zone). The ad must make sense muted.
- **Real UI and real output only**, privacy rules as in every video. No mock-ups that the plugin can't produce.
- **Claims**: only what is true and shown. No superlatives (best, #1, ultimate as a claim, fastest), no invented
  numbers, no "500+ effects" or "1500+ layouts" (not countable in the code), no fake reviews or testimonials. The
  banned word list in `/Users/richard/Local Sites/oxilab/SKILL.md` is a good filter for ad copy too. ("Ultimate" stays
  only as part of the product name.)
- **No Free/Pro or prices** in the video ([video-common.md](video-common.md)). "Try it free" in the call to action is
  allowed only if Richard approves it for that campaign (Ask Richard).
- One message per ad. Make 2 or 3 hook variants of the same ad for testing instead of one long ad.
- Brand: pink accent, the dark plum stage for title cards, logo at the end; text never covers the effect being shown.
- English, international, no dashes.

## Formats

| Platform | Placement | Ratio and size | Length | Notes |
| --- | --- | --- | --- | --- |
| Facebook, Instagram | Reels, Stories | 9:16, 1080 x 1920 | 15 s main, 6 s and 30 s variants | Keep text and key UI out of roughly the top 14% and bottom 35% (Reels overlays) |
| Facebook, Instagram | Feed | 4:5, 1080 x 1350 (or 1:1, 1080 x 1080) | 15 s | Same edit reframed; captions inside the frame |
| TikTok | In-feed | 9:16, 1080 x 1920 | 9 to 30 s, 15 s main | Keep clear of the right edge (action buttons) and the bottom caption area; native feel, fast first second |
| Google Ads | YouTube skippable in-stream | 16:9, 1920 x 1080 | 15 to 30 s | Viewers can skip after 5 s: effect and brand visible before 5 s |
| Google Ads | YouTube bumper | 16:9, 1920 x 1080 | 6 s, not skippable | One idea, one effect, logo |
| Google Ads | YouTube Shorts and Demand Gen | 9:16 (and 1:1) | 15 to 30 s | Reuse the vertical cut |

Google's ABCD guidance for YouTube ads matches this: Attract early, Brand early and often, Connect with a clear
benefit, Direct with one call to action.

## Production

- Project folder: `/Users/richard/Local Sites/oxilab/image-hover-ads-video/`, compositions per ratio from one timeline
  (as the launch video's `Launch` and `LaunchVertical`).
- Voice: Jesse `sXcl1NoQHcU7NRh6Na5D` (the explainer voice) unless Richard picks another for ads (Ask Richard; he
  was given 19 voices to compare on 2026-10-16). Music: an upbeat ElevenLabs take shaped to the cut.
- Captions: burned in for every ad; also export the `.srt` for platforms that accept it.
- Thumbnail or first frame: a frame that already shows a hover effect mid animation.
- Export: H.264, 30 or 60 fps (check the platform), AAC, -14 LUFS, under each platform's file size limit.

## Landing page and tracking

- Where the ad sends people: **Ask Richard** per campaign (`https://wordpress.org/plugins/image-hover-effects-ultimate/`,
  `https://oxilab.dev/image-hover-effects/pricing/` or `https://demos.oxilab.dev/imagehover/demos/`). The page must
  show what the ad promised.
- UTM parameters on ad links are fine (the WordPress.org rule against tracking applies to links inside the plugin, not
  to ads).

## Deliverables

`out/ads/<campaign>/`: one MP4 per platform and ratio (`ihe-<campaign>-<hook>-9x16.mp4`, `-4x5`, `-16x9`, `-6s`),
captions, a `copy.md` with the primary text, headline and call to action button per platform (no superlatives), and a
`QA.md` (privacy, claims checked in code, safe zones checked on each platform's preview).
