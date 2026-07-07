<?php

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use OiLab\OiLaravelMetadata\Data\MetadataData;
use OiLab\OiLaravelMetadata\Data\OpenGraphData;
use OiLab\OiLaravelMetadata\Facades\Meta;
use OiLab\OiLaravelMetadata\Facades\Og;
use OiLab\OiLaravelMetadata\Facades\Seo;
use OiLab\OiLaravelMetadata\Support\Schema;
use OiLab\OiLaravelMetadata\Tests\Fixtures\Page;

afterEach(function () {
    Seo::forget();
});

it('renders a passed model through the @meta and @og directives', function () {
    $page = Page::create(['name' => 'Home']);
    Meta::update($page, new MetadataData(description: 'Passed meta'));
    Og::update($page, new OpenGraphData(title: 'Passed og'));

    expect(Blade::render('@meta($page)', ['page' => $page]))
        ->toContain('<meta name="description" content="Passed meta">');

    expect(Blade::render('@og($page)', ['page' => $page]))
        ->toContain('<meta property="og:title" content="Passed og">');
});

it('renders the shared subject when the directives get no argument', function () {
    $page = Page::create(['name' => 'Home']);
    Meta::update($page, new MetadataData(description: 'Shared meta'));
    Og::update($page, new OpenGraphData(title: 'Shared og'));
    $page->syncJsonLd(Schema::webPage()->name('Shared json'));

    Seo::for($page);

    expect(Blade::render('@meta'))->toContain('<meta name="description" content="Shared meta">');
    expect(Blade::render('@og'))->toContain('<meta property="og:title" content="Shared og">');
    expect(Blade::render('@jsonLd'))->toContain('"name":"Shared json"');
});

it('lets an explicit directive argument override the shared subject', function () {
    $shared = Page::create(['name' => 'Shared']);
    $other = Page::create(['name' => 'Other']);
    Meta::update($shared, new MetadataData(description: 'Shared meta'));
    Meta::update($other, new MetadataData(description: 'Other meta'));

    Seo::for($shared);

    expect(Blade::render('@meta($other)', ['other' => $other]))
        ->toContain('Other meta')
        ->not->toContain('Shared meta');
});

it('renders only site-wide defaults when no subject is set', function () {
    $html = Blade::render('@og');

    expect($html)
        ->toContain('<meta property="og:type" content="website">')
        ->not->toContain('og:title');
});

it('auto-resolves the subject from the route model binding', function () {
    $page = Page::create(['name' => 'Home']);
    Meta::update($page, new MetadataData(description: 'Route meta'));
    Og::update($page, new OpenGraphData(title: 'Route og'));
    $page->syncJsonLd(Schema::webPage()->name('Route json'));

    Route::middleware(SubstituteBindings::class)->get('/pages/{page}', function (Page $page) {
        return Blade::render('<head>@meta @og @jsonLd</head>');
    });

    $this->get("/pages/{$page->id}")
        ->assertOk()
        ->assertSee('<meta name="description" content="Route meta">', false)
        ->assertSee('<meta property="og:title" content="Route og">', false)
        ->assertSee('"name":"Route json"', false);
});

it('does not auto-resolve when disabled in config', function () {
    config()->set('oi-laravel-metadata.auto_resolve_subject', false);

    $page = Page::create(['name' => 'Home']);
    Meta::update($page, new MetadataData(description: 'Route meta'));

    Route::middleware(SubstituteBindings::class)->get('/pages/{page}', function (Page $page) {
        return Blade::render('@meta');
    });

    $this->get("/pages/{$page->id}")
        ->assertOk()
        ->assertDontSee('Route meta');
});
