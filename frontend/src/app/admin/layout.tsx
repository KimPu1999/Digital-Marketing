import { AdminShell } from "@/components/admin-shell";
import type { Metadata } from "next";

export const instant = false;
export const metadata: Metadata = { title: "Admin Control Center | Northstar Digital", robots: { index: false, follow: false } };

export default function AdminLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return <AdminShell>{children}</AdminShell>;
}
