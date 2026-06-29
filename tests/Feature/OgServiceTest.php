<?php

use OiLab\OiLaravelMetadata\Data\OpenGraphData;
use OiLab\OiLaravelMetadata\Data\OpenGraphImageData;
use OiLab\OiLaravelMetadata\Facades\Og;
use OiLab\OiLaravelMetadata\Tests\Fixtures\Page;
use OiLab\OiLaravelMetadata\Tests\Fixtures\Setting;

it('creates then updates a single open graph record via the service', function () {
    $page = Page::create(['name' => 'Home']);

    Og::update($page, new OpenGraphData(type: 'website', title: 'First'));
    Og::update($page, new OpenGraphData(type: 'article', title: 'Second'));

    expect($page->openGraph()->count())->toBe(1)
        ->and($page->refresh()->openGraph->type)->toBe('article')
        ->and($page->openGraph->title)->toBe('Second');
});

it('persists and rehydrates the nested image object', function () {
    $page = Page::create(['name' => 'Home']);

    Og::update($page, new OpenGraphData(
        type: 'website',
        image: new OpenGraphImageData(url: 'https://example.com/og.png', width: 1200, height: 630),
    ));

    $data = Og::toData($page);

    expect($data->image)->toBeInstanceOf(OpenGraphImageData::class)
        ->and($data->image->url)->toBe('https://example.com/og.png')
        ->and($data->image->width)->toBe(1200);
});

it('renders open graph tags including image dimensions', function () {
    $page = Page::create(['name' => 'Home']);
    Og::update($page, new OpenGraphData(
        type: 'website',
        title: 'Home',
        url: 'https://example.com',
        image: new OpenGraphImageData(url: 'https://example.com/og.png', width: 1200, height: 630),
    ));

    $html = Og::render($page)->toHtml();

    expect($html)
        ->toContain('<meta property="og:type" content="website">')
        ->toContain('<meta property="og:title" content="Home">')
        ->toContain('<meta property="og:url" content="https://example.com">')
        ->toContain('<meta property="og:image" content="https://example.com/og.png">')
        ->toContain('<meta property="og:image:width" content="1200">')
        ->toContain('<meta property="og:image:height" content="630">');
});

it('pulls site-wide open graph values from the Setting model', function () {
    Setting::create(['key' => 'METADATA_OG_LOCALE', 'value' => 'en']);
    Setting::create(['key' => 'METADATA_OG_SITE_NAME', 'value' => 'Acme']);
    Setting::create(['key' => 'METADATA_FACEBOOK_APP_ID', 'value' => '123456']);

    $page = Page::create(['name' => 'Home']);
    Og::update($page, new OpenGraphData(title: 'Home'));

    $html = Og::render($page)->toHtml();

    expect($html)
        ->toContain('<meta property="og:locale" content="en">')
        ->toContain('<meta property="og:site_name" content="Acme">')
        ->toContain('<meta property="fb:app_id" content="123456">');
});
