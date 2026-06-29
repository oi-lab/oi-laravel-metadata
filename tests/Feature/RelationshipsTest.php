<?php

use Illuminate\Database\QueryException;
use OiLab\OiLaravelMetadata\Models\Metadata;
use OiLab\OiLaravelMetadata\Models\OpenGraph;
use OiLab\OiLaravelMetadata\Tests\Fixtures\Page;

it('exposes a single metadata morphOne relation', function () {
    $page = Page::create(['name' => 'Home']);

    $page->metadata()->create(['title' => 'Home title']);

    expect($page->refresh()->metadata)->toBeInstanceOf(Metadata::class)
        ->and($page->metadata->title)->toBe('Home title')
        ->and($page->metadata->metable)->toBeInstanceOf(Page::class);
});

it('exposes a single openGraph morphOne relation', function () {
    $page = Page::create(['name' => 'Home']);

    $page->openGraph()->create(['type' => 'website']);

    expect($page->refresh()->openGraph)->toBeInstanceOf(OpenGraph::class)
        ->and($page->openGraph->type)->toBe('website');
});

it('forbids a second metadata record for the same parent', function () {
    $page = Page::create(['name' => 'Home']);

    $page->metadata()->create(['title' => 'First']);
    $page->metadata()->create(['title' => 'Second']);
})->throws(QueryException::class);

it('forbids a second open graph record for the same parent', function () {
    $page = Page::create(['name' => 'Home']);

    $page->openGraph()->create(['type' => 'website']);
    $page->openGraph()->create(['type' => 'article']);
})->throws(QueryException::class);

it('builds records through the factories', function () {
    $page = Page::create(['name' => 'Home']);

    $metadata = Metadata::factory()->forMetable($page)->create();
    $openGraph = OpenGraph::factory()->forMetable($page)->create();

    expect($metadata->metable->is($page))->toBeTrue()
        ->and($metadata->keywords)->toBeArray()
        ->and($openGraph->image)->toHaveKeys(['url', 'width', 'height']);
});
