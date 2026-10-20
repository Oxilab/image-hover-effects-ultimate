# Docs: writing, publishing and SEO

Image Hover docs live on the live site `oxilab.dev` as the `docs` post type, under
`https://oxilab.dev/docs/image-hover-effects/<section>/<slug>/`. The method comes from the 34 Shop Manager docs and
their SEO pass (2026-07 to 2026-09), where Richard set every rule below.

## Workflow (strict, from Richard)

1. **Understand** the feature from the code and the real UI ([00-general.md](00-general.md) rule 3). Stop and report.
2. **Plan**: the list of docs (or the one doc) with section, title, focus keyword. Stop for approval.
3. **One doc at a time**: show the brief (title, slug, focus keyword, SEO title, meta description, outline,
   screenshots planned) and wait for a literal "YES".
4. Write the full doc, with screenshots ([screenshots-docs.md](screenshots-docs.md)).
5. **Publish only when Richard says so.** Then set the SEO fields and verify the live page.

Never skip ahead or batch several docs without his go.

## Fixing the existing docs (Richard, 2026-10-18)

Richard: "vul information dewa jabe na" (no wrong information may be given). For the docs that are already live:

- **Every fact matches the plugin.** Read every doc against the code and the real UI. When something doesn't match
  (a number, a menu path, a button label, a field, Free or Pro, a link, a screenshot), fix it. Leave out what can't
  be verified.
- **Remove what isn't needed.** A doc that only repeats other docs is removed: move anything useful into the doc that
  covers the topic, set the removed doc to draft (the MCP has no delete), and give Richard the 301 redirect
  (old URL → new URL) to add in Rank Math, because the MCP can't add redirects. Never remove a doc the plugin links
  to (see "Links into these docs from the plugin" below).
- **Modify the rest** in place: same id, same slug, current UI, house HTML, new screenshots.
- For this kind of fix Richard gave a standing go: no brief and no "YES" per doc. A brand new doc still follows the
  workflow above.
- **Still one doc at a time** (Richard, 2026-10-18: "ami chaiteci 1 ta 1ta kore doc update korte"): fix and publish
  one doc, give Richard its id and link, and wait for his check before starting the next. Never run ahead through
  several docs in one go.

## Focus keywords for docs: no keyword research (Richard, 2026-10-18)

Docs don't go through Keyword Planner. Claude picks the focus keyword: the 2 or 3 word phrase a user would really
type to find that doc, which the doc uses naturally. One keyword per page (check the other docs' keywords in the
table at the end of this file). The Keyword Planner workflow in [keyword-research.md](keyword-research.md) is for
blog posts only.

## Audience and voice

- Beginners: site owners and designers, not developers. Plain English, short paragraphs (under 120 words), numbered
  steps for anything done in order, the exact menu path and button labels as they appear on screen ("Use this
  design", "Create and edit", "Save", "Rename").
- Every claim verified in the code or the UI. Every screenshot matches its step.
- No dashes ([00-general.md](00-general.md) rule 4). Pro features are labelled "(Pro)" in the doc.

## House HTML (Gutenberg block markup, from the live docs)

```
<p class="wp-block-paragraph">Intro with the <strong>focus keyword</strong> early.</p>
<h2 class="wp-block-heading">Before You Start</h2>
<ul class="wp-block-list"><li>✅ ...</li></ul>
<h2 class="wp-block-heading">Step 1: ...</h2>
<ol class="wp-block-list"><li>...</li></ol>
<figure class="wp-block-image size-large"><img src="https://cdn.oxilab.dev/imagehover/docs/..." alt="..."/></figure>
<blockquote class="wp-block-quote is-layout-flow wp-block-quote-is-layout-flow"><p><strong>Tip:</strong> ...</p></blockquote>
```

Shape: intro paragraph with the bolded focus keyword, "Before You Start" checklist, H2 sections with numbered steps,
a screenshot per step that changes the screen, Tip blockquotes. The table of contents and the image lightbox are
added by the theme (`themes/oxilab/inc/oxilab-docs/`); don't paste a TOC into the content.

## Image Hover docs now (live, checked 2026-10-16)

Category ids (`docs_category`): parent **Image Hover Effects = 9**; Getting Started = 12, Effect Types = 13,
Settings & Configuration = 14, Page Builder Integration = 15, Customization = 16, Troubleshooting & FAQ = 17,
Extensions (Pro) = 18. Assign a doc to the parent **and** its section, for example `[9, 13]`.

| Section (slug) | Docs (slug) |
| --- | --- |
| ihe-getting-started | what-is-image-hover-effects-ultimate, how-to-install-activate-image-hover-effects-ultimate, creating-your-first-hover-effect (id 291), image-hover-effects-shortcode, free-vs-pro-whats-the-difference |
| effect-types | general-hover-effects, caption-hover-effects, flip-box-hover-effects, lightbox-gallery-effects, before-after-image-comparison-slider, button-overlay-hover-effects, square-image-magnifier-filter-gallery-carousel-effects |
| settings-configuration | general-plugin-settings-explained (id 301), google-fonts-font-awesome-custom-css (mobile-touch-device-behavior, id 302, removed 2026-10-18 and merged into 301) |
| page-builder-integration | how-to-use-image-hover-effects-with-elementor, how-to-use-with-wpbakery-visual-composer, using-as-a-wordpress-widget-in-gutenberg, using-the-native-gutenberg-block (id 429) |
| customization | typography-font-customization, colors-gradients-overlay-settings, animations-transition-effects, responsive-mobile-layout-settings, border-shadow-box-styling |
| troubleshooting-faq | hover-effects-not-working-how-to-fix (id 312), frequently-asked-questions-faq (plugin-conflicts-performance-optimization, id 313, removed 2026-10-18 and merged into 312) |
| extensions | display-post-show-wordpress-posts-with-hover-effects, carousel-slider-turn-hover-effects-into-a-sliding-carousel, filter-sorting-create-a-filterable-image-gallery |

30 docs, written 2026-03 (the Gutenberg block doc 2026-05). They already use H2 steps and R2 images
(`cdn.oxilab.dev/imagehover/docs/`), but their screenshots and steps show the interface **before** the 9.12.0 and
9.12.1 redesign (Create page, naming dialog, editor header, Settings, Shortcode page). Rewriting them means new
screenshots and re-checking every step.

Plugin features with no doc of their own (checked against the sitemap): the Shortcode page actions (copy PHP code,
clone, export, import, delete), the Settings Danger zone and License card (9.12.0), the Getting Started page, the
"How to use?" menu, the Image Magnifier on its own (it shares a doc with Square), Rebuild and Rearrange in the editor.

**Links into these docs from the plugin.** `Classes/Docs.php` (the "How to use?" menu and Getting Started) and the
cards on the Image Hover page (`Includes/Admin/Pages/Admin.php`, the `doc` keys) link to exact doc URLs. Never change
the slug of a linked doc; if it must change, add a redirect and update the plugin in the next release.

Find a doc's id: the live page's body class `postid-<id>`. `get_docs` returns only the newest 100 docs (most Image
Hover docs are older), so use `get_doc` with the id. The authoritative URL list is
`https://oxilab.dev/docs-sitemap.xml`.

## Tools: the `wordpress` MCP only

Richard doesn't want permission popups during docs work: use the `mcp__wordpress__*` tools (server
`/Users/richard/Local Sites/oxilab/oxilab-mcp-server`, registered in `/Users/richard/Local Sites/oxilab/.mcp.json`),
never node or curl scripts that write.

