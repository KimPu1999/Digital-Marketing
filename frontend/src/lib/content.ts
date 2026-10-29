import type { LucideIcon } from "lucide-react";
import { BarChart3, Brush, Code2, Film, Mail, Megaphone, Search, Share2 } from "lucide-react";

export type Service = {
  slug: string;
  title: string;
  shortTitle: string;
  description: string;
  longDescription: string;
  icon: LucideIcon;
  color: "blue" | "navy" | "sky" | "indigo";
  benefits: string[];
  deliverables: string[];
  process: { title: string; description: string }[];
  related: string[];
  faqs: { question: string; answer: string }[];
};

export type Package = {
  name: string;
  eyebrow: string;
  description: string;
  price: string;
  priceNote: string;
  features: string[];
  featured?: boolean;
};

export type Project = {
  slug: string;
  title: string;
  clientLabel: string;
  category: string;
  year: string;
  summary: string;
  result: string;
  challenge: string;
  strategy: string;
  results: string[];
  accent: "blue" | "navy" | "sky" | "indigo";
  initials: string;
};

export type Post = {
  slug: string;
  title: string;
  category: string;
  date: string;
  readingTime: string;
  excerpt: string;
  body: string[];
  accent: "blue" | "navy" | "sky" | "indigo";
};

export type TeamMember = { name: string; role: string; bio: string; initials: string };

export const previewDisclaimer = "Illustrative preview content — client names, testimonials, case studies, and performance figures are placeholders pending CMS data.";

