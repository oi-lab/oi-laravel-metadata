<?php

use OiLab\OiLaravelMetadata\Support\Schema;

it('builds a typed node with dynamic property setters', function () {
    $schema = Schema::article()
        ->headline('Hello world')
        ->datePublished('2026-07-07');

    expect($schema->toArray())->toBe([
        '@type' => 'Article',
        'headline' => 'Hello world',
        'datePublished' => '2026-07-07',
    ]);
});

it('supports arbitrary types via type()', function () {
    expect(Schema::type('Recipe')->name('Tarte')->toArray())->toBe([
        '@type' => 'Recipe',
        'name' => 'Tarte',
    ]);
});

it('resolves nested schema nodes recursively', function () {
    $schema = Schema::article()
        ->headline('Hello')
        ->author(Schema::person()->name('Jane Doe'))
        ->publisher(Schema::organization()->name('Acme'));

    expect($schema->toArray())->toBe([
        '@type' => 'Article',
        'headline' => 'Hello',
        'author' => ['@type' => 'Person', 'name' => 'Jane Doe'],
        'publisher' => ['@type' => 'Organization', 'name' => 'Acme'],
    ]);
});

it('resolves arrays of nested nodes', function () {
    $schema = Schema::breadcrumbList()->itemListElement([
        Schema::listItem()->set('position', 1)->name('Home')->item('https://example.com'),
        Schema::listItem()->set('position', 2)->name('Blog')->item('https://example.com/blog'),
    ]);

    expect($schema->toArray())->toBe([
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://example.com'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => 'https://example.com/blog'],
        ],
    ]);
});

it('sets the node identifier via id()', function () {
    expect(Schema::organization()->id('https://example.com/#org')->toArray())->toBe([
        '@type' => 'Organization',
        '@id' => 'https://example.com/#org',
    ]);
});

it('removes a property when set to null', function () {
    $schema = Schema::article()->headline('Hello')->headline(null);

    expect($schema->toArray())->toBe(['@type' => 'Article']);
});
