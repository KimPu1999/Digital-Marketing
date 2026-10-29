import Link from "next/link";
import { ArrowRight, Compass } from "lucide-react";
import { Footer, Header } from "@/components/site";
import type { Metadata } from "next";

export const metadata: Metadata = { title: "Page not found | Northstar Digital", description: "The requested Northstar Digital page could not be found.", robots: { index: false, follow: false } };
export default function NotFound() { return <><Header /><main className="flex min-h-[65vh] items-center justify-center bg-slate-50 px-6 py-20"><div className="max-w-xl text-center"><div className="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-100 text-blue-700"><Compass size={30} /></div><p className="mt-8 text-sm font-bold uppercase tracking-[0.18em] text-blue-700">404</p><h1 className="mt-4 text-5xl font-extrabold tracking-[-0.05em] text-slate-950">This page took a wrong turn.</h1><p className="mt-5 text-lg leading-8 text-slate-600">The link may be outdated, or the page is still finding its place.</p><Link href="/" className="mt-8 inline-flex h-12 items-center gap-2 rounded-lg bg-blue-700 px-5 text-sm font-bold text-white hover:bg-blue-800">Return home <ArrowRight size={17} /></Link></div></main><Footer /></>; }
