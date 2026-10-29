import { BlogPostPage } from "@/components/marketing";
import { posts } from "@/lib/content";
import { notFound } from "next/navigation";
import { getPageMetadata } from "@/lib/seo-data";

export const instant = false;
export function generateStaticParams() { return posts.map((post) => ({ slug: post.slug })); }
export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const post = posts.find((item) => item.slug === slug);
  return getPageMetadata("/blog/" + slug, post ? post.title + " | Northstar Digital" : "Digital Marketing Insight", post?.excerpt);
}
export default async function Page({ params }: { params: Promise<{ slug: string }> }) { const { slug } = await params; if (!posts.some((post) => post.slug === slug)) notFound(); return <BlogPostPage slug={slug} />; }
