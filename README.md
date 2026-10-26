# ATACJL NGO Management Platform

ATACJL is a server-rendered PHP 8.2+/MySQL platform for the public website, member accounts and administrative operations. The redesigned interface separates the public website, member portal and admin workspace while retaining the existing PHP/MySQL architecture.

## Included

- Public home, About, Projects, News, Events, Gallery, Contact, Search, Membership and Volunteer pages.
- Responsive public design with a shared forest-green/sage design system, accessible focus styles and improved mobile navigation.
- Admin workspace with overview metrics, membership/participation queues, event management, recent audit activity, CRUD tools and direct navigation to member features.
- Member dashboard and event listing, profile/settings, member directory, participation claims and documents placeholder; administrator accounts can access member features and admin tools through a combined cross-workspace sidebar.
- Username/email authentication with bcrypt password hashing, CSRF tokens, role checks and session regeneration on login.
- Admin CRUD for Projects, News, Events, Gallery, Team, Users, Memberships, Volunteers and Site Settings.
- Event RSVP with duplicate registration handling, capacity checks under a transaction, and a corrected insert statement.
- Audit logging, PDO prepared statements, output escaping and server-side error logging.
- English, French, Arabic, German, Italian and Spanish language selection. RTL direction is applied for Arabic; content translation coverage depends on available translated fields.

## Requirements

- PHP 8.2 or later.
- MySQL 8+ (or a compatible MySQL/MariaDB version supported by the schema).
- PHP extensions: PDO MySQL, fileinfo, mbstring, cURL.
- Apache with the included `.htaccess`, or equivalent Nginx rules for denying access to `.env` and `/storage`.

## Setup (new installation)

1. Copy `.env.example` to `.env` and configure the application URL, database connection and your own ImgBB API key. Do not commit `.env`.
2. Create/import the database by running `database/schema.sql` with a MySQL account that can create the database.
3. For an upgraded existing installation, back up the database first, then review and run `database/migrate_v2.sql` if required and `database/migrate_v3.sql`. Do not import a fresh schema over an existing production database without reviewing the SQL.
4. Point your web server document root to `atacjl-php/` and configure HTTPS for production.
5. Create the first admin from a terminal in this directory: `php scripts/create-admin.php`. The script refuses web requests, prompts for credentials and never uses a hardcoded default administrator password.
6. Sign in through `/login.php`, verify the admin account and ensure the ImgBB key is configured if image uploads are needed.

## Image storage

Images are uploaded to ImgBB through the server using `IMGBB_API_KEY`. The application stores the resulting image URL in MySQL. Use a private secret manager or an untracked `.env` file for the API key.

## Security and deployment notes

- Passwords use `password_hash()` / `password_verify()`; new admin accounts should use a unique password of at least 12 characters.
- POST mutations require CSRF tokens. Role-based access control is checked server-side.
- PDO prepared statements are used for user-supplied database values; output is HTML-escaped.
- `.env` and `/storage` should not be publicly accessible. The distributable source ZIP excludes `.env`, SQL backups and logs.
- The original project documented a default `admin` / `admin` login. This release removes that known default from fresh schema installs. On an existing database, verify that no default or shared admin credentials remain and rotate them immediately.
- Use HTTPS, regular database backups, least-privilege database accounts, rate limiting/WAF and a transactional email provider before a public production launch.
- The member documents page remains a placeholder by design. Do not upload private documents to a public folder without adding a permission-checked private storage layer.

## Design notes

The design uses reusable tokens and shared components in `assets/css/style.css`. Real ATACJL event photographs are preferred over illustrative stock images; when no featured image is set, the interface uses a branded placeholder. Optional integrations such as private messaging, real-time chat and calendar services should be specified separately before they are implemented.


## ATACJL v4: editable pages, translation API and visual polish

