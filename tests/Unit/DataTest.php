<?php

use OiLab\OiLaravelMetadata\Data\MetadataData;
use OiLab\OiLaravelMetadata\Data\OpenGraphData;
use OiLab\OiLaravelMetadata\Data\OpenGraphImageData;

it('builds metadata data from an array', function () {
    $data = MetadataData::from([
        'title' => 'Title',
        'description' => 'Desc',
        'keywords' => ['a', 'b'],
        'language' => 'fr',
    ]);

    expect($data->title)->toBe('Title')
        ->and($data->keywords)->toBe(['a', 'b'])
        ->and($data->author)->toBeNull();
});

it('builds open graph data with a nested image object from an array', function () {
    $data = OpenGraphData::from([
        'type' => 'website',
        'title' => 'Title',
        'url' => 'https://example.com',
        'image' => ['url' => 'https://example.com/og.png', 'width' => 1200, 'height' => 630],
    ]);

    expect($data->image)->toBeInstanceOf(OpenGraphImageData::class)
        ->and($data->image->url)->toBe('https://example.com/og.png')
        ->and($data->image->width)->toBe(1200)
        ->and($data->image->height)->toBe(630);
});

it('defaults the keywords list to an empty array', function () {
    $data = new MetadataData(title: 'Only title');

    expect($data->keywords)->toBe([]);
});
