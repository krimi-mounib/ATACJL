# ATACJL redesign release notes

## Visual and UX changes

- Reworked the public navigation into a light, responsive header with language selection, account menu, search, focus-visible states and a compact mobile menu.
- Replaced the logo-as-hero layout with a branded illustration panel and stronger editorial heading/CTA hierarchy. Real event/project photography is displayed wherever the content already has an image; no fictional project photos or impact claims were added.
- Standardized colors, type, spacing, card layouts, forms, buttons, tables, status chips, empty states and motion preferences.
- Built a dedicated deep-green admin sidebar and a lighter member portal shell, both with responsive navigation and RTL layout handling.
- Reorganized the admin overview around membership applications, pending participation claims, contact messages, event management, content summary and recent audit activity.
- Added an actual member dashboard and upcoming-event listing.
- Added translation tabs to the content editor; only English is required, and missing translations fall back to available copy.
- Improved projects/news/events/gallery/contact/about pages and added more helpful missing-content/not-found states.

## Correctness and setup changes

- Fixed the event RSVP SQL placeholder mismatch and added duplicate-registration and capacity checks inside a transaction.
- Closed registration for events that are no longer available and show registration state/counts.
- Added the missing `team` table to the fresh schema and added `database/migrate_v3.sql` for existing installations.
- Restricted member search results to member/admin roles.
- Hardened the language redirect against external open redirects.
- Removed the seeded `admin` / `admin` account from fresh schema installs. Use `php scripts/create-admin.php` from a terminal to create an administrator securely.
- Sanitized `.env.example`. The distributable archive intentionally excludes `.env`, database backups, and error logs.

## Existing limitations intentionally not disguised as completed work

- Member documents remain a placeholder and need a permission-checked private storage layer before uploading private files.
- The current project does not have a private messaging/conversation schema. A proper forum or private-message feature should be scoped before implementation.
- Language switching and RTL behavior are retained and improved, but not every hardcoded sentence on every legacy page has a full translation in all six languages. Existing translated content fields are supported, with fallback to available copy.
- The website needs a production runtime smoke test against the actual target database and PHP extensions before deployment.

## v4 improvements

- Added a settings-driven content layer for the home page, about page, projects/news/events/gallery/contact introductions and key calls to action.
- Extended `Admin → Site settings` with public copy and translation-provider configuration.
- Added `translate-api.php`, a server-side LibreTranslate-compatible API proxy. API keys stay on the server and are not exposed to browser JavaScript.
- Added `database/migrate_v4.sql` for existing installs and included equivalent seeded defaults in `database/schema.sql` for fresh installs.
- Updated the visual palette to royal blue, sky blue and red based on the ATACJL logo; added subtle hover and scroll reveals with reduced-motion support.
- Validated PHP syntax for all PHP files and JavaScript syntax for `assets/js/app.js`.


## v7 compatibility and navigation patch

- Added `database/migrate_v7.sql` to create missing operational tables used by the admin dashboard, contact inbox, volunteer applications and event assignment metrics. Run after v6 on existing installations, after taking a database backup.
- Wrapped optional admin read queries with logged, non-fatal compatibility handling so a missing optional table no longer turns the entire dashboard into HTTP 500.
- Hardened contact inbox and volunteer status updates with server-side error logging and a useful flash message.
- Added a branded SVG favicon and an avatar placeholder at `member/default.png` for older profile-photo references.
- Polished sidebar navigation focus/hover states and mobile drawer presentation, preserving the pale member sidebar and dark admin sidebar.
- Verification: all 47 PHP files pass `php -l`; `assets/js/app.js` passes `node --check`; local static asset references and ZIP integrity pass. Live MySQL-backed workflows still require testing on the user's installation.


## v8: warm theme tokens and editor clarity
- Replaced the former blue/white/red base tokens with the requested warm ivory, deep brand blue, terracotta and gold palette.
- Added a persistent light/dark toggle. First visit follows the operating-system preference; the chosen light/dark mode is remembered in local storage.
- Dark mode uses dedicated warm charcoal surfaces, ivory text, pale blue brand controls, brighter terracotta/gold accents, and separate border tokens rather than mechanically inverting colors.
- Clarified content editor translation guidance and added server-side English-title validation plus invalid-delete ID checks.
- Admin/member workspaces retain their existing shell and sidebar styling; administrators can still use member-only features through role-aware routes where permitted.

## v9: global theme, cross-workspace navigation, editor and reliability fixes

- Added an accessible theme selector to public, member and admin headers. It cycles through **System → Light → Dark**, persists an explicit selection in browser storage, and follows OS preference while set to System. The choice is applied before CSS loads to reduce theme flash.
- Applied the warm ivory/brand-blue/terracotta/gold theme tokens to legacy cards, forms, tables, status messages, navigation, footer details, member panels and other previously hard-coded color surfaces.
- Kept the admin sidebar layout and added a **Member Workspace** section to the admin shell; administrators can reach their own dashboard, events, profile, directory and documents. Admin tools are also listed in the member-area sidebar for admin accounts.
- Reorganized **Content Management → Edit public pages** into named, collapsible sections. Only fields for the selected page are displayed, raw database setting keys are no longer shown under every input, and one save action persists that page's current fields in a transaction.
- Separated page wording from **Site settings**. Site settings now focus on public contact details, social/map links and translation provider credentials, avoiding duplicate editing of page copy. Blank API-key input preserves the saved key; URLs must be complete HTTP(S) URLs.
- Added transactional saves and safe, logged failure messages for page content and site settings. The global exception handler returns a themed, user-safe error page with a reference code while logging diagnostic details server-side.
- Verification completed: PHP lint passed for all PHP files; `node --check assets/js/app.js` passed; static route checks found no missing PHP targets in generated local links. Live database workflows and browser rendering still need a smoke test in an environment with the project's required PHP/MySQL extensions and configured database.

## v10: topbar, controls and sidebar polish

- Fixed public and workspace language dropdown colors in dark mode and styled the public search control to use warm charcoal surfaces, readable text and theme-aware accents.
- Reworked the admin/member topbar: the page title is left-aligned and easier to read, while language, profile, theme and sign-out controls sit together in a clear responsive action group.
- Made the sidebar collapsible on desktop, with an icon-only compact rail, tooltips/accessibility labels, and a saved preference across admin and member navigation. Mobile still uses the slide-out sidebar.
- Standardized the order of the shared member links and admin tools between both workspaces.
- Verification: all 47 PHP files pass syntax checks, JavaScript passes `node --check`, and CSS validation reports zero syntax errors. Browser/database end-to-end testing remains environment-dependent.


## v11: workspace form redirect and header contrast repair

- Fixed nested admin/member POST redirects to return to the correct local page rather than a nonexistent app-root URL.
- Explicit light-mode contrast colors keep the ATACJL brand and workspace page titles readable.
