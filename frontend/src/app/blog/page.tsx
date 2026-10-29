import { BlogPage } from "@/components/marketing";
import { getPageMetadata } from "@/lib/seo-data";

export async function generateMetadata() { return getPageMetadata("/blog"); }
export const instant = false;
export default function Page() { return <BlogPage />; }