export const services: Service[] = [
  { slug: "seo-optimization", title: "SEO Optimization", shortTitle: "Search & SEO", description: "Build durable visibility around the searches that matter most.", longDescription: "We make search a useful growth channel by connecting technical foundations, content, and measurement to the questions your audience is already asking.", icon: Search, color: "blue", benefits: ["Clearer search visibility", "More qualified organic demand", "A healthier technical foundation"], deliverables: ["Technical SEO audit", "Search and content opportunity map", "On-page optimization", "Measurement and monthly recommendations"], process: [{ title: "Diagnose", description: "Understand the current search experience, technical constraints, and commercial opportunity." }, { title: "Prioritize", description: "Turn findings into a focused roadmap ranked by impact, effort, and confidence." }, { title: "Improve", description: "Ship the technical, content, and experience changes that compound over time." }], related: ["content-video-marketing", "marketing-analytics"], faqs: [{ question: "How soon should we expect movement?", answer: "Search compounds over time. We establish leading indicators early and review meaningful movement against the agreed roadmap." }, { question: "Do you work with internal content teams?", answer: "Yes. We can lead, support, or provide a clear system for your team to execute." }] },
  { slug: "social-media-management", title: "Social Media Management", shortTitle: "Social", description: "Create a consistent social presence that sounds like your brand.", longDescription: "We bring strategy, editorial thinking, and community care together so social channels feel active, useful, and connected to the wider business.", icon: Share2, color: "sky", benefits: ["A clearer channel role", "Consistent publishing quality", "More useful audience signals"], deliverables: ["Channel strategy", "Monthly content calendar", "Creative direction and copy", "Community response playbook"], process: [{ title: "Listen", description: "Map the audience, existing voice, channel patterns, and cultural context." }, { title: "Shape", description: "Create repeatable themes and formats that make good content easier to produce." }, { title: "Learn", description: "Review signals and use them to make the next month sharper." }], related: ["branding-graphic-design", "content-video-marketing"], faqs: [{ question: "Which platforms do you support?", answer: "We tailor the channel mix to the audience and the team. The foundation supports Instagram, LinkedIn, TikTok, Facebook, and more." }, { question: "Can you work with our existing brand guidelines?", answer: "Absolutely. We can work within an established system or help make it more usable for social." }] },
  { slug: "paid-advertising", title: "Paid Advertising", shortTitle: "Paid Growth", description: "Make every media decision more focused, measurable, and useful.", longDescription: "We help teams create a paid growth system that joins audience strategy, creative testing, landing pages, and clear reporting.", icon: Megaphone, color: "navy", benefits: ["More focused media decisions", "Faster creative learning", "Cleaner performance visibility"], deliverables: ["Channel and audience plan", "Campaign architecture", "Creative testing matrix", "Weekly performance readout"], process: [{ title: "Frame", description: "Align the business goal, audience, offer, budget, and measurement plan." }, { title: "Test", description: "Launch focused experiments that reveal what deserves more attention." }, { title: "Scale", description: "Invest behind the signals while protecting quality and efficiency." }], related: ["marketing-analytics", "website-development"], faqs: [{ question: "Are ad budgets included?", answer: "No. Our service fee and the advertising media budget are separate so each investment stays transparent." }, { question: "Which ad platforms do you use?", answer: "We choose channels based on audience behavior and the role the channel needs to play, including search, social, and display." }] },
  { slug: "branding-graphic-design", title: "Branding & Graphic Design", shortTitle: "Brand Systems", description: "Give good ideas a visual system people can recognize.", longDescription: "We create practical brand systems that make your positioning clearer and your everyday marketing more consistent.", icon: Brush, color: "indigo", benefits: ["A sharper market position", "More consistent touchpoints", "A system your team can use"], deliverables: ["Positioning workshop", "Visual direction", "Core identity toolkit", "Campaign-ready templates"], process: [{ title: "Discover", description: "Find the useful tension between audience need, business strength, and category context." }, { title: "Define", description: "Turn that insight into a clear point of view and visual language." }, { title: "Equip", description: "Package the system so teams can apply it with confidence." }], related: ["social-media-management", "website-development"], faqs: [{ question: "Can you refresh an existing identity?", answer: "Yes. A focused refresh can clarify the system while preserving the recognition you have already built." }, { question: "Do you provide editable files?", answer: "Yes, final deliverables are prepared for the tools and teams that will use them." }] },
  { slug: "website-development", title: "Website Development", shortTitle: "Web Development", description: "Turn a clear brand and strategy into a fast, useful digital experience.", longDescription: "We build conversion-minded websites that balance editorial clarity, thoughtful interaction, and a maintainable technical foundation.", icon: Code2, color: "blue", benefits: ["A clearer customer journey", "A flexible content foundation", "A faster, more accessible experience"], deliverables: ["Information architecture", "Responsive UI implementation", "CMS-ready content patterns", "Launch quality checklist"], process: [{ title: "Map", description: "Clarify user needs, content priorities, journeys, and technical constraints." }, { title: "Design", description: "Create a visual system that makes the right actions easy to find." }, { title: "Build", description: "Ship a responsive, accessible experience with a clean handoff." }], related: ["branding-graphic-design", "marketing-analytics"], faqs: [{ question: "Can you work with our existing CMS?", answer: "Yes. The presentation layer can be shaped around your current or future content system." }, { question: "Do you provide ongoing support?", answer: "Support can be scoped around content changes, optimization, and future improvements." }] },
  { slug: "content-video-marketing", title: "Content & Video Marketing", shortTitle: "Content & Video", description: "Tell useful stories in formats people want to spend time with.", longDescription: "We combine editorial strategy and visual storytelling to make your expertise easier to find, understand, and remember.", icon: Film, color: "sky", benefits: ["A repeatable editorial engine", "More useful brand education", "Content that travels further"], deliverables: ["Editorial strategy", "Campaign concepts", "Short-form video direction", "Content repurposing system"], process: [{ title: "Find the story", description: "Identify the ideas, proof points, and audience tensions worth owning." }, { title: "Make the system", description: "Choose formats, channels, and rhythms that your team can sustain." }, { title: "Create and refine", description: "Publish, learn, and keep the work useful rather than merely busy." }], related: ["seo-optimization", "social-media-management"], faqs: [{ question: "Do you produce on location?", answer: "We can shape remote-first, studio, or on-location production depending on the story and scope." }, { question: "Can you help with content we already have?", answer: "Yes. Repurposing an existing knowledge base is often a strong first step." }] },
  { slug: "email-marketing", title: "Email Marketing", shortTitle: "Email", description: "Build a direct relationship with the people who already raised their hand.", longDescription: "We turn email into a thoughtful customer experience with useful segmentation, clear journeys, and messages people are glad to receive.", icon: Mail, color: "indigo", benefits: ["More useful lifecycle journeys", "Stronger audience relationships", "Clearer campaign learning"], deliverables: ["Lifecycle map", "Campaign and automation plan", "Email copy and design direction", "Performance review"], process: [{ title: "Segment", description: "Understand the moments, needs, and signals that should shape the experience." }, { title: "Sequence", description: "Build useful journeys that move people forward without adding noise." }, { title: "Tune", description: "Use behavior and feedback to improve relevance over time." }], related: ["content-video-marketing", "marketing-analytics"], faqs: [{ question: "Can you work with our email platform?", answer: "Yes. We can adapt the strategy and implementation plan to your existing tooling." }, { question: "Do you write the emails?", answer: "Yes, copy and creative direction can be included in the engagement." }] },
  { slug: "marketing-analytics", title: "Marketing Analytics", shortTitle: "Analytics", description: "Make the signal easier to see and the next decision easier to make.", longDescription: "We bring clarity to marketing measurement with practical tracking, useful reporting, and a shared language for performance.", icon: BarChart3, color: "navy", benefits: ["More trustworthy measurement", "Faster decision-making", "A shared performance language"], deliverables: ["Measurement plan", "Tracking audit", "Reporting dashboard design", "Monthly insight review"], process: [{ title: "Align", description: "Define the decisions the data needs to support and the language everyone can share." }, { title: "Connect", description: "Map the important events and build a clean path from activity to outcome." }, { title: "Explain", description: "Turn reporting into a small set of actions the team can take." }], related: ["paid-advertising", "seo-optimization"], faqs: [{ question: "Will you replace our analytics tools?", answer: "Usually no. We make the existing stack more useful before recommending additional complexity." }, { question: "Can you set up dashboards?", answer: "Yes. Dashboard design and the underlying measurement plan are part of the service scope." }] },
];

