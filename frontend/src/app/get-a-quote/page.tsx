import { QuotePage } from "@/components/marketing";
import { getPageMetadata } from "@/lib/seo-data";

export async function generateMetadata() { return getPageMetadata("/get-a-quote"); }
export const instant = false;
export default function Page() { return <QuotePage />; }
