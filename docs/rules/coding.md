# Coding rules

How code in Image Hover Effects Ultimate is organised, how to change it safely, how to prove nothing broke, how
admin screens look, and how a release goes out. Read [00-general.md](00-general.md) first. The code reference is in
[../code/](../code/).

## Structure

| Rule | Detail |
| --- | --- |
| Namespace | `OXI_IMAGE_HOVER_PLUGINS\` maps to the plugin root (Composer PSR-4, `vendor/autoload.php`). Folder and class names match exactly: `Includes/Admin/Pages/Settings.php` is `OXI_IMAGE_HOVER_PLUGINS\Includes\Admin\Pages\Settings`. |
| Main file | `index.php`: Freemius init, final class `Oxilab_Imagehover` (singleton `oxilab_imagehover()`), boot on `init` priority 20. |
| Hooks that fire early | Anything added inside `init_plugin()` runs on `init` priority 20, so a hook that fires earlier is never reached: `widgets_init` (init 1), WPBakery's `vc_before_init` (init 9), `plugins_loaded`, `after_setup_theme`. Add those in the constructor, as `register_widget()` and `register_vc_element()` do. In 9.12.1 and earlier the widget and the WPBakery element never registered for this reason (fixed 2026-10-18). |
| Math with saved values (PHP 8) | Never calculate with a saved setting directly: PHP 8 stops the whole page with a TypeError for an empty or text value and a DivisionByZeroError for / 0, which ends the page right after the gallery. Use `$this->num( $value )` (empty or text counts as 0) and `$this->percent( $height, $width )` from `Page/Public_Render.php`. Check new layouts with `security-tests/image-hover-effects-ultimate/php8-fuzz.php` (0 fatal errors). Fixed in 9.12.2 after a Flipbox customer's page died this way (2026-10-20). |
| Admin pages | `Includes/Admin/Pages/` (Image Hover home, Shortcode, Settings, Getting Started). The template library and the editor are routed by `Includes/Admin/Menu::Image_Parent()` to module classes. |
| AJAX | One router: `Classes/ImageApi.php`. `image_hover_settings` calls `post_<functionname>()` on `ImageApi`; a new admin action is a new `post_<name>()` method there. Notices have their own small handlers in their `Support_*` class. |
| Effect types | `Modules/<Type>/`: `<Type>.php` (template library page), `Modules.php` (editor controls, shared by all effects of the type), `Admin/EffectsN.php` (editor per effect), `Render/EffectsN.php` (front-end HTML), `Files/` (CSS and JS), `Layouts/N/*.json` (ready designs). See [../code/modules.md](../code/modules.md). |
| Class names from data | `style_name` (`general-6`) becomes `Modules\General\Admin\Effects6` or `...\Render\Effects6`. This is core architecture: keep the format, and check `class_exists()` before `new $cls` in new code. |
| Data | Tables `{prefix}image_hover_ultimate_style` and `_list`, plus the shared `{prefix}oxi_div_import`. A new option goes into `Classes/Data_Cleaner.php` (SETTINGS or OPTIONS) so uninstall removes it. |
| Assets | Plain CSS and JS, no build step. Admin: `assets/backend/`; front end: `assets/frontend/` and each module's `Files/`. Enqueued in `Includes/Assets.php`, `Helper/CSS_JS_Loader.php`, `Page/Admin_Render.php` and each `Render/EffectsN::public_css()`. |
| Freemius | `oxilab_iheu_v()`. Pro check: the filter `oxi-image-hover-plugin-version` (`Admin_helper::check_current_version()`: legacy license option `image_hover_ultimate_license_status` = `valid`, or `can_use_premium_code()`). Before `Admin_Filters()` has run, use `has_pro_access()` instead. |
| Hex strings | `Admin_helper::Admin_Filters()` and the license checks name hooks and options as hex (`fixed_data('6f78...')`). The decoded names are in [../code/hooks-shortcodes.md](../code/hooks-shortcodes.md). Don't add new hex names; write plain strings. |

## Style

- Every new or changed line follows the WordPress Coding Standards in `phpcs.xml` and passes PHPCS (see PHPCS and
  WPCS validation below), also in older files written as `func($a)` without inner spaces: the lines you touch are
  written `func( $a )`, the lines you don't touch stay as they are (no mass reformatting).
- Comment density like the surrounding code: a short docblock per method, a comment only where the reason isn't
  obvious. No dashes in comments ([00-general.md](00-general.md) rule 4).
- New methods carry `@since` with the version that will ship them.
- Text domain `image-hover-effects-ultimate`; every new string is translatable and escaped.

## Security checklist (every change)

Admin side:
- Capability: the plugin's own permission, `ImageApi::get_permissions_check()` (the role chosen in Settings → Access,
  resolved to that role's first capability, default `manage_options`, cached for an hour in the transient
  `oxi_image_user_permission_role`). Destructive or site-wide actions (Danger zone) additionally require
  `manage_options`.
- Nonce `image_hover_ultimate` on every AJAX call (created in `CSS_JS_Loader::admin_js()` as
  `image_hover_settings.nonce`); the import form uses `image-hover-effects-ultimate-import`.
- Destructive actions confirm on the server too (Delete all data needs `confirm=DELETE`).

Every request:
- Sanitize input by type: `(int)`/`absint` for ids, `sanitize_key` for modes and function names,
  `sanitize_text_field` for text, `wp_unslash` first. Editor `rawdata` goes through `ImageApi::sanitize_rawdata()`
  (decodes the JSON and runs `wp_kses_post` on every key and string value).
- Escape output at the point of output: `esc_html`, `esc_attr`, `esc_url`, `wp_kses` with an allow list.
- SQL only through `$wpdb->prepare` or `$wpdb->insert/update/delete`; table names from `$wpdb->prefix` through
  `esc_sql`.
- Files: only read layout JSON from inside the plugin (`realpath()` check as in `post_create_new()`); never write files
  into the plugin folder; read uploads from `tmp_name`.
- CSS printed inside `<style>` must not be able to close the tag. Today only the exact `</style>` is removed
  (`str_ireplace`), which misses `</style >`; new code writes every `</` as `<\/` instead (see
  [../code/security.md](../code/security.md), open item 1).
- Never let a missing row or class cause a fatal error: check before `new $cls` and before reading array keys.

Public (logged out):
- The plugin has no public AJAX that works for visitors (see [../code/security.md](../code/security.md)); shortcodes
  only read rows by integer id. Keep it that way, or design a separate, read-only public endpoint.

Then record anything new or still open in [../code/security.md](../code/security.md).

## PHPCS and WPCS validation (mandatory, every code change)

Richard, 2026-10-16: "phpcs and wpcs validation korte hobe". Every PHP change passes PHP_CodeSniffer with the
WordPress Coding Standards before it is called done, together with the security test below.

**Tools.** PHPCS 3.13, WPCS 3.4 and PHPCompatibilityWP are installed once in
`/Users/richard/Local Sites/oxilab/tools/wpcs/`, outside the plugin: the plugin's `vendor/` folder is tracked in git
and ships, so never run `composer install` or `composer require` inside the plugin for dev tools. The checks use
this plugin's own `phpcs.xml` (WordPress, WordPress-Core, PHPCompatibilityWP for PHP 7.4+, the text domain, and the
sniffs the project turned off).

| Command | Use |
| --- | --- |
| `node "/Users/richard/Local Sites/oxilab/tools/wpcs/changed.mjs" "<plugin folder>"` | **The gate.** Checks only the lines changed since the last commit plus new PHP files in full. Must end with 0 errors and 0 warnings |
| `node ".../tools/wpcs/changed.mjs" "<plugin folder>" <tag>` | The same against a release tag, to check everything since that release |
| `".../tools/wpcs/check.sh" "<plugin folder>" <files>` | Full report for whole files |
| `".../tools/wpcs/check.sh" "<plugin folder>" --summary` | Totals for the whole plugin, to compare with the baseline |
| `".../tools/wpcs/check.sh" "<plugin folder>" --fix <files>` | phpcbf auto fix; only on files you are changing anyway, and review the diff line by line after (it may touch lines outside the task) |

**Rules.**
1. **Changed lines: 0 errors, 0 warnings** in `changed.mjs`. Fix the code; don't silence the sniff.
2. A `// phpcs:ignore <Sniff.Name> -- <reason>` only where the code is right and the sniff can't see it (for
   example a table name from `$wpdb->prefix`, or JSON written for a file download), always naming the sniff and
   the reason. Never a bare `phpcs:ignore` or `phpcs:disable` for a whole file, never one to hide a security sniff
   (`WordPress.Security.*`, `WordPress.DB.PreparedSQL*`) without the security test proving the line safe.
3. **The plugin total never goes up.** Run `--summary` before and after; compare with the baseline in
   `/Users/richard/Local Sites/oxilab/tools/wpcs/baseline-image-hover-effects-ultimate.txt`. Baseline on 2026-10-16: 9118 errors and 1520 warnings in 188 files.
   Re-record the baseline after a release or a cleanup.
4. **PHPCompatibility errors are never accepted**: the plugin must run on PHP 7.4 to 8.4.
5. **No mass reformatting.** Old violations on lines you don't change stay until Richard asks for a cleanup; a
   cleanup is its own task with its own before and after proof (behavior identical, output byte for byte).
6. **JavaScript** has no linter set up: `node --check` every changed file and follow the WordPress JavaScript
   style of the file.
7. **Report** the `changed.mjs` result (files checked, 0 errors, 0 warnings) and the before and after totals in
   the reply, next to the security test results.

## Security test (mandatory, every code change)

Richard, 2026-10-16: "code lekhar shomoi obboshoi security maintain korte hobe and security test korte hobe, jate kore
kono doroner security break na hoi." Every change to PHP or JS, however small, keeps security and is **security
tested before it is called done**. No exceptions, also not for "just a UI change". A change that fails a check is not
finished: fix it first. If the fix is outside the task, stop and tell Richard; never ship a known hole.

### 1. Review the diff before testing

For every changed or added line, write down in the test notes:
- **Inputs**: `$_GET`, `$_POST`, `$_REQUEST`, `$_COOKIE`, `$_FILES`, AJAX and REST parameters, shortcode and block
  attributes, imported files, and database values that a lower role or a visitor can write.
- **Who can reach it**: logged-out visitor, subscriber, the plugin's own role (it can be set to Editor or Author),
  administrator.
- **Where it goes**: HTML, attribute, URL, JavaScript, CSS inside `<style>`, SQL, a file path, a class or method name,
  a redirect, an outgoing request.

### 2. Check every item (WordPress threat list)

| Threat | Rule |
| --- | --- |
| Missing permission | Every handler checks the capability; destructive or site-wide actions need `manage_options` |
| CSRF | A nonce on every state change, checked on the server |
| IDOR | A posted id must belong to the parent being edited and to this plugin (its own table, its own type) |
| SQL injection | Only `$wpdb->prepare`, `insert`, `update`, `delete`; never a variable inside SQL |
| Stored and reflected XSS | Escape at output for the context (`esc_html`, `esc_attr`, `esc_url`, `wp_kses`); never echo raw input |
| DOM XSS | No `innerHTML`, `.html()` or `insertAdjacentHTML` with data that came from a user; use `textContent` or escape |
| CSS breakout | CSS printed in `<style>` writes every `</` as `<\/`; entity-decoded values are checked after decoding |
| Path traversal and files | No user value in a file path without `realpath` inside the plugin; uploads read from `tmp_name` after `is_uploaded_file`; never write into the plugin folder |
| Dynamic calls | A class or method name from a request only through an allow list |
| Object injection, code execution | No `unserialize`, `eval`, `assert`, `create_function` or variable `include` on user data |
| Open redirect, SSRF | `wp_safe_redirect`; outgoing requests only with `wp_safe_remote_*` to known hosts |
| Privilege escalation | A setting that grants access (the plugin's role) can only be changed by an administrator |
| Information leak | No paths, SQL errors, debug output or other users' data in responses; drafts, private and password-protected content stays hidden |
| Denial of service | Every count and page size from a request is clamped; no unbounded query |
| Bad input crash | Wrong type (array instead of string), missing key, unknown id, `-1`, huge numbers: a clean error, never a fatal 500 |
| Secrets | No key, token or license in code, logs, responses or commits |

### 3. Run real attacks against the local site

Write the test as a script in `/Users/richard/Local Sites/oxilab/security-tests/<plugin>/` (not only in the
scratchpad) and run it on `https://oxilab.test`:
- **As every role**: logged out, a subscriber, the plugin's role, an administrator. Create temporary users named
  "security test (delete me)" and delete them after (by id and name).
- **Without and with a wrong nonce**, and with another user's nonce.
- **Payloads** in every changed input: `<script>alert(1)</script>`, `"><img src=x onerror=alert(1)>`,
  `javascript:alert(1)`, `</style ><script>alert(1)</script>`, `&lt;/style &gt;&lt;script&gt;`, `' OR 1=1 -- `,
  `../../wp-config.php`, `-1`, `0`, `99999999`, an array where a string is expected, an unknown id, an id that
  belongs to another plugin or another parent.
- **Expected**: refused (403 or a clear error) or stored and printed safely escaped; check the saved row and the
  rendered page source, not only the response.

### 4. Static checks

- `php -l` on every changed PHP file, `node --check` on every changed JS file.
- The PHPCS and WPCS gate (section above) with 0 errors and 0 warnings on changed lines; the security sniffs
  (`WordPress.Security.*`, `WordPress.DB.PreparedSQL*`) are part of it and are never silenced without proof.
- Grep the diff for `echo`/`print` without escaping, superglobals without sanitizing, `->query(` without
  `prepare`, `unserialize`, `eval`, `file_put_contents`, `include` with a variable, `innerHTML`, `.html(`.

### 5. Re-test what was fixed before

Run the earlier attacks from [../code/security.md](../code/security.md) again, so an old hole never comes back.

### 6. Report

In the reply and in [../code/security.md](../code/security.md): a table of each check and attack with its result
(the command and the output for anything that failed), what was not tested and why. A security fix is proven by
running the attack against the old code and the new code (see Testing).

### Where to attack in this plugin

| Surface | Reached by |
| --- | --- |
| `image_hover_settings` → `ImageApi::post_*` | The plugin's role (can be Editor or Author), nonce `image_hover_ultimate` |
| `image_hover_ultimate` (also `nopriv`) → `ajax_action()` dynamic class | Anyone; must stay refused for visitors unless a safe public endpoint is designed |
| `oxi_image_hover_preview_frame` | Any logged-in user today (open item) |
| JSON import form (Shortcode page) | Users with the plugin's role and `upload_files`; the file is attacker controlled |
| Style CSS printed in `<style>` (late CSS, admin, Elementor, block preview, preview frame) | Anyone who can save or import a style; seen by every visitor (open item 1) |
| `[iheu_ultimate_oxi id]`, the block, Elementor, WPBakery, the widget | Authors who can add shortcodes or blocks |

## Testing: prove nothing broke

Pick the method that fits, run it **before** and **after** the change, and compare.

| Change | Method |
| --- | --- |
| Front-end output | Render every saved style's shortcode in a PHP bootstrap (`do_shortcode('[iheu_ultimate_oxi id="N"]')` for every id in the style table) and hash the HTML plus the collected `Public_Render::$pending_late_css`. Run the "before" twice to prove it is deterministic (a style without an id uses `wp_rand()`). |
| Editor | Playwright: open the editor for one style of each effect type, serialize `#oxi-addons-form-submit`, click Save, compare the saved `rawdata`, `stylesheet` and `font_family` byte for byte with a backup. Collect `pageerror` events. |
| CSS generation | For every style row: `new Modules\<Type>\Admin\EffectsN('admin')` then `template_css_render()` on a copy, and compare the CSS with the stored `stylesheet`. |
| Old rows | Include rows saved by old versions (escaped JSON, `wp_kses_post` damaged quotes); `decode_rawdata_array()` must still decode them. |
| Admin screens | Playwright: load the page, check the elements, click the buttons, collect `pageerror` events. |
| Security fix | Run the attack against the old code (put the original file back briefly, then restore and `cmp` it) and the new code; show both results. |

Rules:
- Back up any real row you will touch (JSON in the scratchpad) and confirm it is identical at the end.
- Test on the real local site, logged in through `auth-cookies.php` (no passwords).
- `php -l` every PHP file you changed; `node --check` every JS file.
- Report results with numbers ("128 of 128 renders identical"), and say plainly what was not tested.

## Admin UI design

The plugin screens were redesigned in 9.12.0 and 9.12.1 (Settings, Account, Shortcode, Getting Started, Image Hover
home, Create New, the editor). They share one look:

| Token (CSS variable in `assets/backend/css/settings.css`) | Value | Use |
| --- | --- | --- |
| `--iheu-brand` | `#ee326e` | Primary buttons, active states, links (logo pink; 3.95:1 on white, approved by Richard) |
| `--iheu-brand-strong` | `#c41d55` | Hover and pressed |
| `--iheu-brand-soft` / `--iheu-brand-ring` | `#fdecf2` / `rgba(238,50,110,.28)` | Soft buttons, chips, focus ring |
| `--iheu-success` / `--iheu-success-soft` | `#067647` / `#ecfdf3` | Saved status, success badges |
| `--iheu-danger` / `--iheu-danger-soft` | `#b42318` / `#fef3f2` | Delete, Danger zone |
| `--iheu-ink`, `--iheu-line`, `--iheu-radius` | `#101828`, `#eaecf0`, `14px` | Text, borders, card corners |

- New pages use the `oxi-iheu-set-*` building blocks from `settings.css`: hero (logo tile, title, one line of help,
  actions on the right), cards, rows with on/off switches, in-page dialogs (`oxi-iheu-set-dialog`, never the browser's
  `confirm()`), a status line for saves.
- Each page loads only its own CSS (`Includes/Assets.php` by `$_GET['page']`). The editor's visual layer
  (`editor.css`) applies only under `body.oxi-iheu-editor`; Account and Getting Started get `oxi-iheu-menu-page`.
- `admin.css` is old and global on the plugin pages: it sets `.notice{display:block!important}`,
  `.oxi-addons-wrapper a{color:#ff6138}` and `.oxi-addons-wrapper img{width:100%}`. Scope new rules through your own
  class with enough specificity rather than editing `admin.css`.
- For non-technical site owners: plain words, an example line where a setting is abstract, friendly empty states,
  Pro parts labelled.
- Never restyle Freemius screens globally: the Account page is wrapped and scoped (`Includes/Admin/Account.php`,
  `account.css`).

## Known issues to keep in mind

- **Load more and infinite scroll do not work for visitors.** Display Post and dynamic load (Pro) post to the
  `image_hover_ultimate` AJAX action, whose handler `ImageApi::ajax_action()` starts with the admin permission
  check (added in 9.11.5), so a logged-out visitor gets nothing back. Read from the code on 2026-10-16, not yet
  run in a browser; confirm before fixing.
- Admin notice dismissal through `post_notice_dissmiss()` reads `$this->request`, which is never set; the working
  path is `Support_Reviews::ajax_action()` (`wp_ajax_oxi_image_admin_notice`). That handler checks the nonce but not
  the capability and doesn't end with `wp_die()`.
- The role cache: after changing "Who can edit", AJAX keeps the old capability for up to an hour (transient
  `oxi_image_user_permission_role` is not cleared on save); the menu uses the new role at once.
- `post_web_import()` reads a `template/` folder that doesn't exist in the plugin; the Create page's old "Demos"
  buttons that called it are gone. Dead code.
- `Includes/Installation.php` (a Flipbox copy, uses `OXI_FLIP_BOX_PLUGIN_VERSION`) and `Includes/Frontend.php` are
  not used; `Includes/Admin/Notice.php` is an empty class. Don't call them.
- `Public_Helper::validate_post()` reads `$data` before it is set when called with an empty value (PHP warning).
- The free plan labels the color and gradient fields "Pro Only" in the editor (inline script in
  `Admin_Render::import_font_family()`).

## Release

1. **Never bump the version unless Richard asks** ([00-general.md](00-general.md) rule 6). Released versions have a
   git tag (`v9.12.1` is the newest on 2026-10-16).
2. The version lives in three places: the `Version:` header and `define('OXI_IMAGE_HOVER_PLUGIN_VERSION', ...)` in
   `index.php`, and `Stable tag:` in `readme.txt`.
3. Changelog lines go in the top section of `readme.txt` (format in [00-general.md](00-general.md) rule 6).
3a. **Integration check before every release** (added 2026-10-18 after the widget and WPBakery element were found
   silently missing from 9.10.5 to 9.12.1, about a year): run
   `security-tests/image-hover-effects-ultimate/builder-registration.php` and `render-hash.php`. The widget, the
   WPBakery element (once), the block, the Elementor widget and both shortcodes must register, and every saved style
   must render. A missing integration blocks the release.
4. Richard commits on `dev` and pushes a tag. `.github/workflows/deploy-org.yml` (10up deploy action, slug
   `image-hover-effects-ultimate`) then ships to WordPress.org SVN, including the `.wordpress-org/` banners, icons and
   screenshots. `update-assets.yml` listens to a `trunk` branch this repo doesn't have. Claude never commits, tags or
   pushes unless asked.
5. What ships is decided by `.gitattributes` `export-ignore`: `docs/`, `CLAUDE.md`, `.wordpress-org/`, `.github/`,
   `composer.*`, `phpcs.xml`, `phpunit.xml` stay out. Add any new non-shipping file there. `vendor/` ships (Freemius).
6. Readme rules: the WordPress.org readme audit guide in `/Users/richard/Local Sites/oxilab/SKILL.md` (no
   superlatives such as best, ultimate, most powerful, #1; at most 5 tags; short description up to about 150
   characters; plugin name only in install step 1). The current readme still has "the most powerful image hover
   effects plugin", "No jQuery" (the plugin loads jQuery) and the 500+/1500+ numbers: fix them when Richard asks for a
   readme pass.
7. Screenshots and their captions: [screenshots-wporg.md](screenshots-wporg.md).
