<p align="center">
  <img src="docs/screenshots/dashboard.svg" alt="AI Procurement Negotiation dashboard preview" width="100%"/>
</p>

<h1 align="center">AI Procurement Negotiation</h1>

<p align="center"><strong>Supplier quote comparison and AI negotiation assistant</strong></p>

<p align="center">
  <a href="https://github.com/imranbru99/ai-procurement-negotiation-quote-compare"><img alt="GitHub" src="https://img.shields.io/badge/GitHub-imranbru99%2Fai-procurement-negotiation-quote-compare-0F172A?style=for-the-badge&logo=github"/></a>
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-PHP-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img alt="Author" src="https://img.shields.io/badge/Author-Imran%20Ahmed-14B8A6?style=for-the-badge"/>
</p>

---

## Overview

**AI Procurement Negotiation** is a complete business application focused on real operational workflows. This README lists the full feature surface so GitHub visitors and search engines can understand every capability of the product.

## Product preview

| Dashboard | Operations |
| :---: | :---: |
| ![Dashboard](docs/screenshots/dashboard.svg) | ![Operations](docs/screenshots/operations.svg) |

## Complete feature list

### Application modules

- ai negotiation
- approvals
- attendance
- audit log
- awards
- backup gdpr
- budgets
- catalog
- cms
- collaboration
- contracts
- dashboard
- documents
- goods receiving
- integrations
- inventory
- invoices
- knowledge base
- modules
- multi branch
- multi currency
- multi language
- notifications
- payments
- price intelligence
- purchase orders
- quote comparison
- quotes
- requisitions
- reverse auctions
- rfq
- savings
- settings
- spend analytics
- subscription
- supplier performance
- supplier risk
- suppliers
- support
- tasks
- tenants
- users

### Admin / Filament resources

- Attendance Device
- Awards
- Budgets
- Catalog Category
- Catalog Item
- Cms Faq
- Cms Page
- Cms Post
- Comment
- Contract Clauses
- Contracts
- Cost Centers
- Currency
- Document
- Entity
- Exchange Rate
- Gdpr Delete Request
- Goods Receipts
- Kb Article
- Market Price
- Payments
- Plan
- Purchase Order
- Purchase Orders
- Purchase Requisition
- Quote Extraction
- Quote
- Reverse Auction
- Rfq Invitation
- Rfq
- Savings Record
- Stock Items
- Supplier Invoices
- Supplier
- Supplier Risk Assessment
- Supplier Scorecard
- Support Ticket
- Task
- Tax Rule
- Warehouses

### Domain models

- Ai Job
- Ai Prompt Template
- Ai Usage Log
- Approval Action
- Approval Delegation
- Approval Request
- Approval Workflow
- Auction Bid
- Award
- Award Line
- Belongs To Branch
- Belongs To Tenant
- Budget
- Catalog Item
- Contract
- Contract Clause
- Contract Version
- Cost Center
- Goods Receipt
- Goods Receipt Line
- Has Comments
- Market Price Point
- Module Definition
- Negotiation Round
- Negotiation Strategy
- Negotiation Thread
- Payment
- Plan
- Plan Module
- Price Alert
- Purchase Order
- Purchase Order Line
- Purchase Requisition
- Purchase Requisition Line
- Quote
- Quote Comparison
- Quote Extraction
- Quote Import
- Quote Line
- Reverse Auction
- Rfq
- Rfq Invitation
- Rfq Line
- Rfq Question
- Savings Record
- Scoring Criterion
- Setting
- Stock Item
- Stock Movement
- Supplier

### HTTP / API controllers

- Blog APIs & screens
- Contact APIs & screens
- Feature APIs & screens
- Help Center APIs & screens
- Home APIs & screens
- Marketing APIs & screens
- Page APIs & screens
- Pricing APIs & screens
- Rfq Invitation APIs & screens
- Robots APIs & screens
- Sitemap APIs & screens

### Detailed feature requirements delivered

