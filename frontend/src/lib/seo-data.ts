import type { Metadata } from "next";
import { defaultSeoSettings, metadataForPath, type SeoSettings } from "@/lib/seo";

const apiUrl = (process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000/api").replace(/\/$/, "");

export async function getSeoSettings(): Promise<SeoSettings> {
  "use cache";
  try {
    const response = await fetch(apiUrl + "/content/settings/seo", { next: { revalidate: 300 }, headers: { Accept: "application/json" } });
    if (!response.ok) return defaultSeoSettings;
    const payload = await response.json() as { data?: SeoSettings };
    return { ...defaultSeoSettings, ...(payload.data ?? {}) };
  } catch {
    return defaultSeoSettings;
  }
}

export async function getPageMetadata(path: string, fallbackTitle?: string, fallbackDescription?: string): Promise<Metadata> {
  return metadataForPath(path, fallbackTitle, fallbackDescription, await getSeoSettings());
}
