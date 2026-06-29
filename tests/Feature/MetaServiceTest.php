<?php

use OiLab\OiLaravelMetadata\Data\MetadataData;
use OiLab\OiLaravelMetadata\Facades\Meta;
use OiLab\OiLaravelMetadata\Tests\Fixtures\Page;

it('creates then updates a single metadata record via the service', function () {
    $page = Page::create(['name' => 'Home']);

    Meta::update($page, new MetadataData(title: 'First', description: 'One'));
    Meta::update($page, new MetadataData(title: 'Second', description: 'Two', keywords: ['a', 'b']));

    expect($page->metadata()->count())->toBe(1)
        ->and($page->refresh()->metadata->title)->toBe('Second')
        ->and($page->metadata->keywords)->toBe(['a', 'b']);
});

it('hydrates a data object from a model', function () {
    $page = Page::create(['name' => 'Home']);
    Meta::update($page, new MetadataData(title: 'Hi', keywords: ['x']));

    $data = Meta::toData($page);

    expect($data)->toBeInstanceOf(MetadataData::class)
        ->and($data->title)->toBe('Hi')
        ->and($data->keywords)->toBe(['x']);
});

it('renders metadata as escaped meta tags', function () {
    $page = Page::create(['name' => 'Home']);
    Meta::update($page, new MetadataData(
        title: 'Home',
        description: 'A & B',
        keywords: ['seo', 'laravel'],
        author: 'OI Lab',
        language: 'fr',
        robots: 'index, follow',
    ));

    $html = Meta::render($page)->toHtml();

    expect($html)
        ->toContain('<meta name="description" content="A &amp; B">')
        ->toContain('<meta name="keywords" content="seo, laravel">')
        ->toContain('<meta name="author" content="OI Lab">')
        ->toContain('<meta name="language" content="fr">')
        ->toContain('<meta name="robots" content="index, follow">');
});

it('falls back to config and setting defaults when fields are empty', function () {
    $page = Page::create(['name' => 'Home']);
    Meta::update($page, new MetadataData(title: 'Home'));

    $html = Meta::render($page)->toHtml();

    expect($html)
        ->toContain('<meta name="language" content="fr">')
        ->toContain('<meta name="robots" content="index, follow">');
});
