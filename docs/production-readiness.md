# Production readiness review

Review date: 2026-10-29

## Verified in this workspace

- Next.js lint passes.
- Strict TypeScript check passes.
- Next.js production build passes and generates all declared routes, sitemap, and robots endpoints.
- PHP syntax checks pass for application, routes, configuration, migrations, seeders, and tests.
- Composer manifest validation passes.
- Public route smoke checks return expected 200 responses for fixed routes, service detail, portfolio detail, blog detail, admin entry points, sitemap, and robots.
- Unknown public routes return the custom 404 response.
- Next security headers are present in the local production response.
- Public preview content remains explicitly labeled as illustrative.
- SVG media uploads are rejected; quote attachments use the private local disk by default.

## Blocked or pending

- Laravel feature tests cannot run locally until Composer dependencies are installed. The backend vendor directory is absent.
- PostgreSQL is not running locally, so migrations, relationships, authentication, lead workflow, queue behavior, and database-backed tests are not locally verified.
- Composer lockfile is not committed. Generate and review composer.lock in a networked environment before the first production release.
- npm audit could not reach the npm registry in this environment. Run it in CI or a networked release environment.
- Browser-based keyboard, mobile layout, Lighthouse, and axe audits were not available in this environment.
- No deployment target, DNS, HTTPS certificate, S3 bucket, transactional mail provider, backup schedule, or monitoring destination is configured.

## Launch blockers

Replace preview fixtures and illustrative partner claims with verified CMS content. Configure real organization identity, mail, S3, PostgreSQL, queue workers, secure cookies, CORS, backups, monitoring, and Search Console. Run the backend CI workflow against PostgreSQL, complete a restore drill, and manually test contact and quote email delivery before launch.

See docs/deployment.md for the deployment sequence, required environment values, backup and restore procedure, queue and scheduler operations, health checks, and rollback guidance.
