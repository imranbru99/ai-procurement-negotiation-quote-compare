<?php

use App\Http\Controllers\Public\BlogController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\FeatureController;
use App\Http\Controllers\Public\HelpCenterController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\MarketingController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\PricingController;
use App\Http\Controllers\Public\RobotsController;
use App\Http\Controllers\Public\SitemapController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'public.locale'])->name('public.')->group(function (): void {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/features', [FeatureController::class, 'index'])->name('features');
    Route::get('/features/{module}', [FeatureController::class, 'show'])->name('features.show');
    Route::get('/ai-negotiation', [MarketingController::class, 'aiNegotiation'])->name('ai_negotiation');
    Route::get('/quote-comparison-demo', [MarketingController::class, 'quoteComparison'])->name('quote_comparison');
    Route::get('/how-it-works', [MarketingController::class, 'howItWorks'])->name('how_it_works');
    Route::get('/solutions', [MarketingController::class, 'solutionsIndex'])->name('solutions');
    Route::get('/solutions/{industry}', [MarketingController::class, 'solutionIndustry'])->name('solutions.industry');
    Route::get('/pricing', PricingController::class)->name('pricing');
    Route::get('/savings-calculator', [MarketingController::class, 'savingsCalculator'])->name('savings_calculator');
    Route::get('/demo', [MarketingController::class, 'demo'])->name('demo');
    Route::get('/supplier-signup', [MarketingController::class, 'supplierSignup'])->name('supplier_signup');
    Route::post('/supplier-signup', [MarketingController::class, 'storeSupplierSignup'])->name('supplier_signup.store');
    Route::get('/integrations', [MarketingController::class, 'integrations'])->name('integrations');
    Route::get('/security', [MarketingController::class, 'security'])->name('security');
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/search', [BlogController::class, 'index'])->name('blog.search');
    Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
    Route::get('/help', [HelpCenterController::class, 'index'])->name('help.index');
    Route::get('/help/{slug}', [HelpCenterController::class, 'show'])->name('help.show');
    Route::get('/changelog', [MarketingController::class, 'changelog'])->name('changelog');
    Route::get('/about', [MarketingController::class, 'about'])->name('about');
    Route::get('/careers', [MarketingController::class, 'careers'])->name('careers');
    Route::get('/partners', [MarketingController::class, 'partners'])->name('partners');
    Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    Route::get('/legal/{document}', [MarketingController::class, 'legal'])->name('legal');
    Route::get('/pages/{slug}', [PageController::class, 'show'])->name('page');
});

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');
