"use client";

import { useEffect, useState } from "react";
import { services as previewServices, packages as previewPackages, projects as previewProjects, posts as previewPosts, faqs as previewFaqs, type Package, type Post, type Project, type Service } from "@/lib/content";

const API_URL = (process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000/api").replace(/\/$/, "");

export type ApiState<T> = { data: T; loading: boolean; error: string | null; source: "api" | "preview" };
export class ApiRequestError extends Error { constructor(message: string, public readonly status: number, public readonly fields: Record<string, string[]> = {}) { super(message); this.name = "ApiRequestError"; } }

async function getJson<T>(path: string): Promise<T> {
  const response = await fetch(API_URL + path, { credentials: "include", headers: { Accept: "application/json" }, cache: "no-store" });
  if (!response.ok) throw new Error("Content request failed with " + response.status + ".");
  return response.json() as Promise<T>;
}

function usePublicCollection<Remote, Local>(path: string, fallback: Local[], normalize: (item: Remote) => Local): ApiState<Local[]> {
  const [state, setState] = useState<ApiState<Local[]>>({ data: fallback, loading: true, error: null, source: "preview" });
  useEffect(() => { let active = true; getJson<{ data: Remote[] }>(path).then((payload) => { if (active) setState({ data: payload.data.map(normalize), loading: false, error: null, source: "api" }); }).catch((error: unknown) => { if (active) setState((current) => ({ ...current, loading: false, error: error instanceof Error ? error.message : "Unable to load content.", source: "preview" })); }); return () => { active = false; }; }, [path, normalize]);
  return state;
}

function usePublicItem<Remote, Local>(path: string, fallback: Local, normalize: (item: Remote) => Local): ApiState<Local> {
  const [state, setState] = useState<ApiState<Local>>({ data: fallback, loading: true, error: null, source: "preview" });
  useEffect(() => { let active = true; getJson<{ data: Remote }>(path).then((payload) => { if (active) setState({ data: normalize(payload.data), loading: false, error: null, source: "api" }); }).catch((error: unknown) => { if (active) setState((current) => ({ ...current, loading: false, error: error instanceof Error ? error.message : "Unable to load content.", source: "preview" })); }); return () => { active = false; }; }, [path, normalize]);
  return state;
}

type RemoteService = Partial<Service> & { short_title?: string; long_description?: string; related_service_slugs?: string[] };
type RemotePackage = Partial<Package> & { price_label?: string; features?: string[] };
type RemoteProject = Partial<Project> & { result?: string; result_summary?: string; client_label?: string };
type RemotePost = Partial<Post> & { reading_time?: number; published_at?: string; category?: { name?: string } };

const normalizeService = (remote: RemoteService): Service => { const preview = previewServices.find((item) => item.slug === remote.slug) ?? previewServices[0]; return { ...preview, ...remote, shortTitle: remote.short_title ?? remote.shortTitle ?? preview.shortTitle, longDescription: remote.description ?? preview.longDescription, related: remote.related_service_slugs ?? preview.related, benefits: remote.benefits ?? preview.benefits, deliverables: remote.deliverables ?? preview.deliverables, process: remote.process ?? preview.process, faqs: remote.faqs ?? preview.faqs }; };
const normalizePackage = (remote: RemotePackage): Package => { const preview = previewPackages.find((item) => item.name === remote.name) ?? previewPackages[0]; return { ...preview, ...remote, priceNote: remote.price_label ?? remote.priceNote ?? preview.priceNote, features: remote.features ?? preview.features }; };
const normalizeProject = (remote: RemoteProject): Project => { const preview = previewProjects.find((item) => item.slug === remote.slug) ?? previewProjects[0]; return { ...preview, ...remote, clientLabel: remote.client_label ?? remote.clientLabel ?? preview.clientLabel, result: remote.result ?? remote.result_summary ?? preview.result }; };
const normalizePost = (remote: RemotePost): Post => { const preview = previewPosts.find((item) => item.slug === remote.slug) ?? previewPosts[0]; return { ...preview, ...remote, category: remote.category?.name ?? remote.category ?? preview.category, readingTime: typeof remote.reading_time === "number" ? remote.reading_time + " min read" : remote.readingTime ?? preview.readingTime, date: remote.published_at ?? remote.date ?? preview.date }; };
const normalizeFaq = (remote: Partial<(typeof previewFaqs)[number]>) => ({ ...previewFaqs[0], ...remote });

export function useServices() { return usePublicCollection<RemoteService, Service>("/content/services", previewServices, normalizeService); }
export function useService(slug: string) { const fallback = previewServices.find((item) => item.slug === slug) ?? previewServices[0]; return usePublicItem<RemoteService, Service>("/content/services/" + slug, fallback, normalizeService); }
export function usePackages() { return usePublicCollection<RemotePackage, Package>("/content/packages", previewPackages, normalizePackage); }
export function useProjects() { return usePublicCollection<RemoteProject, Project>("/content/portfolio", previewProjects, normalizeProject); }
export function useProject(slug: string) { const fallback = previewProjects.find((item) => item.slug === slug) ?? previewProjects[0]; return usePublicItem<RemoteProject, Project>("/content/portfolio/" + slug, fallback, normalizeProject); }
export function usePosts() { return usePublicCollection<RemotePost, Post>("/content/blog", previewPosts, normalizePost); }
export function usePost(slug: string) { const fallback = previewPosts.find((item) => item.slug === slug) ?? previewPosts[0]; return usePublicItem<RemotePost, Post>("/content/blog/" + slug, fallback, normalizePost); }
export function useFaqs() { return usePublicCollection<Partial<(typeof previewFaqs)[number]>, (typeof previewFaqs)[number]>("/content/faqs", previewFaqs, normalizeFaq); }

async function submitRequest(url: string, init: RequestInit): Promise<{ message: string }> {
  const response = await fetch(url, init);
  const payload = await response.json().catch(() => ({}));
  if (!response.ok) {
    const fields = Object.fromEntries(Object.entries(payload.errors ?? {}).map(([key, value]) => [key, Array.isArray(value) ? value.map(String) : [String(value)]]));
    throw new ApiRequestError(payload.message ?? "Request failed with " + response.status + ".", response.status, fields);
  }
  return payload as { message: string };
}

export async function submitLead(payload: Record<string, unknown>) { return submitRequest(API_URL + "/leads", { method: "POST", body: JSON.stringify(payload), credentials: "include", headers: { Accept: "application/json", "Content-Type": "application/json" } }); }
export async function submitQuote(formData: FormData) { return submitRequest(API_URL + "/quote-requests", { method: "POST", body: formData, credentials: "include", headers: { Accept: "application/json" } }); }
