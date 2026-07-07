<?php

use Illuminate\Support\Facades\Blade;
use OiLab\OiLaravelMetadata\Data\JsonLdData;
use OiLab\OiLaravelMetadata\Facades\JsonLd;
use OiLab\OiLaravelMetadata\Support\Schema;
use OiLab\OiLaravelMetadata\Tests\Fixtures\Page;

it('creates then updates a single json-ld record via the service', function () {
    $page = Page::create(['name' => 'Home']);

    JsonLd::update($page, Schema::webPage()->name('First'));
    JsonLd::update($page, Schema::webPage()->name('Second'));

    expect($page->jsonLd()->count())->toBe(1)
        ->and($page->refresh()->jsonLd->graphs)->toBe([
            ['@type' => 'WebPage', 'name' => 'Second'],
        ]);
});

it('stores several graphs from a JsonLdData object', function () {
    $page = Page::create(['name' => 'Home']);

    JsonLd::update($page, JsonLdData::make(
        Schema::article()->headline('Post'),
        Schema::breadcrumbList()->itemListElement([]),
    ));

    expect($page->refresh()->jsonLd->graphs)->toHaveCount(2)
        ->and($page->jsonLd->graphs[0]['@type'])->toBe('Article')
        ->and($page->jsonLd->graphs[1]['@type'])->toBe('BreadcrumbList');
});

it('renders one script block per graph with an injected @context', function () {
    $page = Page::create(['name' => 'Home']);

    JsonLd::update($page, JsonLdData::make(
        Schema::article()->headline('Post'),
        Schema::organization()->name('Acme'),
    ));

    $html = JsonLd::render($page)->toHtml();

    expect(substr_count($html, '<script type="application/ld+json">'))->toBe(2)
        ->and($html)->toContain('"@context":"https://schema.org"')
        ->and($html)->toContain('"@type":"Article"')
        ->and($html)->toContain('"headline":"Post"')
        ->and($html)->toContain('"@type":"Organization"');
});

it('does not double-inject a @context that is already present', function () {
    $html = JsonLd::render(Schema::webPage()->set('@context', 'https://schema.org/docs')->name('X'))->toHtml();

    expect(substr_count($html, '@context'))->toBe(1)
        ->and($html)->toContain('https://schema.org/docs');
});

it('escapes angle brackets to keep the script tag safe', function () {
    $html = JsonLd::render(Schema::webPage()->description('</script><script>alert(1)</script>'))->toHtml();

    expect($html)->not->toContain('</script><script>alert(1)')
        ->and($html)->toContain('<');
});

it('renders directly from a Schema builder without a model', function () {
    $html = JsonLd::render(Schema::person()->name('Jane'))->toHtml();

    expect($html)
        ->toContain('<script type="application/ld+json">')
        ->toContain('"@type":"Person"')
        ->toContain('"name":"Jane"');
});

it('renders a raw associative array as a single graph', function () {
    $html = JsonLd::render(['@type' => 'WebSite', 'name' => 'Acme'])->toHtml();

    expect(substr_count($html, '<script'))->toBe(1)
        ->and($html)->toContain('"@type":"WebSite"');
});

it('renders a list of raw arrays as multiple graphs', function () {
    $html = JsonLd::render([
        ['@type' => 'WebSite', 'name' => 'Acme'],
        ['@type' => 'Organization', 'name' => 'Acme Inc'],
    ])->toHtml();

    expect(substr_count($html, '<script'))->toBe(2);
});

it('renders nothing for an empty or null source', function () {
    expect(JsonLd::render(null)->toHtml())->toBe('')
        ->and(JsonLd::render([])->toHtml())->toBe('');
});

it('returns an empty JsonLdData for a model with no record', function () {
    $page = Page::create(['name' => 'Home']);

    expect(JsonLd::toData($page)->graphs)->toBe([]);
});

it('renders through the @jsonLd blade directive', function () {
    $page = Page::create(['name' => 'Home']);
    JsonLd::update($page, Schema::webPage()->name('Directive'));

    $html = Blade::render('@jsonLd($page)', ['page' => $page]);

    expect($html)
        ->toContain('<script type="application/ld+json">')
        ->toContain('"name":"Directive"');
});

it('renders an ad-hoc schema through the @jsonLd blade directive', function () {
    $html = Blade::render(
        '@jsonLd($schema)',
        ['schema' => Schema::faqPage()->name('Help')],
    );

    expect($html)->toContain('"@type":"FAQPage"');
});

it('renders the trait helper off the model instance', function () {
    $page = Page::create(['name' => 'Home']);
    $page->syncJsonLd(Schema::webPage()->name('Trait'));

    expect($page->renderJsonLd())->toContain('"name":"Trait"');
});
