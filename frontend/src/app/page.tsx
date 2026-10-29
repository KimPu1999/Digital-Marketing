import { HomePage } from "@/components/marketing";
import { getPageMetadata } from "@/lib/seo-data";

export async function generateMetadata() { return getPageMetadata("/"); }
export const instant = false;
export default function Page() {
  return <HomePage />;
}
