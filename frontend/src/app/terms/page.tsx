import { LegalPage } from "@/components/marketing";
import { getPageMetadata } from "@/lib/seo-data";

export async function generateMetadata() { return getPageMetadata("/terms"); }
export const instant = false;
export default function Page() { return <LegalPage type="terms" />; }
