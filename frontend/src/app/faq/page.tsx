import { FAQPage } from "@/components/marketing";
import { getPageMetadata } from "@/lib/seo-data";

export async function generateMetadata() { return getPageMetadata("/faq"); }
export const instant = false;
export default function Page() { return <FAQPage />; }
