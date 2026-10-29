# Production deployment and operations

Phase 7 prepares the application for a separately deployed Next.js frontend, Laravel API, managed PostgreSQL database, S3-compatible media storage, and transactional email provider. No deployment target or credentials are configured in this repository, so release remains a deliberate manual operation.

## Required production values

Frontend:

    NEXT_PUBLIC_API_URL=https://api.example.com/api
    NEXT_PUBLIC_SITE_URL=https://www.example.com
    NEXT_PUBLIC_GA_MEASUREMENT_ID=G-XXXXXXXXXX
    NEXT_PUBLIC_ORGANIZATION_NAME=Verified agency name
    NEXT_PUBLIC_ORGANIZATION_LOGO=https://www.example.com/logo.png
    NEXT_PUBLIC_ORGANIZATION_SAME_AS=https://...

Laravel:

    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://api.example.com
    FRONTEND_URL=https://www.example.com
    CORS_ALLOWED_ORIGINS=https://www.example.com
    SANCTUM_STATEFUL_DOMAINS=www.example.com
    SESSION_DOMAIN=.example.com
    SESSION_SECURE_COOKIE=true
    DB_CONNECTION=pgsql
    DB_HOST=managed-postgres-host
    DB_PORT=5432
    DB_DATABASE=northstar_production
    DB_USERNAME=managed-user
    DB_PASSWORD=secret-manager-value
    FILESYSTEM_DISK=s3
    AWS_ACCESS_KEY_ID=secret-manager-value
    AWS_SECRET_ACCESS_KEY=secret-manager-value
    AWS_DEFAULT_REGION=region
    AWS_BUCKET=private-or-public-media-bucket
    QUEUE_CONNECTION=database
    MAIL_MAILER=postmark
    MAIL_FROM_ADDRESS=verified-sender@example.com
    MAIL_FROM_NAME=Northstar Digital
    AGENCY_NOTIFICATION_EMAIL=verified-inbox@example.com

Use a secret manager or host-managed environment variables. Do not put production values in workflow files, .env.example, build logs, or the repository. Generate a unique APP_KEY with Laravel and keep it stable for the lifetime of the deployment.

## Deployment sequence

1. Provision the database, object storage bucket, mail provider, API host, and frontend host.
2. Configure DNS and HTTPS certificates. Use www.example.com and api.example.com consistently.
3. Configure the Laravel variables above, run php artisan config:cache, and confirm APP_DEBUG=false.
4. Deploy the API code and run php artisan migrate --force. Review the migration list before applying it to a shared database.
5. Run php artisan storage:link only when using the local public disk. S3 deployments should use the configured disk and bucket policy.
6. Seed only the intended administrator and role data. Never use development passwords or seed fixtures in production.
7. Start a persistent queue worker with php artisan queue:work --sleep=3 --tries=3 --timeout=90, supervised by the platform or a process manager.
8. Deploy the frontend with the public API and site URLs. Do not enable GA4 until consent, the property, and the agency identity have been reviewed.
9. Verify the health endpoints, login/logout, content visibility, lead submission, queued email, media upload, and sitemap.
10. Record the release version and migration batch in the change log.

There are currently no scheduled commands in routes/console.php; add a supervised scheduler only when a scheduled command is introduced. If needed, run php artisan schedule:work continuously or configure the host to invoke php artisan schedule:run every minute.

## DNS, cookies, CORS, and HTTPS

Point the public site to the frontend host and the API hostname to the Laravel host. Terminate TLS at the host or load balancer and redirect HTTP to HTTPS. Set CORS_ALLOWED_ORIGINS to the exact frontend origin, without a wildcard. For a first-party Sanctum SPA, include the frontend host in SANCTUM_STATEFUL_DOMAINS, use a compatible SESSION_DOMAIN, enable secure cookies, and make the frontend request the Sanctum CSRF cookie before login. Confirm that proxies forward HTTPS so Laravel generates secure URLs and cookies.

## Backups and restore drills

Use the managed PostgreSQL provider's point-in-time backups and retain an independent scheduled logical backup. A typical encrypted export is:

    pg_dump --format=custom --no-owner --file=northstar-YYYY-MM-DD.dump "$DATABASE_URL"

Restore into a separate non-production database:

    createdb northstar_restore_check
    pg_restore --clean --if-exists --no-owner --dbname=northstar_restore_check northstar-YYYY-MM-DD.dump
    php artisan migrate:status

Run application smoke tests against the restored database, verify row counts for leads and published content, verify media object references, and record the restore duration and result. Never test restore by overwriting production. Store backup credentials outside the application and test restoration at least quarterly.

## Health, logging, and monitoring

Monitor GET /up and GET /api/health from outside the API host. Monitor the frontend root, /robots.txt, and /sitemap.xml. Alert on 5xx rates, failed queue jobs, queue depth, database storage/connections, mail provider failures, disk/object storage errors, authentication spikes, and backup failures. Forward Laravel logs to the host's central logging service with PII redaction. Do not log complete form payloads, passwords, tokens, or attachment contents.

## Release and rollback

Before release, take a database backup and capture the current application version. Apply migrations forward with php artisan migrate --force; do not use the intentionally non-destructive migration rollbacks against production. Roll back application code through the host's previous release mechanism. If a schema change needs reversal, create a reviewed forward migration after confirming its effect on existing data.

## Launch gates

- Replace all labeled preview content and illustrative claims with verified CMS content.
- Confirm every public and detail route returns the expected status.
- Confirm drafts and archived records remain hidden from public APIs.
- Confirm contact and quote submissions persist exactly once and queue both notifications.
- Confirm private quote attachments are not publicly browseable.
- Confirm administrator roles, audit logs, rate limits, secure cookies, CSRF, and HTTPS.
- Confirm S3, mail, database backups, queue workers, and monitoring.
- Run the frontend and backend CI workflows successfully.
- Complete Search Console verification and submit the sitemap.
- Measure Lighthouse and real-user Core Web Vitals after deployment.
