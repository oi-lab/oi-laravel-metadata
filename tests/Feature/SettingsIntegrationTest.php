<?php

use OiLab\OiLaravelMetadata\Contracts\SettingStore;
use OiLab\OiLaravelMetadata\Support\SettingResolver;
use OiLab\OiLaravelMetadata\Support\SettingsInstaller;
use OiLab\OiLaravelMetadata\Tests\Fixtures\Setting;

it('seeds the default metadata settings into the Setting model', function () {
    $created = app(SettingsInstaller::class)->install();

    expect($created)->toContain('METADATA_ROBOTS')
        ->and(Setting::where('key', 'METADATA_ROBOTS')->value('value'))->toBe('index, follow')
        ->and(Setting::where('key', 'METADATA_OG_TYPE')->value('value'))->toBe('website')
        ->and(Setting::where('key', 'METADATA_OG_LOCALE')->value('value'))->toBe('fr')
        ->and(Setting::count())->toBe(8);
});

it('is idempotent and never overwrites existing settings', function () {
    Setting::create(['key' => 'METADATA_ROBOTS', 'value' => 'noindex, nofollow']);

    $first = app(SettingsInstaller::class)->install();
    $second = app(SettingsInstaller::class)->install();

    expect($first)->not->toContain('METADATA_ROBOTS')
        ->and($second)->toBe([])
        ->and(Setting::where('key', 'METADATA_ROBOTS')->value('value'))->toBe('noindex, nofollow')
        ->and(Setting::count())->toBe(8);
});

it('runs the install command', function () {
    $this->artisan('metadata:install-settings')
        ->assertSuccessful();

    expect(Setting::count())->toBe(8);
});

it('resolves values from the Setting model when present', function () {
    Setting::create(['key' => 'METADATA_OG_SITE_NAME', 'value' => 'Acme']);

    expect(app(SettingResolver::class)->get('METADATA_OG_SITE_NAME'))->toBe('Acme');
});

it('falls back to config defaults when no Setting model is configured', function () {
    config()->set('oi-laravel-metadata.settings.model', 'App\\Models\\NonExistentSetting');

    $resolver = new SettingResolver;

    expect($resolver->isAvailable())->toBeFalse()
        ->and($resolver->get('METADATA_ROBOTS'))->toBe('index, follow')
        ->and($resolver->get('METADATA_OG_TYPE'))->toBe('website');
});

it('reports it cannot install without a Setting model', function () {
    config()->set('oi-laravel-metadata.settings.model', 'App\\Models\\NonExistentSetting');

    $installer = new SettingsInstaller;

    expect($installer->canInstall())->toBeFalse()
        ->and($installer->install())->toBe([]);
});

it('routes reads and writes through an explicitly configured store', function () {
    $store = new class implements SettingStore
    {
        /** @var array<string, string> */
        public array $values = [];

        public function isAvailable(): bool
        {
            return true;
        }

        public function get(string $key, ?string $default = null): ?string
        {
            return $this->values[$key] ?? $default;
        }

        public function has(string $key): bool
        {
            return array_key_exists($key, $this->values);
        }

        public function set(string $key, string $value): void
        {
            $this->values[$key] = $value;
        }
    };

    app()->instance('custom.store', $store);
    config()->set('oi-laravel-metadata.settings.store', 'custom.store');

    $created = app(SettingsInstaller::class)->install();

    expect($created)->toContain('METADATA_ROBOTS')
        ->and($store->values['METADATA_ROBOTS'])->toBe('index, follow')
        ->and(app(SettingResolver::class)->get('METADATA_ROBOTS'))->toBe('index, follow')
        ->and(Setting::count())->toBe(0);
});
