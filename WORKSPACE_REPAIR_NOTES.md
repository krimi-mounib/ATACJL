# ATACJL v9 workspace repair notes

## What changed

- Shared **System / Light / Dark** theme control on public, member and admin pages, with a pre-stylesheet theme bootstrap and persistent browser preference.
- Palette tokens based on the requested warm theme, plus compatibility overrides for older components that used fixed colors.
- Admin login now opens the administration dashboard directly. The admin sidebar includes shortcuts to member features, and administrator accounts can reach admin tools from the member workspace sidebar as well.
- Page-content editor fields are organized into collapsible sections and limited to the selected public page. The editor no longer prints internal setting keys under every field.
- Site Settings now focus on contact details, social/map URLs and translation-provider configuration rather than duplicating page copy. Empty API-key input preserves the saved key.
- Content/settings saves use database transactions and safe flash messages on failure. URL fields only accept HTTP or HTTPS addresses.
- Unhandled exceptions get a safe themed error page with an error reference; detailed exceptions are logged server-side.

## Validation performed

- PHP syntax check: all 47 PHP files passed `php -l`.
- JavaScript syntax check: `node --check assets/js/app.js` passed.
- Static local PHP target scan: 31 distinct generated targets checked, none missing.
- Original v7 and v8 ZIPs passed `unzip -t`.
- Secret check: no `.env` file is included; `.env.example` remains available.

## What still needs environment testing

This workspace does not have the project's `pdo_mysql` extension or a configured MySQL database, so database-backed login, saves, uploads, translation-provider calls and role-protected request flows could not be run end-to-end here. The CSS rendering attempt also did not produce a browser screenshot in this environment. Please smoke-test those flows on your normal PHP 8.2+/MySQL setup before deployment.

## v10 UI polish follow-up

- Changed the language selectors to use the active theme surface/text/border tokens, including their native option lists. The public navigation search control now uses a charcoal surface and readable theme-aware controls in dark mode.
- Reworked the workspace header into a left-aligned brand/page breadcrumb and a single, neatly spaced action group for language, account, theme and sign-out controls. Improved text sizing and added responsive layout behavior to avoid crammed controls on smaller screens.
- Added a desktop collapsible icon-rail sidebar. Its collapsed/expanded preference persists between visits and between the admin/member workspaces; mobile retains its slide-out navigation behavior. Collapsed links keep accessible labels and hover titles.
- Standardized sidebar ordering in admin and member workspaces: personal/member workspace first, then overview and the same administration/content/operations groups.
- Cache-busted the shared CSS and JavaScript includes (`v=10`) so the updated styles and behavior load after deployment.
- Follow-up verification: all 47 PHP files pass `php -l`; `node --check assets/js/app.js` passes; CSS parser reports zero syntax errors. Live rendering and interactive behavior still need a browser smoke test on the configured PHP/MySQL installation.


## v11 redirect and contrast fix

- Fixed the shared redirect helper: bare page filenames now resolve within the current workspace when that file exists, while explicit paths such as `admin/index.php` and `member/profile.php` remain app-root-relative. Successful admin/member form submissions no longer redirect to nonexistent root-level copies of nested pages. Redirects use HTTP 303 for post/redirect/get and reject external hosts/path traversal.
- Added explicit high-contrast light-mode colors for the workspace brand, page title, username, divider, language selector and sign-out control.
- Bumped shared CSS/JS cache tokens to v11.
- Static verification includes per-route redirect resolution, PHP syntax, JavaScript syntax, local generated PHP targets, and ZIP integrity. Live database operations still require testing on the configured host.
