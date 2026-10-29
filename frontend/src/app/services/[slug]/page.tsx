import { ServiceDetailPage } from "@/components/marketing";
import { services } from "@/lib/content";
import { notFound } from "next/navigation";
import { getPageMetadata } from "@/lib/seo-data";

export const instant = false;
export function generateStaticParams() { return services.map((service) => ({ slug: service.slug })); }
export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const service = services.find((item) => item.slug === slug);
  return getPageMetadata("/services/" + slug, service ? service.title + " | Northstar Digital" : "Digital Marketing Service", service?.description);
}
export default async function Page({ params }: { params: Promise<{ slug: string }> }) { const { slug } = await params; if (!services.some((service) => service.slug === slug)) notFound(); return <ServiceDetailPage slug={slug} />; }
