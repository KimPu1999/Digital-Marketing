import type { Metadata } from "next";
import type { ReactNode } from "react";

export const siteUrl = (process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3000").replace(/\/$/, "");
export const organizationName = process.env.NEXT_PUBLIC_ORGANIZATION_NAME ?? "Northstar Digital";

export type SeoPageOverride = {
  path: string;
  title?: string | null;
  description?: string | null;
  canonical_url?: string | null;
  search_engine_visibility?: "index" | "noindex" | null;
};

export type SeoSettings = {
  title: string;
  description: string;
  homepage_title?: string;
  homepage_description?: string;
  canonical_url?: string | null;
  og_image_url?: string | null;
  search_engine_visibility?: "index" | "noindex";
  pages?: SeoPageOverride[];
};

export const defaultSeoSettings: SeoSettings = {
  title: "Northstar Digital — Marketing that moves your business forward",
  description: "A strategy-led digital marketing partner for ambitious brands.",
  homepage_title: "Northstar Digital — Marketing that moves your business forward",
  homepage_description: "A strategy-led digital marketing partner for ambitious brands.",
  canonical_url: null,
  og_image_url: null,
  search_engine_visibility: "index",
  pages: [],
};

const pageDefaults: Record<string, { title: string; description: string }> = {
  "/": { title: defaultSeoSettings.title, description: defaultSeoSettings.description },
  "/about": { title: "About Northstar Digital", description: "Meet the strategy-led digital marketing agency helping ambitious brands move forward." },
  "/team": { title: "Our Team | Northstar Digital", description: "Meet the specialists behind Northstar Digital's strategy, creative, and growth work." },
  "/services": { title: "Digital Marketing Services | Northstar Digital", description: "Explore SEO, social media, paid advertising, branding, web development, content, email, and analytics services." },
  "/pricing": { title: "Pricing & Packages | Northstar Digital", description: "Review transparent digital marketing packages with service fees clearly separated from advertising budgets." },
  "/portfolio": { title: "Portfolio & Case Studies | Northstar Digital", description: "Explore selected digital marketing projects, strategies, and preview results from Northstar Digital." },
  "/blog": { title: "Digital Marketing Insights | Northstar Digital", description: "Practical insights on strategy, SEO, content, paid media, and sustainable digital growth." },
  "/faq": { title: "Frequently Asked Questions | Northstar Digital", description: "Find answers about Northstar Digital services, process, packages, and working together." },
  "/contact": { title: "Contact Northstar Digital", description: "Start a conversation with Northstar Digital about your marketing goals and next steps." },
  "/get-a-quote": { title: "Get a Digital Marketing Quote | Northstar Digital", description: "Tell us about your goals, services, budget, and timeline so we can prepare the right next step." },
  "/privacy-policy": { title: "Privacy Policy | Northstar Digital", description: "Read how Northstar Digital handles personal information and website submissions." },
  "/terms": { title: "Terms & Conditions | Northstar Digital", description: "Review the terms that apply to using the Northstar Digital website and services." },
  "/thank-you": { title: "Thank You | Northstar Digital", description: "Your request has been received by Northstar Digital." },
};

export function safeUrl(value: string | null | undefined, fallback: string): string {
  if (!value) return fallback;
  try {
    const url = new URL(value, siteUrl);
    if (url.protocol !== "http:" && url.protocol !== "https:") return fallback;
    return url.toString().replace(/\/$/, "") || siteUrl;
  } catch {
    return fallback;
  }
}

export function canonicalForPath(path: string, override?: string | null): string {
  return safeUrl(override, siteUrl + (path === "/" ? "" : path));
}

export function metadataForPath(path: string, fallbackTitle?: string, fallbackDescription?: string, settings: SeoSettings = defaultSeoSettings): Metadata {
  const page = settings.pages?.find((item) => item.path === path);
  const defaults = pageDefaults[path] ?? { title: fallbackTitle ?? settings.title, description: fallbackDescription ?? settings.description };
  const title = page?.title || (path === "/" ? settings.homepage_title || settings.title : defaults.title);
  const description = page?.description || (path === "/" ? settings.homepage_description || settings.description : defaults.description);
  const canonical = canonicalForPath(path, page?.canonical_url || (path === "/" ? settings.canonical_url : null));
  const visibility = page?.search_engine_visibility || settings.search_engine_visibility || "index";
  const image = settings.og_image_url ? safeUrl(settings.og_image_url, "") : undefined;
  return {
    metadataBase: new URL(siteUrl),
    title: { absolute: title },
    description,
    alternates: { canonical },
    openGraph: { type: "website", url: canonical, siteName: organizationName, title, description, images: image ? [{ url: image, alt: organizationName + " social sharing image" }] : undefined },
    twitter: { card: image ? "summary_large_image" : "summary", title, description, images: image ? [image] : undefined },
    robots: visibility === "noindex" ? { index: false, follow: false } : { index: true, follow: true },
  };
}

export function jsonLd(data: Record<string, unknown>): string {
  return JSON.stringify(data).replace(/</g, "\\u003c");
}

export function JsonLd({ data }: { data: Record<string, unknown> }): ReactNode {
  return <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: jsonLd(data) }} />;
}

export function organizationJsonLd(): Record<string, unknown> {
  const logo = process.env.NEXT_PUBLIC_ORGANIZATION_LOGO;
  const sameAs = (process.env.NEXT_PUBLIC_ORGANIZATION_SAME_AS ?? "").split(",").map((value) => value.trim()).filter(Boolean).map((value) => safeUrl(value, "")).filter(Boolean);
  return { "@context": "https://schema.org", "@type": "Organization", name: organizationName, url: siteUrl, ...(logo ? { logo: safeUrl(logo, "") } : {}), ...(sameAs.length ? { sameAs } : {}) };
}

export function breadcrumbJsonLd(items: Array<{ name: string; path: string }>): Record<string, unknown> {
  return { "@context": "https://schema.org", "@type": "BreadcrumbList", itemListElement: items.map((item, index) => ({ "@type": "ListItem", position: index + 1, name: item.name, item: canonicalForPath(item.path) })) };
}

export function articleJsonLd(article: { headline: string; description: string; path: string; datePublished?: string; image?: string }): Record<string, unknown> {
  return { "@context": "https://schema.org", "@type": "Article", headline: article.headline, description: article.description, mainEntityOfPage: canonicalForPath(article.path), author: { "@type": "Organization", name: organizationName, url: siteUrl }, publisher: organizationJsonLd(), ...(article.datePublished ? { datePublished: article.datePublished } : {}), ...(article.image ? { image: safeUrl(article.image, "") } : {}) };
}

export function serviceJsonLd(service: { name: string; description: string; path: string }): Record<string, unknown> {
  return { "@context": "https://schema.org", "@type": "Service", name: service.name, description: service.description, url: canonicalForPath(service.path), provider: organizationJsonLd() };
}

