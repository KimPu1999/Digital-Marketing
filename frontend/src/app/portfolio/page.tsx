import { PortfolioPage } from "@/components/marketing";
import { getPageMetadata } from "@/lib/seo-data";

export async function generateMetadata() { return getPageMetadata("/portfolio"); }
export const instant = false;
export default function Page() { return <PortfolioPage />; }
