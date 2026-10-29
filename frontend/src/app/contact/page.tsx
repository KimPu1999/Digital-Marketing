import { ContactPage } from "@/components/marketing";
import { getPageMetadata } from "@/lib/seo-data";

export async function generateMetadata() { return getPageMetadata("/contact"); }
export const instant = false;
export default function Page() { return <ContactPage />; }
