# Image Hover Effects Ultimate

Before any task in this plugin, read `docs/README.md`. It sends each kind of task (code, security, release, doc
screenshots, WordPress.org screenshots, docs and SEO, blog posts, tutorial, explainer and ad videos) to its rule file
in `docs/rules/`, and the code reference is in `docs/code/`. Always read `docs/rules/00-general.md` first.

The rules that matter most:

- Never break a live site: change only what the task needs, keep old saved data readable, and prove it with a before
  and after comparison.
- Keep or improve security on every change and security test every code change before calling it done:
  review the diff, real attacks as every role, static checks, report (`docs/rules/coding.md`, Security test).
- Every PHP change passes PHPCS with WPCS on the changed lines (0 errors, 0 warnings) with the plugin's
  `phpcs.xml` and the shared tools in `/Users/richard/Local Sites/oxilab/tools/wpcs/` (`docs/rules/coding.md`).
- Verify every claim in the code or the real UI before writing it anywhere. Use only numbers counted in the code
  (11 effect types, 140 effects, 406 designs), never the readme's 500+ and 1500+.
- Image Hover and the Flipbox plugin are separate: work on this plugin with its own code and tables only.
- No em or en dashes anywhere. English, for an international audience.
- Never bump the version unless Richard asks. A version with a git tag is released.
- One item first, Richard approves, then the rest. Nothing goes live without his go.
- No personal data or license keys in any screenshot or video; real data only.
- One plain Bash command per call; tools live in project folders, not only in the scratchpad.

Core architecture in one line: `style_name` `general-6` → `Modules\General\Admin\Effects6` (editor) or
`Modules\General\Render\Effects6` (front end); all admin AJAX goes through `Classes/ImageApi.php` `post_<name>()`.

When Richard corrects something, update the matching file in `docs/rules/` in the same session.

This file and `docs/` are excluded from the release in `.gitattributes`.
