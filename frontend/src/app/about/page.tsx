import { AboutPage } from "@/components/marketing";
import { getPageMetadata } from "@/lib/seo-data";

export async function generateMetadata() { return getPageMetadata("/about"); }
export const instant = false;
export default function Page() { return <AboutPage />; }