- **Editable public copy:** open `Admin → Page content` to edit the wording and labels on Home, About, Projects, News, Events, Gallery and Contact. Use `Admin → Site settings` for organization details, social links, map and translation-provider settings.
- **Editorial content:** `Admin → Projects & content` manages project, news and event records (including their language-specific titles and descriptions); `Admin → Gallery` manages albums and photos.
- **Translation API:** English is the default/source layout. Visitors can select English, French, Arabic, German, Italian or Spanish in the language menu. For non-English public pages, visible copy—including content entered in another language—is translated through the configured LibreTranslate-compatible API. Configure the endpoint and optional key under Site settings, or with `TRANSLATION_API_URL` and `TRANSLATION_API_KEY` in `.env`. If the provider is unavailable, the original content remains visible and a visible error notice explains that automatic translation failed.
- **Existing installations:** back up the database, then run `database/migrate_v4.sql` followed by `database/migrate_v5.sql` after the existing migrations. Fresh installations should use `database/schema.sql`.
- **Visual refresh:** the design palette now follows the logo's blue/red identity and includes restrained hover and scroll-reveal effects with reduced-motion support.

Translation quality and availability depend on the configured translation provider; for production, use a reliable hosted instance/API with suitable rate limits and terms.


## Page copy editor and translation reliability (v5)

- Administrators can edit the text and headings for Home, About, Projects, News, Events, Gallery and Contact under **Admin → Page content**.
- Projects, news stories and events retain full create/edit/delete workflows under **Projects & content**; albums and gallery images can be edited, uploaded and removed under **Gallery**.
- Run `database/migrate_v5.sql` after `migrate_v4.sql` on an existing installation. Fresh installs should use the updated `database/schema.sql`.
- Translation calls are now batched and a provider error is shown on the public page instead of silently leaving English copy in place. The default community LibreTranslate mirror is for development/testing and does not guarantee uptime; for production use a dedicated provider or self-hosted LibreTranslate URL and configure any required API key in **Admin → Site settings**.


## ATACJL v6: admin access, directory compatibility and translation failover

- Admins now see **Administration dashboard** and **Edit public pages** directly in the member-portal sidebar. Page-copy editing remains available at `/admin/pages.php`.
- The member directory now detects optional profile columns on the installed database, excludes inactive/banned accounts when those fields exist, and logs genuine directory errors instead of emitting the generic crash page.
- The default LibreTranslate-compatible endpoint is now `https://translate.cutie.dating/translate`, which is listed in the official LibreTranslate community mirrors directory. Public community mirrors can still go offline, so the translation client retries the current listed mirrors in the browser if the local PHP host cannot reach the saved server provider. For reliable production service, configure a paid API endpoint or self-host LibreTranslate.
- The translation connection-test button checks the configured backend and browser fallback. The language selector now submits immediately after a new language is selected.
- Existing installation: back up files and the database, then run `database/migrate_v6.sql` after `database/migrate_v5.sql`. Fresh installation: import `database/schema.sql`. No application data is deleted by this migration.


## ATACJL v7: operational-page resilience and sidebar polish

- Existing installation: back up files and the database, then run `database/migrate_v7.sql` after `database/migrate_v6.sql`.
- The migration creates missing contact inbox, volunteer application, audit and event-assignment tables without replacing existing tables or deleting records.
- The admin overview and inbox/volunteer screens now log database compatibility errors and render safe empty states instead of an unhandled HTTP 500 for missing optional tables.
- Added a branded favicon and a member-directory avatar fallback; the shared admin/member navigation has improved mobile drawer behavior and active states.
- The migration uses `CREATE TABLE IF NOT EXISTS`; if an existing table is present with different columns, review `storage/php-error.log` and align that table before use.

## Theme and workspace updates (v9)

- A shared **System / Light / Dark** control is available on the public site and both authenticated workspaces. System follows the device preference; explicit choices persist in the browser.
- Administrators can switch between admin tools and member features through the matching sidebar links while keeping the existing workspace navigation styling.
- The **Edit public pages** screen is grouped into expandable areas for the selected page. Use **Site settings** for organization contact details, social/map URLs and translation provider configuration.
- Page-content and site-settings writes are transactional and report safe, user-facing errors if a save fails.
- For deployment, use the PHP extensions listed above and test the website against the real MySQL database before publishing changes.
