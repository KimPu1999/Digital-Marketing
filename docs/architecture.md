# Initial architecture

## Runtime boundaries

The Next.js application owns the public website experience, reusable presentation components, responsive layouts, metadata, and public content fetching. The Laravel application owns REST endpoints, validation, persistence, queue work, and administrator authentication.

    Browser
      |
      v
    Next.js frontend :3000  ---- JSON over HTTP ---->  Laravel API :8000
                                                           |
                                                           v
                                                    PostgreSQL database

## Frontend structure

- src/app/ contains routes, layout, metadata, and global tokens.
- src/components/site.tsx contains shared site chrome and the original UI primitives.
- src/components/marketing.tsx contains the public page templates, sections, cards, filters, and submission forms.
- src/components/admin.tsx contains the responsive control center and inquiry workflow views.
- src/lib/content.ts is a typed, clearly labeled development fixture module. It is the only source for preview service, package, portfolio, blog, team, testimonial, and FAQ content.
- src/lib/api.ts is the public content and submission adapter.
- src/lib/admin-api.ts is the authenticated control-center adapter.
- Route files under src/app/ stay thin and compose shared page templates.
- Browser-state interactions such as mobile navigation, FAQ accordions, filters, admin drawers, and form notices are isolated to client components.

## Backend structure

The backend is a Laravel REST API with PostgreSQL environment defaults, safe content migrations, session-based Sanctum authentication, Form Requests, API Resources, policies, queue work, and feature tests. New API endpoints are introduced under routes/api.php, backed by request validation, authorization, persistence, and audit metadata.

## Design tokens

The foundation uses the supplied blue and white palette as CSS variables. Components use a maximum content width of 1280px, restrained borders, soft shadows, visible focus rings, and responsive spacing. The font stack is Plus Jakarta Sans with Noto Sans Myanmar fallback support.

## Backend and API scope

The Laravel layer owns published content, admin CRUD, session-based Sanctum authentication, role and permission checks, public lead and quote intake, password reset flow, PostgreSQL migrations, CMS settings, media storage, navigation, administrator accounts, and audit logs.

Roles are seeded as super-admin, content-editor, and sales-manager. Policies protect the main content resources, while the admin content controller applies resource permissions for the remaining content types. CORS, stateful domains, secure session cookie settings, CSRF middleware, and login rate limiting are environment-configured.

Lead intake is handled by Laravel Form Requests and a database transaction. Contact leads and quote requests use a shared deduplication key, store a consent timestamp and hashed source IP, and dispatch queued notifications after persistence. Quote requests retain a foreign-key reference to their lead and keep uploaded files on a private disk. Lead administration supports status transitions, assignment, internal notes, follow-up dates, filtered pagination, CSV export, audit metadata, archiving, and retention-based purge.

## Phase 6 SEO and analytics

The root layout emits organization JSON-LD and consent-gated GA4 scripts. Public route files use shared metadata helpers with page-specific titles, descriptions, canonical URLs, Open Graph/Twitter data, and indexability controls. Dynamic service, portfolio, and article routes retain typed fixture fallback while their metadata uses the current route content. The Laravel GET /api/content/settings/seo endpoint exposes only the public SEO settings needed by the frontend and applies a short cache policy.

/sitemap.xml covers public fixed routes and current fixture detail routes. /robots.txt allows public routes and disallows /admin and /api. Search Console verification and submission are intentionally documented deployment tasks.

## Current boundaries

Included: complete public route set, reusable content-driven page templates, public published-content API integration with labeled fixture fallback, service details, pricing, portfolio and case-study layouts, blog and article layouts, FAQ, contact and quote intake, legal placeholders, a thank-you route, custom not-found page, responsive navigation, session-based administrator authentication, role permissions, protected content CRUD, PostgreSQL migrations, the responsive admin control center, content publishing, media uploads, site settings, navigation management, user management, audit logs, validated lead intake, linked quote requests, queued notifications, lead assignment, notes, follow-ups, workflow filters, CSV exports, and retention workflows.

Deferred: verified client content, live Search Console verification, measured production Core Web Vitals, finalized legal copy, production mail and storage provider credentials, and deployment configuration.

