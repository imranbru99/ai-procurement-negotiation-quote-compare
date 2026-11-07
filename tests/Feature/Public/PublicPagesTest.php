<?php

use Database\Seeders\CmsSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(CmsSeeder::class);
});

it('renders the marketing home page', function (): void {
    $this->get(route('public.home'))
        ->assertOk()
        ->assertSee('ProcureAI')
        ->assertSee('Negotiate smarter');
});

it('renders pricing with plans and faq', function (): void {
    $this->get(route('public.pricing'))
        ->assertOk()
        ->assertSee('Starter')
        ->assertSee('Can I enable modules individually?');
});

it('renders features overview and module page', function (): void {
    $this->get(route('public.features'))
        ->assertOk()
        ->assertSee('Features');

    $this->get(route('public.features.show', 'quote_comparison'))
        ->assertOk()
        ->assertSee('Quote Comparison', false);
});

it('renders demo accounts and blog', function (): void {
    $this->get(route('public.demo'))
        ->assertOk()
        ->assertSee('superadmin@procureai.test');

    $this->get(route('public.blog.index'))
        ->assertOk()
        ->assertSee('Launching the ProcureAI marketing site');
});

it('smoke tests marketing utility pages', function (): void {
    $this->get(route('public.ai_negotiation'))->assertOk();
    $this->get(route('public.savings_calculator'))->assertOk();
    $this->get(route('public.solutions'))->assertOk();
    $this->get(route('public.legal', 'terms'))->assertOk();
    $this->get(route('public.contact.create'))->assertOk();
    $this->get(route('sitemap'))->assertOk();
    $this->get(route('robots'))
        ->assertOk()
        ->assertSee('Sitemap:');
});
