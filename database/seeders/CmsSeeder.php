<?php

namespace Database\Seeders;

use App\Modules\cms\Models\CmsCategory;
use App\Modules\cms\Models\CmsFaq;
use App\Modules\cms\Models\CmsMenu;
use App\Modules\cms\Models\CmsPage;
use App\Modules\cms\Models\CmsPost;
use App\Modules\cms\Models\CmsTestimonial;
use App\Modules\knowledge_base\Models\KbArticle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('cms_pages')) {
            return;
        }

        $locale = 'en';
        $published = now()->subDay();

        $pages = [
            'home' => ['ProcureAI — Negotiate smarter', '<p>ProcureAI unifies sourcing, comparison, and AI-assisted negotiation for industrial buyers.</p>'],
            'features' => ['Features overview', '<p>Activate modular procure-to-pay capabilities per tenant.</p>'],
            'pricing' => ['Modules & Pricing', '<p>Flexible plans with module matrix and currency switcher on the marketing site.</p>'],
            'demo' => ['Live demo', '<p>Explore seeded demo workspaces with documented credentials.</p>'],
            'contact' => ['Contact', '<p>Reach our solutions team for pilots and enterprise deployments.</p>'],
            'about' => ['About', '<p>We build procurement AI that keeps humans accountable.</p>'],
            'how-it-works' => ['How it works', '<ol><li>Requisition</li><li>RFQ</li><li>Quotes</li><li>Compare & negotiate</li><li>Award</li></ol>'],
            'ai-negotiation' => ['AI negotiation', '<p>Template-driven prompts with demo mode for safe evaluations.</p>'],
            'integrations' => ['Integrations', '<p>REST hooks for ERP, webhooks for supplier portals, and export pipelines.</p>'],
            'security' => ['Security', '<p>Role-based access, tenant isolation, and encryption in transit and at rest.</p>'],
            'changelog' => ['Changelog', '<p><strong>Phase 8</strong> — Public marketing site + CMS.</p>'],
            'supplier-signup' => ['Supplier signup', '<p>Register interest to receive onboarding invitations.</p>'],
            'solutions' => ['Solutions', '<p>Industry playbooks for manufacturing through government.</p>'],
            'savings-calculator' => ['Savings calculator', '<p>Model competitive savings before rollout.</p>'],
            'terms' => ['Terms of Service', '<p>Standard SaaS terms for ProcureAI subscribers.</p>'],
            'privacy' => ['Privacy Policy', '<p>We process business contact data to deliver the service.</p>'],
            'cookie' => ['Cookie Policy', '<p>We use essential cookies for sessions and preferences.</p>'],
            'dpa' => ['DPA', '<p>Data processing terms for enterprise customers.</p>'],
            'careers' => ['Careers', '<p>We are hiring Laravel, Filament, and procurement domain experts.</p>'],
            'partners' => ['Partners', '<p>Alliances with systems integrators and ERP vendors.</p>'],
        ];

        foreach ($pages as $slug => [$title, $body]) {
            CmsPage::query()->updateOrCreate(
                ['locale' => $locale, 'slug' => $slug],
                [
                    'title' => $title,
                    'body' => $body,
                    'body_format' => 'html',
                    'meta_title' => $title,
                    'meta_description' => strip_tags($body),
                    'published_at' => $published,
                ]
            );
        }

        CmsPage::query()->updateOrCreate(
            ['locale' => 'bn', 'slug' => 'home'],
            [
                'title' => 'প্রকিউরএআই-এ স্বাগতম',
                'body' => '<p>সরবরাহকারী তুলনা ও দর কষাকষির জন্য বুদ্ধিমান ক্রয় সহায়ক।</p>',
                'body_format' => 'html',
                'published_at' => $published,
            ]
        );

        foreach (config('public.industries', []) as $industry) {
            CmsPage::query()->updateOrCreate(
                ['locale' => $locale, 'slug' => 'solution-'.$industry],
                [
                    'title' => ucfirst($industry).' solution',
                    'body' => '<p>'.e(__('public.solutions.'.$industry.'.lead')).'</p>',
                    'body_format' => 'html',
                    'published_at' => $published,
                ]
            );
        }

        $category = CmsCategory::query()->updateOrCreate(
            ['locale' => $locale, 'slug' => 'product-updates'],
            ['name' => 'Product updates', 'description' => 'Release notes and roadmap highlights']
        );

        CmsPost::query()->updateOrCreate(
            ['locale' => $locale, 'slug' => 'welcome-phase-8'],
            [
                'cms_category_id' => $category->id,
                'title' => 'Launching the ProcureAI marketing site',
                'excerpt' => 'CMS-driven pages, multilingual public routes, and Filament content tools.',
                'body' => '<p>Phase 8 delivers a brand-first public website with seeded blog and knowledge base content.</p>',
                'body_format' => 'html',
                'author_name' => 'ProcureAI Team',
                'meta_title' => 'ProcureAI marketing site',
                'published_at' => $published,
            ]
        );

        CmsFaq::query()->updateOrCreate(
            ['locale' => $locale, 'category' => 'pricing', 'question' => 'Can I enable modules individually?'],
            [
                'answer' => 'Yes—tenants toggle modules from the Filament module manager with dependency checks.',
                'sort' => 1,
                'published_at' => $published,
            ]
        );

        CmsFaq::query()->updateOrCreate(
            ['locale' => $locale, 'category' => 'pricing', 'question' => 'Do you support multi-currency pricing?'],
            [
                'answer' => 'Marketing pricing includes a currency switcher; tenant billing uses the multi-currency module.',
                'sort' => 2,
                'published_at' => $published,
            ]
        );

        CmsFaq::query()->updateOrCreate(
            ['locale' => 'ar', 'question' => 'كيف يعمل وضع العرض التوضيحي للذكاء الاصطناعي؟'],
            [
                'category' => 'ai',
                'answer' => 'يستخدم النظام ردوداً محلية حتمية دون مفاتيح API.',
                'sort' => 1,
                'published_at' => $published,
            ]
        );

        CmsTestimonial::query()->updateOrCreate(
            ['locale' => $locale, 'name' => 'Aisha Rahman'],
            [
                'role' => 'Head of Sourcing',
                'company' => 'Northline Manufacturing',
                'body' => 'Quote comparison exports alone saved our buyers hours every RFQ cycle.',
                'rating' => 5,
                'published_at' => $published,
            ]
        );

        foreach ([
            ['Features', '/features', 10],
            ['Pricing', '/pricing', 20],
            ['Demo', '/demo', 30],
            ['Blog', '/blog', 40],
        ] as [$label, $url, $sort]) {
            CmsMenu::query()->updateOrCreate(
                ['locale' => $locale, 'location' => 'header', 'label' => $label],
                ['url' => $url, 'sort' => $sort]
            );
        }

        KbArticle::query()->updateOrCreate(
            ['locale' => $locale, 'slug' => 'getting-started'],
            [
                'title' => 'Getting started with ProcureAI',
                'category' => 'Onboarding',
                'body' => '<p>Create a tenant, enable modules, and invite buyers and suppliers.</p>',
                'body_format' => 'html',
                'published_at' => $published,
            ]
        );

        KbArticle::query()->updateOrCreate(
            ['locale' => $locale, 'slug' => 'compare-quotes'],
            [
                'title' => 'Compare supplier quotes',
                'category' => 'Sourcing',
                'body' => '<p>Use the quote comparison workspace to normalize lines and export PDF summaries.</p>',
                'body_format' => 'html',
                'published_at' => $published,
            ]
        );
    }
}
