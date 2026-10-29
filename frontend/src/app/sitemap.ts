import type { MetadataRoute } from "next";
import { posts, projects, services } from "@/lib/content";
import { siteUrl } from "@/lib/seo";

const fixedRoutes = ["/", "/about", "/team", "/services", "/pricing", "/portfolio", "/blog", "/faq", "/contact", "/get-a-quote", "/privacy-policy", "/terms"];

export default function sitemap(): MetadataRoute.Sitemap {
  const base = fixedRoutes.map((path) => ({ url: siteUrl + (path === "/" ? "" : path), changeFrequency: path === "/" ? "weekly" as const : "monthly" as const, priority: path === "/" ? 1 : 0.7 }));
  const serviceRoutes = services.map((item) => ({ url: siteUrl + "/services/" + item.slug, changeFrequency: "monthly" as const, priority: 0.7 }));
  const projectRoutes = projects.map((item) => ({ url: siteUrl + "/portfolio/" + item.slug, changeFrequency: "monthly" as const, priority: 0.6 }));
  const postRoutes = posts.map((item) => ({ url: siteUrl + "/blog/" + item.slug, changeFrequency: "monthly" as const, priority: 0.6 }));
  return [...base, ...serviceRoutes, ...projectRoutes, ...postRoutes];
}
