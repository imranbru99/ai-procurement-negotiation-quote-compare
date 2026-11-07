<?php

use ImranDev\UnicodePdf\Exceptions\PdfGenerationException;
use ImranDev\UnicodePdf\Facades\UnicodePdf;

it('renders bengali quote comparison blade', function (): void {
    $html = view('pdf.quote-comparison', [
        'title' => 'তুলনা',
        'results' => ['quotes' => []],
    ])->render();

    expect($html)->toContain('সরবরাহকারী');
});

it('generates or skips unicode pdf for bengali content', function (): void {
    try {
        $binary = UnicodePdf::loadView('pdf.quote-comparison', [
            'title' => 'তুলনা',
            'results' => ['quotes' => []],
        ])->preset('bengali')->output();

        expect(strlen($binary))->toBeGreaterThan(100);
    } catch (PdfGenerationException $exception) {
        test()->markTestSkipped('PDF engine unavailable: '.$exception->getMessage());
    }
});

it('renders arabic quote comparison blade', function (): void {
    $html = view('pdf.quote-comparison', [
        'title' => 'مقارنة العروض',
        'results' => ['quotes' => []],
    ])->render();

    expect($html)->toContain('مقارنة العروض');
});

it('generates or skips unicode pdf for arabic content', function (): void {
    try {
        $binary = UnicodePdf::loadView('pdf.quote-comparison', [
            'title' => 'مقارنة العروض',
            'results' => ['quotes' => []],
        ])->preset('arabic')->output();

        expect(strlen($binary))->toBeGreaterThan(100);
    } catch (PdfGenerationException $exception) {
        test()->markTestSkipped('PDF engine unavailable: '.$exception->getMessage());
    }
});
