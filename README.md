# Northstar Digital Marketing Agency

Phase 7 public website, Laravel API, administrator control center, lead quotation workflow, SEO/analytics foundation, and deployment runbook for a production-oriented digital marketing agency.

The project is organized as a small monorepo:

- frontend/ — Next.js App Router, React, TypeScript, Tailwind CSS, and Lucide React.
- backend/ — Laravel REST API foundation, configured for PostgreSQL.
- docs/ — architecture and delivery notes.

## Requirements

- Node.js 20+
- npm 10+
- PHP 8.3+
- Composer 2+
- PostgreSQL 15+

## Local development

### Frontend

    cd frontend
    npm install
    npm run dev

Open http://localhost:3000.

Public routes include the home, about, team, services, pricing, portfolio, blog, FAQ, contact, quote, privacy, terms, and thank-you pages. Service, portfolio, and blog detail routes use typed preview fixtures when the Laravel API is unavailable.

Useful checks:

    npm run lint
    npx tsc --noEmit
    npm run build

### Backend

    cd backend
    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan migrate
    php artisan db:seed
    php artisan serve

The API application will be available at http://localhost:8000. Set DB_DATABASE, DB_USERNAME, and DB_PASSWORD in backend/.env for a local PostgreSQL database before running migrations.

For administrator authentication, set ADMIN_EMAIL, ADMIN_PASSWORD, FRONTEND_URL, and SANCTUM_STATEFUL_DOMAINS in backend/.env. Set AGENCY_NOTIFICATION_EMAIL for internal lead alerts and configure the mail transport without committing credentials. Run php artisan queue:work when using the database queue.

### Admin control center

After the backend is running and an administrator has been seeded, open http://localhost:3000/admin/login. The admin UI uses the same Sanctum session flow and protected API. It includes dashboard metrics, inquiry management, content CRUD and publishing, media uploads, site settings, navigation, users, roles, and audit logs.

The lead workflow stores contact and quote submissions in PostgreSQL, links quote requests to leads, validates private attachments, queues internal and customer notifications, supports assignment, notes, follow-ups, workflow statuses, authorized CSV exports, and retention-based deletion of archived records.

For SEO and analytics, copy the documented values in frontend/.env.example to frontend/.env.local. Leave NEXT_PUBLIC_GA_MEASUREMENT_ID empty until the agency has a consent decision and a verified GA4 property. See docs/seo.md for sitemap, Search Console, measurement, and launch instructions.

## Environment files

Environment examples are committed in frontend/.env.example and backend/.env.example. Never commit local .env files or credentials.

## Project conventions

- Strict TypeScript and semantic HTML are required in the frontend.
- Reusable UI belongs in frontend/src/components/.
- Preview content belongs in frontend/src/lib/content.ts and must remain clearly labeled until CMS data is connected.
- frontend/src/lib/api.ts uses published Laravel content when the API is available and falls back to labeled preview fixtures when it is unavailable.
- Global design tokens live in frontend/src/app/globals.css.
- REST routes and domain logic belong in Laravel controllers and services as they are added.
- Database changes must use Laravel migrations and must be reviewed before applying to shared databases.

See docs/api.md for the API route contract, docs/architecture.md for the current architecture, docs/seo.md for SEO and analytics, docs/deployment.md for production deployment, backup, operations, and launch requirements, and docs/production-readiness.md for the current verification results and blockers.

