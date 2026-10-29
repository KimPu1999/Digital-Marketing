import { ServicesPage } from "@/components/marketing";
import { getPageMetadata } from "@/lib/seo-data";

export async function generateMetadata() { return getPageMetadata("/services"); }
export const instant = false;
export default function Page() { return <ServicesPage />; }
