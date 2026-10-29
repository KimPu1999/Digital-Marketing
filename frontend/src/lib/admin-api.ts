"use client";

import { useCallback, useEffect, useState } from "react";

const API_URL = (process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000/api").replace(/\/$/, "");
const API_ORIGIN = API_URL.replace(/\/api$/, "");

export type ApiPagination<T> = { data: T[]; meta?: { current_page: number; last_page: number; total: number; per_page: number } };
export type AdminUser = { id: number; name: string; email: string; roles?: string[] };
export type AdminState<T> = { data: T; loading: boolean; error: string | null };

async function apiRequest<T>(path: string, init: RequestInit = {}): Promise<T> {
  const response = await fetch(`${API_URL}${path}`, { ...init, credentials: "include", headers: { Accept: "application/json", ...(init.body instanceof FormData ? {} : { "Content-Type": "application/json" }), ...(init.headers ?? {}) } });
  const payload = await response.json().catch(() => ({}));
  if (!response.ok) throw new Error(payload.message ?? payload.error ?? `Request failed with ${response.status}.`);
  return payload as T;
}

export async function csrfCookie() { await fetch(`${API_ORIGIN}/sanctum/csrf-cookie`, { credentials: "include" }); }
export async function adminLogin(email: string, password: string, remember = false) { await csrfCookie(); return apiRequest<{ data: AdminUser }>("/auth/login", { method: "POST", body: JSON.stringify({ email, password, remember }) }); }
export async function adminLogout() { return apiRequest<{ message: string }>("/auth/logout", { method: "POST" }); }
export async function adminMe() { return apiRequest<{ data: AdminUser }>("/auth/me"); }

export function useAdminRequest<T>(path: string, fallback: T): AdminState<T> & { reload: () => void } {
  const [state, setState] = useState<AdminState<T>>({ data: fallback, loading: true, error: null });
  const [revision, setRevision] = useState(0);
  const reload = useCallback(() => setRevision((value) => value + 1), []);
  useEffect(() => { let active = true; apiRequest<T>(path).then((payload) => { if (active) setState({ data: payload, loading: false, error: null }); }).catch((error: unknown) => { if (active) setState((current) => ({ ...current, loading: false, error: error instanceof Error ? error.message : "Unable to load admin data." })); }); return () => { active = false; }; }, [path, revision]);
  return { ...state, reload };
}

export function useCurrentAdmin(enabled = true): AdminState<AdminUser | null> & { reload: () => void } {
  const state = useAdminRequest<{ data: AdminUser | null }>(enabled ? '/auth/me' : '/health', { data: null });
  return { data: enabled ? state.data.data : null, loading: enabled ? state.loading : false, error: enabled ? state.error : null, reload: state.reload };
}

export async function adminGet<T>(path: string) { return apiRequest<T>(path); }
export async function adminMutate<T>(path: string, method: string, body?: unknown) { return apiRequest<T>(path, { method, body: body instanceof FormData ? body : body === undefined ? undefined : JSON.stringify(body) }); }
export async function adminDownload(path: string) { const response = await fetch(API_URL + path, { credentials: "include", headers: { Accept: "text/csv" } }); if (!response.ok) throw new Error("Download failed with " + response.status + "."); return response.blob(); }

export const adminApi = { get: adminGet, mutate: adminMutate };