- MASTER PROMPT — AI Procurement Negotiation Assistant (Supplier Quote Comparison)
- Stack: PHP 8.3+, Laravel 11/12, Filament 3 (latest stable), MySQL, Livewire, Alpine, Tailwind, Vite. Queue: database driver. Cache: database/file. Tests: Pest.
- Keep code clean: Form Requests, Policies, Services/Actions, Enums, DTOs, Events/Listeners, Jobs. No business logic in controllers or Blade.
- PRODUCT VISION
- Request quotes from many suppliers, collect and normalize them (price, tax, shipping, lead time, payment terms, warranty, MOQ, validity).
- REQUIRED PACKAGES (install and integrate all)
- Scrape/import market prices, product specs, supplier catalogs, competitor price points for benchmarking (Price Intelligence module).
- All PDFs: RFQ, comparison report, negotiation brief, PO, contract, invoice, GRN, scorecard, savings report. Must render Bangla, Arabic, Hindi, Chinese, etc. correctly.
- MODULE SYSTEM (CORE REQUIREMENT)
- Dependency resolution: enabling a module auto-suggests required modules; disabling warns about dependents.
- Plan gating: subscription plans define which modules are available; tenant owners toggle within their plan.
- Helper API: Module::enabled('rfq'), Blade @module('rfq'), route middleware module:rfq, policy hook, and tests proving on/off behavior for every module.
- Core (always on): Tenants & Branding, Users/Roles/Permissions, Module Manager, Settings, Audit Log, Notifications, Dashboard.
- Supplier Management (profiles, contacts, categories, documents, certifications, bank info, onboarding, approval status, blacklist/preferred tiers)
- RFQ / RFP / RFI (create, invite suppliers, deadlines, Q&A, attachments, templates, versions)
- Supplier Quote Collection (supplier portal submission, manual entry, Excel/CSV import, PDF/email quote upload with AI extraction)
- AI Negotiation Assistant (see section 4)
- Award & Split Awards (award by line, by supplier, by weighted score, justification record)
- Purchase Orders (generate from award, revisions, supplier acknowledgment, PDF, email)
- Contract Management (clauses library, renewal alerts, price-lock, SLAs, e-sign-ready flow, AI clause risk review)
- Inventory (stock levels, reorder points, warehouses, movements, auto-requisition from low stock)
- Invoices & 3-Way Match (PO ↔ GRN ↔ Invoice, tolerance rules, exception queue, AI invoice extraction)
- Payments & Payables (schedules, early-payment discount tracking, status, export)
- Supplier Performance & Scorecards (on-time, quality, price, responsiveness, periodic reviews)
- Supplier Risk & Compliance (risk score, document expiry, sanctions/blacklist notes, financial/geo risk flags via AI)
- Spend Analytics & Reports (category, supplier, department, trend, maverick spend, tail spend)
- Savings Tracker (baseline vs negotiated, hard/soft savings, per-negotiation and per-buyer reports)
- Price Intelligence (via laravel-ai-catalog: scrape market prices/specs, price history, benchmark alerts)
- Attendance (via laravel-attendance-hub: devices, staff logs, shifts, overtime, link to receiving/site work)
- Collaboration (comments, mentions, internal notes on RFQs/quotes/negotiations)
- Integrations & Webhooks (REST API with Sanctum, outgoing webhooks, CSV/Excel import-export, email-in for quotes)
- Subscription & Billing (plans, trials, usage limits, invoices, payment gateway abstraction with offline/manual option and Stripe-ready driver)
- Mobile-Responsive Supplier & Approver Portal (PWA-ready: manifest + service worker)
- AI NEGOTIATION ASSISTANT (SIGNATURE FEATURE)
- Quote extraction: upload PDF/image/email text → structured quote lines (validate before saving; human review screen).
- Quote analysis: hidden costs, unusual terms, missing items, ambiguous clauses, unit mismatches, outliers vs other quotes and vs market benchmark.
- Negotiation strategy: per-supplier leverage points, target price, walk-away price, BATNA, concession plan, suggested trade-offs (volume, payment terms, lead time, bundling).
- Negotiation rounds: threaded rounds per RFQ/supplier with each offer, counter, status, and automatic delta/savings calculation.
- Simulator: "what will the supplier likely do?" scenarios with confidence, clearly labeled as AI estimate.
- Explainability: every AI recommendation shows reasoning, inputs used, and a "human must confirm" gate before award/send. Never auto-award or auto-send.
- Guardrails: redact sensitive data per settings, log all prompts/outputs in audit trail, per-plan AI usage quotas.
- USER TYPES & PERMISSIONS
- Super Admin (platform owner: tenants, plans, modules, global settings, AI config, CMS, support)
- Supplier (separate portal: respond to RFQs, submit/revise quotes, join auctions, acknowledge POs, upload invoices/documents, view payment status)
- PUBLIC WEBSITE (SEVERAL PAGES, ALL REAL)
- Quote Comparison showcase (interactive demo comparison table using seeded sample data)
- Demo page: one-click login buttons for each demo role + "Explore as…" guide
- Legal: Terms, Privacy, Cookie policy, DPA
- DEMO DATA (SEEDERS — MUST MAKE THE APP FEEL ALIVE)
- A scheduled/command demo:reset that restores demo data nightly.
- FILAMENT UX REQUIREMENTS
- Dashboards per role: KPIs, savings YTD, open RFQs, pending approvals, spend by category, supplier performance, overdue deliveries, AI insights widget.
- Comparison page: custom Filament page with sticky header matrix, per-criterion weights, best-value badges, outlier warnings, "Ask AI" panel, "Award" wizard.
- NON-FUNCTIONAL REQUIREMENTS
- Performance: eager loading, indexes, cached settings/module state, queued emails/AI/PDF/scrape jobs, drutogoti enabled, no N+1 (add a test/guard in dev).
- Reliability: idempotent jobs, failure handling UI, health check route, scheduler registered for exchange rates, renewal alerts, document expiry, demo reset, scrape jobs.
- Compliance: GDPR export/delete, data retention settings.
- Observability: log channels, activity log, AI cost dashboard.
- TESTING & QUALITY
- DEVOPS & DELIVERY
- One-command local setup: composer setup installs, migrates, seeds, builds assets.
- README.md (MUST BE GENERATED)
- Foundation: project, packages, tenancy, auth, roles, Filament panels, Module Manager, settings, audit log, base layout, theme.
- Master data: suppliers, catalog, categories, units, currencies/tax, documents.
- Sourcing flow: requisitions, approvals, RFQ, quote collection, supplier portal.
- Procure-to-pay: award, POs, contracts, GRN, inventory, invoices, 3-way match, payments, budgets.
- Intelligence: price intelligence (scrape), performance, risk, spend analytics, reverse auctions.
- Platform: attendance, collaboration, tasks, integrations/API/webhooks, subscription/billing, support desk, notifications, GDPR tools.
- Public site: CMS, blog, KB, all public pages, pricing, calculator, demo page, SEO.
- Demo data: full seeders across 3 tenants, demo AI driver, reset command.

