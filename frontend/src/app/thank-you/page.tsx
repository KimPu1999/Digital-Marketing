import { ThankYouPage } from "@/components/marketing";
import { getPageMetadata } from "@/lib/seo-data";

export async function generateMetadata() { return getPageMetadata("/thank-you"); }
export const instant = false;
export default function Page() { return <ThankYouPage />; }
