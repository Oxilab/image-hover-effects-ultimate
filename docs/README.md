# Image Hover Effects Ultimate docs: start here

Everything needed to work on **Image Hover Effects Ultimate** without Richard having to explain it again: how the
plugin is built, and the rules for every kind of job (code, screenshots, docs, blog post, SEO, videos, ads).

This folder is excluded from the release (`/docs export-ignore` in `.gitattributes`), so nothing here ships to
WordPress.org.

**Image Hover and Flipbox are separate plugins.** Work on Image Hover only with this plugin's code and its own
tables (`image_hover_ultimate_style`, `image_hover_ultimate_list`); the Flipbox *module* inside this plugin
(`Modules/Flipbox/`) is not the Flipbox plugin.

## How to use it

1. Richard names a task ("make a tutorial video about the lightbox", "fix this bug", "write the comparison doc").
2. Read [rules/00-general.md](rules/00-general.md) first, every time. It holds the rules that apply to all work.
3. Read the file for the task from the table below, and the code docs it points to.
4. Follow its checklist. Where a rule file says **Ask Richard**, ask before going on; everything else is already
   decided, so don't ask again.
5. When Richard corrects something, update the rule file in the same session, so the correction is never lost.

## Which file for which task

| Task | Read |
| --- | --- |
| Any code change, bug fix, security fix, new feature | [rules/coding.md](rules/coding.md), then [code/](code/) |
| Release, version, changelog, readme.txt | [rules/coding.md](rules/coding.md) (Release section) |
| Screenshots for a doc on oxilab.dev | [rules/screenshots-docs.md](rules/screenshots-docs.md) |
| Screenshots for the WordPress.org plugin page | [rules/screenshots-wporg.md](rules/screenshots-wporg.md) |
| Write or update a doc, publish it, SEO with Rank Math | [rules/docs-publishing-seo.md](rules/docs-publishing-seo.md) |
| Blog post (SEO, AEO, GEO, Rank Math 80+, hook, tags, images) | [rules/keyword-research.md](rules/keyword-research.md) first, then [rules/blog-posts.md](rules/blog-posts.md) and [rules/docs-publishing-seo.md](rules/docs-publishing-seo.md) for the Rank Math facts |
| Keyword research (volume, difficulty, competitors, money keywords) | [rules/keyword-research.md](rules/keyword-research.md), [marketing/keyword-map.md](marketing/keyword-map.md) |
| Any video | [rules/video-common.md](rules/video-common.md) plus one of the three below |
| Tutorial video (YouTube, how to use) | [rules/video-tutorial.md](rules/video-tutorial.md) |
| Explainer or launch video (what it is, why use it) | [rules/video-explainer.md](rules/video-explainer.md) |
| Ad video for Facebook and Instagram, TikTok or Google Ads | [rules/video-ads.md](rules/video-ads.md) |
| Support reply to a customer | [rules/00-general.md](rules/00-general.md) (Support replies) and the code docs |

## Folder map

```
docs/
├── README.md                 this file
├── rules/                    how to do each job
│   ├── 00-general.md         rules for all work (read first)
│   ├── coding.md             code structure, security, testing, admin UI, release
│   ├── screenshots-docs.md   screenshots inside oxilab.dev docs
│   ├── screenshots-wporg.md  WordPress.org screenshot set
│   ├── docs-publishing-seo.md writing docs, publishing them, Rank Math SEO
│   ├── keyword-research.md   keyword data (volume, difficulty, CPC, intent), competitors, money keywords
│   ├── blog-posts.md         blog posts: hook, SEO, AEO, GEO, tags, featured and inside images
│   ├── video-common.md       rules shared by every video (every video opens with a hook)
│   ├── video-tutorial.md     tutorial videos (the Image Hover v3 method)
│   ├── video-explainer.md    explainer and launch videos (Shop Manager method)
│   └── video-ads.md          Facebook, Instagram, TikTok and Google Ads videos
├── marketing/                keyword-map.md (every targeted keyword and its page), data/ (raw exports)
└── code/                     how the plugin is built
    ├── overview.md           identity, boot, request flow, links
    ├── file-map.md           every folder and file, and where to make a change
    ├── classes.md            every class and trait in Classes/, Helper/, Includes/, Page/
    ├── admin-pages.md        every admin screen, its URL, files and assets
    ├── modules.md            the 11 effect types, their effects and designs, Free and Pro
    ├── hooks-shortcodes.md   hooks, AJAX functions, shortcodes, block, widgets, JS objects
    ├── database.md           tables, options, transients, data flow
    ├── assets.md             CSS, JS, libraries, what loads where
    └── security.md           the security model, audit history and open items
```

## Product facts used everywhere

| What | Value |
| --- | --- |
| Name | Image Hover Effects Ultimate (WordPress.org slug `image-hover-effects-ultimate`) |
| Plugin folder | `/Users/richard/Local Sites/oxilab/app/public/wp-content/plugins/image-hover-effects-ultimate` |
| Local test site | `https://oxilab.test` (Local by Flywheel, see [rules/00-general.md](rules/00-general.md)) |
| Live docs | `https://oxilab.dev/docs/image-hover-effects/` |
| Demos | `https://demos.oxilab.dev/imagehover/demos/` (one page per effect type under `/imagehover/elements-extension/`) |
| Pricing | `https://oxilab.dev/image-hover-effects/pricing/` |
| WordPress.org page | `https://wordpress.org/plugins/image-hover-effects-ultimate/` |
| Support forum | `https://wordpress.org/support/plugin/image-hover-effects-ultimate/` |
| Freemius | `oxilab_iheu_v()`, product id 20097, slug `oxi-image-hover-ultimate`, public key `pk_5ae72b1982d4390e3fef586fb2b4e` |
| Brand colors | pink `#EE326E` (main), `#C41D55` (strong), `#FDECF2` (soft); details in [rules/coding.md](rules/coding.md) |
| Logo files | `image/logo.png` (square mark, 75 x 75), `image/sm-logo.svg` (wordmark, 278 x 45, too wide for a square tile), `image/dash-icon.svg` (menu and Elementor icon), `.wordpress-org/icon-256x256.png` |
| Getting started video | `https://www.youtube.com/watch?v=9nqUpIjAFdA` (Getting Started page and readme) |
