import { AuditLogsPage, ContentManager, LeadsPage, MediaPage, NavigationPage, RolesPage, SettingsPage, UsersPage } from "@/components/admin";

export const instant = false;

const contentSections = new Set(["services", "packages", "portfolio", "blog", "blog-categories", "testimonials", "faqs"]);

export default async function Page({ params }: { params: Promise<{ section: string }> }) {
  const { section } = await params;
  if (contentSections.has(section)) return <ContentManager section={section} />;
  if (section === "leads") return <LeadsPage />;
  if (section === "media") return <MediaPage />;
  if (section === "navigation") return <NavigationPage />;
  if (section === "users") return <UsersPage />;
  if (section === "roles") return <RolesPage />;
  if (section === "audit-logs") return <AuditLogsPage />;
  const settingsMap: Record<string, string> = { appearance: "appearance", "homepage-sections": "homepage_sections", hero: "hero", footer: "footer", seo: "seo", contact: "contact", general: "general" };
  return <SettingsPage group={settingsMap[section] ?? "general"} />;
}
