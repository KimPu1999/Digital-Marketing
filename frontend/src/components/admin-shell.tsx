"use client";

import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { Bell, ChevronRight, LogOut, Menu, PanelLeftClose, PanelLeftOpen, ShieldCheck, X } from "lucide-react";
import { useEffect, useState } from "react";
import { adminLogout, useCurrentAdmin } from "@/lib/admin-api";

type NavigationItem = { label: string; href: string; icon: string; group: string };

const navigation: NavigationItem[] = [
  { label: "Overview", href: "/admin", icon: "⌂", group: "Workspace" },
  { label: "Leads & inquiries", href: "/admin/leads", icon: "↗", group: "Workspace" },
  { label: "Services", href: "/admin/services", icon: "✦", group: "Content" },
  { label: "Pricing & packages", href: "/admin/packages", icon: "▣", group: "Content" },
  { label: "Portfolio & case studies", href: "/admin/portfolio", icon: "◫", group: "Content" },
  { label: "Blog management", href: "/admin/blog", icon: "✎", group: "Content" },
  { label: "Blog categories", href: "/admin/blog-categories", icon: "#", group: "Content" },
  { label: "Testimonials", href: "/admin/testimonials", icon: "“", group: "Content" },
  { label: "FAQ management", href: "/admin/faqs", icon: "?", group: "Content" },
  { label: "Media library", href: "/admin/media", icon: "▧", group: "Content" },
  { label: "Appearance & branding", href: "/admin/appearance", icon: "◈", group: "Site" },
  { label: "Header & navigation", href: "/admin/navigation", icon: "☰", group: "Site" },
  { label: "Homepage sections", href: "/admin/homepage-sections", icon: "⌘", group: "Site" },
  { label: "Hero banner", href: "/admin/hero", icon: "▤", group: "Site" },
  { label: "Footer & social links", href: "/admin/footer", icon: "↘", group: "Site" },
  { label: "SEO settings", href: "/admin/seo", icon: "⌕", group: "Site" },
  { label: "Contact information", href: "/admin/contact", icon: "@", group: "Site" },
  { label: "User management", href: "/admin/users", icon: "♙", group: "Administration" },
  { label: "Roles & permissions", href: "/admin/roles", icon: "◇", group: "Administration" },
  { label: "Audit logs", href: "/admin/audit-logs", icon: "≡", group: "Administration" },
  { label: "General settings", href: "/admin/general", icon: "⚙", group: "Administration" },
];

export function AdminShell({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const router = useRouter();
  const login = pathname === "/admin/login";
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [collapsed, setCollapsed] = useState(false);
  const admin = useCurrentAdmin(!login);

  useEffect(() => {
    if (!login && !admin.loading && admin.error) router.replace("/admin/login");
  }, [admin.error, admin.loading, login, router]);

  if (login) return <>{children}</>;
  if (admin.loading) return <div className="flex min-h-screen items-center justify-center bg-slate-50 text-sm font-semibold text-slate-500">Loading control center…</div>;
  if (admin.error || !admin.data) return null;

  const active = navigation.find((item) => item.href === pathname) ?? navigation.find((item) => pathname.startsWith(`${item.href}/`));
  const groups = Array.from(new Set(navigation.map((item) => item.group)));

  async function logout() { await adminLogout().catch(() => undefined); router.replace("/admin/login"); }

  return <div className="min-h-screen bg-slate-50 text-slate-950">
    {sidebarOpen && <button aria-label="Close navigation" className="fixed inset-0 z-40 bg-slate-950/30 lg:hidden" onClick={() => setSidebarOpen(false)} />}
    <aside className={`fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200 bg-white transition-transform lg:translate-x-0 ${sidebarOpen ? "translate-x-0" : "-translate-x-full"} ${collapsed ? "lg:w-20" : "lg:w-72"}`}>
      <div className="flex h-20 items-center justify-between border-b border-slate-200 px-5"><Link href="/admin" className="flex items-center gap-3" onClick={() => setSidebarOpen(false)}><span className="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-700 text-lg font-extrabold text-white">N</span>{!collapsed && <span className="text-lg font-extrabold tracking-[-0.04em]">northstar<span className="text-blue-700">.</span></span>}</Link><button className="rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden" onClick={() => setSidebarOpen(false)} aria-label="Close sidebar"><X size={18} /></button></div>
      <nav className="flex-1 overflow-y-auto px-3 py-5" aria-label="Admin navigation">{groups.map((group) => <div key={group} className="mb-6"><p className={`mb-2 px-3 text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400 ${collapsed ? "lg:sr-only" : ""}`}>{group}</p><div className="grid gap-1">{navigation.filter((item) => item.group === group).map((item) => { const selected = active?.href === item.href; return <Link key={item.href} href={item.href} title={collapsed ? item.label : undefined} onClick={() => setSidebarOpen(false)} className={`flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition ${selected ? "bg-blue-50 text-blue-700" : "text-slate-600 hover:bg-slate-50 hover:text-slate-950"}`}><span className={`flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-sm ${selected ? "bg-blue-700 text-white" : "bg-slate-100 text-slate-500"}`}>{item.icon}</span>{!collapsed && <span className="truncate">{item.label}</span>}</Link>; })}</div></div>)}</nav>
      <div className="border-t border-slate-200 p-3"><button onClick={() => setCollapsed((value) => !value)} className="hidden w-full items-center justify-center gap-2 rounded-lg px-3 py-2 text-xs font-bold text-slate-500 hover:bg-slate-50 lg:flex" aria-label={collapsed ? "Expand sidebar" : "Collapse sidebar"}>{collapsed ? <PanelLeftOpen size={16} /> : <><PanelLeftClose size={16} /> Collapse menu</>}</button></div>
    </aside>
    <div className={`min-h-screen transition-[padding] lg:pl-72 ${collapsed ? "lg:pl-20" : ""}`}>
      <header className="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-slate-200 bg-white/95 px-5 backdrop-blur sm:px-8"><div className="flex items-center gap-4"><button className="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden" onClick={() => setSidebarOpen(true)} aria-label="Open navigation"><Menu size={21} /></button><div className="hidden items-center gap-2 text-sm text-slate-400 sm:flex"><ShieldCheck size={17} className="text-blue-700" /><span>Admin control center</span><ChevronRight size={15} /><span className="font-bold text-slate-800">{active?.label ?? "Overview"}</span></div></div><div className="flex items-center gap-3"><button className="rounded-lg p-2 text-slate-400 hover:bg-slate-100" aria-label="Notifications"><Bell size={18} /></button><div className="hidden text-right sm:block"><p className="text-sm font-bold text-slate-900">{admin.data.name}</p><p className="text-xs text-slate-500">{admin.data.roles?.join(" · ")}</p></div><button onClick={logout} className="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 hover:border-blue-200 hover:text-blue-700" aria-label="Log out"><LogOut size={16} /><span className="hidden sm:inline">Log out</span></button></div></header>
      <main className="mx-auto max-w-[1600px] p-5 sm:p-8"><div className="mb-7 flex items-center gap-2 text-xs text-slate-400"><Link href="/admin" className="hover:text-blue-700">Control center</Link><ChevronRight size={14} /><span className="font-bold text-slate-700">{active?.label ?? "Overview"}</span></div>{children}</main>
    </div>
  </div>;
}

export { navigation as adminNavigation };
