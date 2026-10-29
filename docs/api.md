# Phase 5 API

The Laravel API is rooted at /api. Public content is read-only and returns only published records. Approved testimonials additionally require is_approved=true. Admin content routes require a Sanctum-authenticated session.

## Public routes

| Method | Route | Purpose |
| --- | --- | --- |
| GET | /api/health | Service health check |
| GET | /api/content/services | Published services with search, sorting, and pagination |
| GET | /api/content/services/{slug} | Published service detail |
| GET | /api/content/service-categories | Published service categories |
| GET | /api/content/packages | Published packages and features |
| GET | /api/content/portfolio | Published portfolio projects |
| GET | /api/content/portfolio/{slug} | Published portfolio project detail |
| GET | /api/content/case-studies | Published case studies |
| GET | /api/content/case-studies/{slug} | Published case study by project slug |
| GET | /api/content/blog-categories | Published blog categories |
| GET | /api/content/blog | Published blog posts |
| GET | /api/content/blog/{slug} | Published blog post detail |
| GET | /api/content/faqs | Published FAQs |
| GET | /api/content/testimonials | Approved, published testimonials |
| POST | /api/leads | Public contact intake with validation, consent, spam honeypot, throttling, deduplication, and queued notifications |
| POST | /api/quote-requests | Public quote intake with private attachment storage, linked lead persistence, throttling, deduplication, and queued notifications |

Collection endpoints accept search, sort, direction, and per_page. per_page is capped at 100. Responses use Laravel JSON Resources and paginator metadata.

## Authentication routes

The first-party admin SPA uses the Sanctum session flow:

1. GET /sanctum/csrf-cookie
2. POST /api/auth/login with email, password, and optional remember
3. GET /api/auth/me
4. POST /api/auth/logout

Password recovery uses POST /api/auth/forgot-password and POST /api/auth/reset-password. There is no public registration route.

## Admin content routes

All routes require auth:sanctum and accept one of service-categories, services, packages, portfolio-projects, case-studies, blog-categories, blog-posts, testimonials, or faqs as the resource name:

    GET    /api/admin/content/{resource}
    POST   /api/admin/content/{resource}
    GET    /api/admin/content/{resource}/{content}
    PUT    /api/admin/content/{resource}/{content}
    PATCH  /api/admin/content/{resource}/{content}
    DELETE /api/admin/content/{resource}/{content}

Admin listing accepts status, search, sort, direction, and per_page. Content deletion uses soft deletes and the response describes the record as archived.

## Admin control center routes

| Method | Route | Purpose |
| --- | --- | --- |
| GET | /api/admin/dashboard | Database-backed dashboard counts, workflow summaries, and recent inquiries |
| GET | /api/admin/lead-staff | Authorized staff list for lead assignment |
| GET/PATCH | /api/admin/leads, /api/admin/leads/{id} | Lead search, pagination, service/status/follow-up filters, assignment, status updates, and notes |
| POST | /api/admin/leads/{id}/archive | Archive a lead while preserving the record for retention |
| GET | /api/admin/leads/export | Authorized CSV export using the same filters |
| GET/PATCH | /api/admin/quote-requests, /api/admin/quote-requests/{id} | Quote request review, assignment, follow-up, filtering, and status updates |
| POST | /api/admin/quote-requests/{id}/archive | Archive a quote request |
| POST | /api/admin/leads/retention/purge | Permanently delete archived records older than the selected retention window |
| POST | /api/admin/content/{resource}/{id}/publish | Publish a supported content record |
| POST | /api/admin/content/{resource}/{id}/unpublish | Return a record to draft |
| GET/PUT | /api/admin/settings/{key} | Appearance, hero, homepage sections, footer, SEO, contact, and general settings |
| GET/POST/PATCH/DELETE | /api/admin/media | Validated image upload, metadata, and safe deletion |
| GET/POST/PUT/DELETE | /api/admin/navigation | Header and footer navigation management |
| GET/POST/PUT/DELETE | /api/admin/users | Administrator account management |
| GET | /api/admin/roles | Role and permission matrix |
| GET | /api/admin/audit-logs | Recorded administrator actions |

Settings are stored as JSON in PostgreSQL site_settings. Homepage sections are validated against a fixed registry. Footer social links use a supported icon registry. Media assets use the configured Laravel filesystem disk and cannot be deleted while referenced by site settings.

## Lead workflow

Lead statuses are new, contacted, qualified, proposal_sent, won, lost, spam, and archived. Existing legacy values remain readable during migration. Lead and quote detail endpoints are restricted by leads.read; mutations, notes, assignment, archiving, retention deletion, and exports use leads.manage where appropriate. Audit metadata records action and changed field names without recording full submitted payloads.

Contact and quote submissions require privacy consent. The optional attachment is validated by extension and size and stored on the private local disk. Set AGENCY_NOTIFICATION_EMAIL for the internal recipient. Customer and internal notifications implement Laravel's queued notification contract; run a worker locally with php artisan queue:work when QUEUE_CONNECTION=database.

## Local authentication and queue setup

    cd backend
    cp .env.example .env
    php artisan key:generate
    php artisan migrate
    php artisan db:seed
    php artisan queue:work

Set a local ADMIN_PASSWORD in .env. RolePermissionSeeder creates Super Admin, Content Editor, and Sales Manager roles. AdminUserSeeder creates the account from ADMIN_EMAIL and ADMIN_PASSWORD only when both variables are present. The password is hashed before storage.