### Platform capabilities

- Multi-role ready structure for operators, managers and admins
- Database migrations and seeders for demo-ready local setup
- Laravel architecture with services/controllers/models separation
- Responsive UI foundation (Blade / Filament where enabled)
- Environment-based config for local and production databases
- GitHub-ready documentation with visual product previews

## Tech stack

- PHP 8.2+ / Laravel
- Filament admin resources (where enabled)
- Vite asset pipeline
- SQLite/MySQL-ready database layer
- Migrations, seeders, and test-ready structure

## Quick start

```bash
git clone https://github.com/imranbru99/ai-procurement-negotiation-quote-compare.git
cd ai-procurement-negotiation-quote-compare
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

## Repository

- GitHub: https://github.com/imranbru99/ai-procurement-negotiation-quote-compare
- Developer: **Imran Ahmed** · https://imrandev.bd

---

## 🤝 Let's Connect & Collaborate

I am open to **Senior Remote Full-Stack Roles**, AI Platform Architecture Contracts, and Enterprise Technical Advisory.

**Timezone:** UTC+6 (Dhaka / Rangpur, Bangladesh) — flexible overlap with US, UK & EU hours.  
**Delivery:** Async-ready · Slack · Discord · Jira · GitHub · production-first accountability.

| Channel | Handle | Link |
| :--- | :--- | :--- |
| Portfolio | imrandev.bd | https://imrandev.bd |
| Packagist | imrandevbd | https://packagist.org/packages/imrandevbd/ |
| LinkedIn | imranbru99 | https://linkedin.com/in/imranbru99 |
| GitHub | imranbru99 | https://github.com/imranbru99 |
| WhatsApp | +880 1576-918420 | https://wa.me/8801576918420 |
| Email | me@imrandev.bd | mailto:me@imrandev.bd |
| X | @imrandev_bd | https://x.com/imrandev_bd |
| YouTube | @ImranDevBD | https://youtube.com/@ImranDevBD |
