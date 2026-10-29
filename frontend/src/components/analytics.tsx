"use client";

import Script from "next/script";
import { useEffect, useState } from "react";
import { usePathname } from "next/navigation";

const configuredMeasurementId = process.env.NEXT_PUBLIC_GA_MEASUREMENT_ID;
const measurementId = configuredMeasurementId && /^G-[A-Z0-9]+$/i.test(configuredMeasurementId) ? configuredMeasurementId : undefined;
const consentKey = "northstar.analytics.consent";
const sentEvents = new Set<string>();

declare global {
  interface Window {
    dataLayer: unknown[];
    gtag?: (...args: unknown[]) => void;
  }
}

export type AnalyticsEvent = "page_view" | "cta_click" | "contact_form_submitted" | "quote_request_submitted" | "service_inquiry";

export function trackEvent(name: AnalyticsEvent, parameters: Record<string, string> = {}): void {
  if (!measurementId || typeof window === "undefined" || window.localStorage.getItem(consentKey) !== "accepted") return;
  const eventId = parameters.event_id;
  if (eventId && sentEvents.has(eventId)) return;
  if (eventId) sentEvents.add(eventId);
  const safeParameters = Object.fromEntries(Object.entries(parameters).filter(([key]) => ["event_id", "service", "destination", "placement"].includes(key)).map(([key, value]) => [key, String(value).slice(0, 100)]));
  window.gtag?.("event", name, safeParameters);
}

export function Analytics() {
  const pathname = usePathname();
  const [consent, setConsent] = useState<string | null>(() => typeof window === "undefined" ? null : window.localStorage.getItem(consentKey));

  useEffect(() => {
    if (consent !== "accepted" || !measurementId) return;
    const query = typeof window === "undefined" ? "" : window.location.search;
    const page = pathname + query;
    trackEvent("page_view", { event_id: "page-view:" + page, destination: page, placement: "page_view" });
  }, [consent, pathname]);

  if (!measurementId || consent === "declined") return null;
  const grant = () => { window.localStorage.setItem(consentKey, "accepted"); setConsent("accepted"); };
  const decline = () => { window.localStorage.setItem(consentKey, "declined"); setConsent("declined"); };

  return (
    <>
      {consent === "accepted" && (
        <>
          <Script id="ga4-loader" strategy="afterInteractive" dangerouslySetInnerHTML={{ __html: "window.dataLayer=window.dataLayer||[];window.gtag=function(){window.dataLayer.push(arguments);};window.gtag('js',new Date());window.gtag('config','" + measurementId + "',{send_page_view:false});" }} />
          <Script src={"https://www.googletagmanager.com/gtag/js?id=" + encodeURIComponent(measurementId)} strategy="afterInteractive" />
        </>
      )}
      {consent === null && (
        <aside className="fixed inset-x-4 bottom-4 z-50 mx-auto flex max-w-3xl flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-xl sm:flex-row sm:items-center sm:justify-between" aria-label="Analytics consent">
          <p className="text-sm leading-6 text-slate-600">Allow anonymous analytics to help us understand page performance and improve this website.</p>
          <div className="flex shrink-0 gap-2">
            <button type="button" onClick={decline} className="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Decline</button>
            <button type="button" onClick={grant} className="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Allow analytics</button>
          </div>
        </aside>
      )}
    </>
  );
}