export const packages: Package[] = [
  { name: "Starter", eyebrow: "Find your footing", description: "A focused foundation for teams ready to make their next move clearer.", price: "$1,500", priceNote: "service fee / month", features: ["One priority channel", "Monthly strategy session", "Core opportunity audit", "Monthly recommendations"] },
  { name: "Growth", eyebrow: "Build momentum", description: "A connected growth program for teams with a clear opportunity to pursue.", price: "$3,500", priceNote: "service fee / month", features: ["Two connected channels", "Biweekly working sessions", "Campaign and content system", "Performance reporting"], featured: true },
  { name: "Premium", eyebrow: "Move with confidence", description: "A senior partner team for complex, multi-channel growth work.", price: "$6,500", priceNote: "service fee / month", features: ["Full-funnel strategy", "Senior creative direction", "Weekly optimization", "Leadership-ready insights"] },
  { name: "Custom Quote", eyebrow: "Shape the right team", description: "A tailored engagement around your goals, team, timing, and operating model.", price: "Let’s talk", priceNote: "scope-led service fee", features: ["Flexible specialist team", "Custom deliverables", "Project or retainer options", "Transparent scope planning"] },
];

export const projects: Project[] = [
  { slug: "northstar-example", title: "A clearer category story", clientLabel: "Illustrative SaaS brand", category: "Brand strategy", year: "Preview", summary: "Making a complex product easier to understand and easier to choose.", result: "Illustrative outcome: clearer qualified demand", challenge: "The product was strong, but the story changed from page to page and made the buying decision harder than it needed to be.", strategy: "We focused the positioning, created a more useful narrative system, and carried it into the web experience and campaign toolkit.", results: ["Illustrative: a sharper value proposition", "Illustrative: stronger campaign consistency", "Illustrative: a clearer sales story"], accent: "blue", initials: "N" },
  { slug: "meridian-example", title: "Growth with more signal", clientLabel: "Illustrative retail brand", category: "Paid growth", year: "Preview", summary: "Building a testing system that helped a growing team learn faster.", result: "Illustrative outcome: better media efficiency", challenge: "The team was publishing campaigns, but lacked a shared view of what was being learned and where to place the next bet.", strategy: "We simplified the campaign architecture, connected creative testing to audience insight, and made weekly decisions more visible.", results: ["Illustrative: cleaner testing cadence", "Illustrative: more useful channel reporting", "Illustrative: clearer investment decisions"], accent: "navy", initials: "M" },
  { slug: "finch-example", title: "Making the complex feel simple", clientLabel: "Illustrative fintech brand", category: "SEO & content", year: "Preview", summary: "Turning expert knowledge into a more discoverable customer journey.", result: "Illustrative outcome: stronger organic experience", challenge: "Useful expertise was buried across disconnected pages and did not meet the audience at the moments they needed help.", strategy: "We mapped intent, rebuilt the content system around real questions, and improved the paths between education and action.", results: ["Illustrative: clearer content architecture", "Illustrative: improved search experience", "Illustrative: more useful conversion paths"], accent: "sky", initials: "F" },
  { slug: "orbit-example", title: "A brand system that travels", clientLabel: "Illustrative consumer brand", category: "Brand identity", year: "Preview", summary: "Equipping a growing team with a brand system they could actually use.", result: "Illustrative outcome: more consistent touchpoints", challenge: "As the team grew, the brand became harder to apply consistently across campaigns and channels.", strategy: "We defined a practical visual language with templates, examples, and guidance for everyday marketing decisions.", results: ["Illustrative: easier campaign production", "Illustrative: stronger visual recognition", "Illustrative: a more confident internal team"], accent: "indigo", initials: "O" },
];

