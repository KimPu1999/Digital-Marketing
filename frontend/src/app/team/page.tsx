import { TeamPage } from "@/components/marketing";
import { getPageMetadata } from "@/lib/seo-data";

export async function generateMetadata() { return getPageMetadata("/team"); }
export const instant = false;
export default function Page() { return <TeamPage />; }