| Tool | Use |
| --- | --- |
| `get_docs_categories` | Category ids |
| `get_doc` | One doc with raw content (needed before editing) |
| `create_doc` / `update_doc` | Content, title, status, slug, categories |
| `update_seo` | Rank Math focus keyword, SEO title, meta description, canonical, robots, slug (through `rankmath/v1/updateMeta`, because the docs post type exposes no meta over `wp/v2`) |
| (no upload tool) | Doc images go to Cloudflare R2, which Richard uploads; the doc uses `cdn.oxilab.dev` URLs ([screenshots-docs.md](screenshots-docs.md)). The server's `upload_media` is not used for docs |

Blog posts use the post tools of the same server: [blog-posts.md](blog-posts.md).

Gotchas: the MCP sometimes answers "fetch failed"; retry, then check no duplicate was created. Don't re-fetch a doc
you just wrote; what you sent is what is stored. Write internal links absolute (`https://oxilab.dev/docs/...`) and
check each slug in the sitemap first (Shop Manager docs once linked to planned slugs that were never set: 404s).

The **Rank Math MCP** (`rankmath`, `https://oxilab.dev/wp-json/mcp/mcp-oauth-server`) has write access to the live
site's SEO settings, schema, redirects and sitemap. Start with read-only audits; show the diff and get Richard's go
before any write. It is OAuth: if it needs authorizing, Richard does it in an interactive `/mcp` session. Rank Math is
the free tier: some tools may be Pro only.

## SEO recipe per doc (scores land around 80)

1. Focus keyword: Claude's pick, no keyword research (see "Focus keywords for docs" above), 2 or 3 words the doc
   uses naturally (4 word phrases can't reach 1% density without stuffing), not used by another doc. Examples:
   "image hover effects", "image comparison slider", "lightbox gallery".
2. SEO title up to 60 characters, keyword first; add a number and a power word only where both are true of the doc.
3. Meta description up to 158 characters, keyword early.
4. Keyword in 3 to 5 of the H2s, only as a pure rename; never reword a step or a fact for SEO.
5. Keyword in at least one image alt that describes the screenshot honestly.
6. 5 or more internal links (to other Image Hover docs, checked in the sitemap) and 1 external link to a real
   authority (for example a WordPress.org page).
7. Density: Rank Math scores by band: under 0.5% fails, 0.5 to 0.75% = 2, 0.76 to 1.0% = 3, **1.0 to 2.5% = 6**.
   Aim just over 1.0%; nothing is gained above that. Length bands: 600 to 999 words = 2, 1000 to 1499 = 3,
   1500 to 1999 = 4; don't pad.
8. `update_doc` then `update_seo`. The score only updates when the doc is opened and saved in the editor (Rank Math
   computes it in the browser); never write a score through the API.

Tests you leave alone, and say so:
- **Permalink length**: Rank Math wants the URL under 75 characters. `https://oxilab.dev/docs/image-hover-effects/`
  is 44 characters and the section adds 10 to 25 (`extensions/` to `page-builder-integration/`), leaving very
  little for the slug. Keep the keyword in the slug rather than chasing this test, and never rename a live slug
  without a redirect (and see the plugin links above).
- **Table of contents**: the theme builds it at render time; Rank Math's test reads only the editor content.
- **Content AI**: needs Rank Math Pro.

Never raise a score by writing to what the score measures ([00-general.md](00-general.md) rule 11).