export const posts: Post[] = [
  { slug: "better-marketing-brief", title: "The marketing brief that makes better work possible", category: "Strategy", date: "Oct 12, 2026", readingTime: "6 min read", excerpt: "A useful brief does more than list deliverables. It creates the conditions for a better decision.", body: ["Most marketing briefs start with an output: a campaign, a landing page, a content series. The strongest briefs start one step earlier with the decision the work needs to support.", "That shift changes the conversation. Instead of asking whether an idea is attractive in isolation, the team can ask whether it makes the next customer or business decision easier.", "A good brief gives the team a clear audience, a meaningful tension, a success signal, and enough room to make the idea better than the first version."], accent: "blue" },
  { slug: "content-that-compounds", title: "How to build a content system that compounds", category: "Content", date: "Sep 28, 2026", readingTime: "8 min read", excerpt: "Useful content becomes more valuable when every piece helps the next piece work harder.", body: ["Content gets expensive when every new idea starts from a blank page. A system gives the team a set of useful starting points.", "Begin with the questions your audience asks repeatedly. Then create a small number of strong source pieces and design ways to adapt them across channels.", "The goal is not more publishing. It is a better relationship between what you know, what people need, and what the business is trying to make easier."], accent: "sky" },
  { slug: "analytics-without-the-noise", title: "Analytics without the noise", category: "Analytics", date: "Sep 09, 2026", readingTime: "5 min read", excerpt: "A smaller, clearer reporting system is often more useful than a bigger dashboard.", body: ["More numbers do not automatically create more clarity. A reporting system should help a team choose what to do next.", "Start by naming the recurring decisions. Then identify the signals that can change those decisions and remove the rest from the main view.", "A good dashboard feels less like a warehouse and more like a well-edited conversation about the work."], accent: "navy" },
  { slug: "small-team-big-clarity", title: "What a small senior team changes", category: "Northstar", date: "Aug 21, 2026", readingTime: "4 min read", excerpt: "The value of a small team is not fewer people. It is fewer translations between a problem and a decision.", body: ["Small teams work best when the context stays close to the work. The person thinking about the strategy should be able to stay close to what gets made.", "That does not mean one person does everything. It means the team has a shared point of view and can bring the right specialist in at the right moment.", "The result is often a calmer process: fewer handoffs, clearer tradeoffs, and more room for the work to become itself."], accent: "indigo" },
];

export const team: TeamMember[] = [
  { name: "Ari Lin", role: "Strategy Director", bio: "Turns business tension into a clear point of view and a focused plan.", initials: "AL" },
  { name: "Maya Chen", role: "Creative Lead", bio: "Makes useful ideas feel distinctive, understandable, and ready to travel.", initials: "MC" },
  { name: "Jon Bell", role: "Performance Partner", bio: "Finds the signal in the numbers and connects it to the next action.", initials: "JB" },
  { name: "Nora Reyes", role: "Digital Producer", bio: "Keeps the moving parts thoughtful, practical, and moving in the same direction.", initials: "NR" },
];

export const testimonials = [
  { quote: "They made the complex feel clear and gave our team a sharper way to make decisions.", name: "Illustrative partner quote", role: "Preview testimonial" },
  { quote: "The work brought our strategy and our everyday marketing into the same conversation.", name: "Illustrative partner quote", role: "Preview testimonial" },
  { quote: "Thoughtful, direct, and focused on the work that would matter next.", name: "Illustrative partner quote", role: "Preview testimonial" },
];

export const faqs = [
  { question: "What does a typical engagement look like?", answer: "We begin with a focused discovery sprint, align on the growth opportunity, then build a clear 90-day roadmap with measurable milestones and a regular reporting rhythm." },
  { question: "Do you work with early-stage companies?", answer: "Yes. We work with ambitious teams at different stages, from first positioning through repeatable acquisition and larger-scale growth." },
  { question: "How do you measure success?", answer: "We connect marketing activity to the business metric that matters: qualified pipeline, revenue, retention, or the next meaningful step in the customer journey." },
  { question: "Are service fees separate from advertising budgets?", answer: "Yes. Service fees cover our strategic and production work. Advertising budgets are paid directly to media platforms and planned separately." },
  { question: "Can you work with our internal team?", answer: "Yes. We can lead a workstream, embed with an existing team, or provide a system your team can carry forward." },
  { question: "Is the content on this site real client work?", answer: "This Phase 2 site uses clearly labeled illustrative preview content. Verified client work and CMS-managed content will be connected in a later phase." },
];
