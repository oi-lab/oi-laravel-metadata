<?php

use Illuminate\Support\Facades\Validator;
use OiLab\OiLaravelMetadata\Rules\IsoLanguageRule;
use OiLab\OiLaravelMetadata\Rules\RobotsRule;

function passesRule(object $rule, mixed $value): bool
{
    return Validator::make(['field' => $value], ['field' => $rule])->passes();
}

it('accepts valid ISO language codes', function (string $value) {
    expect(passesRule(new IsoLanguageRule, $value))->toBeTrue();
})->with(['fr', 'en', 'es', 'fr-FR', 'pt_BR']);

it('rejects invalid ISO language codes', function (string $value) {
    expect(passesRule(new IsoLanguageRule, $value))->toBeFalse();
})->with(['french', 'F', 'fr-france', '123', 'fr-']);

it('accepts valid robots directives', function (string $value) {
    expect(passesRule(new RobotsRule, $value))->toBeTrue();
})->with([
    'index, follow',
    'noindex, nofollow',
    'all',
    'none',
    'max-snippet:-1, max-image-preview:large',
]);

it('rejects invalid robots directives', function (string $value) {
    expect(passesRule(new RobotsRule, $value))->toBeFalse();
})->with([
    'index, banana',
    'crawl',
    'follow, nope',
]);
