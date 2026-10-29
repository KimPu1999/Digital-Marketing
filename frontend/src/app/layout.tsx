import type { Metadata } from "next";
import "./globals.css";
import { Analytics } from "@/components/analytics";
import { JsonLd, organizationJsonLd, metadataForPath } from "@/lib/seo";

export const metadata: Metadata = metadataForPath("/");

export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return <html lang="en"><body>{children}<JsonLd data={organizationJsonLd()} /><Analytics /></body></html>;
}
