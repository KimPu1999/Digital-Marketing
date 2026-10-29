import { PricingPage } from "@/components/marketing";
import { getPageMetadata } from "@/lib/seo-data";

export async function generateMetadata() { return getPageMetadata("/pricing"); }
export const instant = false;
export default function Page() { return <PricingPage />; }
