import { CaseStudyPage } from "@/components/marketing";
import { projects } from "@/lib/content";
import { notFound } from "next/navigation";
import { getPageMetadata } from "@/lib/seo-data";

export const instant = false;
export function generateStaticParams() { return projects.map((project) => ({ slug: project.slug })); }
export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const project = projects.find((item) => item.slug === slug);
  return getPageMetadata("/portfolio/" + slug, project ? project.title + " | Northstar Digital" : "Case Study", project?.summary);
}
export default async function Page({ params }: { params: Promise<{ slug: string }> }) { const { slug } = await params; if (!projects.some((project) => project.slug === slug)) notFound(); return <CaseStudyPage slug={slug} />; }
