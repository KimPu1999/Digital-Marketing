# SEO, analytics, and launch checks

Phase 6 adds route metadata, canonical links, Open Graph and Twitter card data, organization, service, article, and breadcrumb JSON-LD, plus /sitemap.xml and /robots.txt.

## Configuration

Set these values in frontend/.env.local:

    NEXT_PUBLIC_SITE_URL=https://www.example.com
    NEXT_PUBLIC_GA_MEASUREMENT_ID=G-XXXXXXXXXX
    NEXT_PUBLIC_ORGANIZATION_NAME=Verified agency name
    NEXT_PUBLIC_ORGANIZATION_LOGO=https://www.example.com/logo.png
    NEXT_PUBLIC_ORGANIZATION_SAME_AS=https://www.linkedin.com/company/example,https://www.instagram.com/example

The organization name, logo, and social URLs must be verified before production use. Empty optional values are omitted from structured data. GA4 is disabled when the measurement ID is empty.

Visitors see an analytics consent prompt when GA4 is configured. Events contain only route, service slug, placement, and generated event IDs. Form names, email addresses, phone numbers, company names, messages, and attachment names are never sent to analytics.

## Admin SEO settings

Open Admin Control Center -> SEO Settings. The protected API persists the default title, description, homepage overrides, canonical override, social image media ID, visibility, and page-specific overrides in the site_settings table. URL validation accepts only HTTP and HTTPS URLs and blocks /admin and /api page paths.

## Search Console

1. Verify the production domain in Google Search Console using a DNS TXT record or the provider's approved HTML tag/file method.
2. Confirm https://www.example.com/robots.txt is reachable.
3. Submit https://www.example.com/sitemap.xml in Search Console.
4. Inspect representative home, service, portfolio, and article URLs and request indexing after verified content is published.
5. Review coverage, canonical selection, structured-data reports, Core Web Vitals, and mobile usability.

Search Console is not connected by this codebase. Verification and submission remain deployment tasks.

## Performance and accessibility

The build uses system-first font fallbacks, no public image element without an explicit alternative, compressed Next responses, cacheable Laravel public settings, route revalidation for metadata, and security headers. The target Core Web Vitals are LCP <= 2.5s, INP <= 200ms, and CLS <= 0.1.

Measure those values after deployment with Lighthouse, PageSpeed Insights, and real-user data. The local environment has no browser or Lighthouse runner configured, so these targets are not claimed as achieved. Keyboard navigation, visible focus rings, form labels, live form errors, responsive navigation state, and FAQ disclosure semantics are implemented in the shared components.

Before launch, replace labeled fixture content, confirm real agency identity data, add production image assets with meaningful alt text, review legal copy, configure consent requirements for the target jurisdictions, and run a browser-based accessibility/performance audit.
