<?php

namespace App\Http\Controllers\Public\Concerns;

trait BuildsPublicSeo
{
    /**
     * @return array{title: string, description: string, image: ?string, jsonLd: array<string, mixed>}
     */
    protected function seo(
        string $title,
        ?string $description = null,
        ?string $image = null,
        string $type = 'WebSite',
    ): array {
        $brand = config('public.brand.name', 'ProcureAI');
        $fullTitle = str_contains($title, $brand) ? $title : "{$title} — {$brand}";

        return [
            'title' => $fullTitle,
            'description' => $description ?? __('public.seo.default_description'),
            'image' => $image,
            'jsonLd' => [
                '@context' => 'https://schema.org',
                '@type' => $type,
                'name' => $brand,
                'headline' => $title,
                'description' => $description ?? __('public.seo.default_description'),
                'url' => url()->current(),
            ],
        ];
    }
}
